<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use JoeDixon\Translation\Drivers\Translation;
use JoeDixon\Translation\Http\Requests\LanguageRequest;
use JoeDixon\Translation\Http\Requests\TranslationRequest;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;
use ZipArchive;


class Multilanguage extends Home
{

	private $translation;
	public $defaultFiles=['auth.php','pagination.php','passwords.php','validation.php'];
	public $vendor_directories;
	public $exclude_groups = []; // agent users dont need this

	public function __construct(Translation $translation)
	{
	    $this->translation = $translation;
        $this->set_global_userdata(false,['Admin','Agent'],['Manager']);
        $this->vendor_directories = [
			resource_path('lang').DIRECTORY_SEPARATOR,
			resource_path('lang').DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'translation'.DIRECTORY_SEPARATOR,
		];
	}

	private function user_language_list($user_id,$is_key=true,$json_file=false)
	{
		$getAllLang = $this->translation->allLanguages();
		$defaultList = get_language_list();
		// set empty array to store user's languages with default
		$userLanguages = [];
		foreach ($getAllLang as $key => $value) {
			// check the user id in the language list, store if found
            $found = Auth::user()->user_type=='Admin' ? !str_contains($value,'-') : str_ends_with($value,'-'.$user_id);
			if($found) {
				$explode_lang = explode("-",$value);
				$lang_name = $explode_lang[0] ?? '';
				if($is_key) $userLanguages[$key] = $defaultList[$lang_name];
				else $userLanguages[$lang_name] = $defaultList[$lang_name];
			}
		}
		return $userLanguages;
	}

	public function index(Request $request)
	{
		$user_id = Auth::user()->id;
		$userLanguages = $this->user_language_list($user_id);
	    // send retrieved data to render
	    $data['user_id'] = $user_id;
	    $data['defaultList'] = get_language_list();
	    $data['userLanguages'] = $userLanguages;
	    $body = 'translation::languages.index';
	    return view($body, $data);
	}

	public function create()
	{
		// get predefined array list of languages with shortcode
		$preDefinedList = get_language_list();
	    return view('translation::languages.create', compact('preDefinedList'));
	}

	public function edit($language= null)
	{
		$directory = resource_path('lang').DIRECTORY_SEPARATOR;
		if(!File::exists($directory.$language)) {
		    return redirect()
		        ->route('languages.index')
		        ->with('error', __("Sorry, No data was found."));
		}
		$getAllLang = $this->translation->allLanguages();
		$selectedLang = $getAllLang[$language];

	    return view('translation::languages.create',compact('selectedLang'));
	}

	public function delete($language=null)
	{
		if($language=='en' || empty($language)) abort('403');
        if(Auth::user()->user_type!='Admin' && !str_ends_with($language,'-'.Auth::user()->id)) abort('403');

		// store the language name
		$locale_name = $language;
		// set json file path
		$json_file = resource_path('lang').DIRECTORY_SEPARATOR.$locale_name.'.json';

        foreach($this->vendor_directories as $directory){
			$directory = $directory.$locale_name;
            @file_delete_directory($directory);
		}
		@File::delete($json_file);

		return Response::json([
			'status'=>'1',
			'message' => __("Locale Deleted Successfully")
		]);


	}

	public function store(Request $request)
	{
		$rules =  ['locale' => 'required'];

		$validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
        	return redirect()
	        ->route('languages.index')
	        ->with('error', $validator->errors()->first());
        }

		$user_id = Auth::user()->id;
		$locale = Auth::user()->user_type=='Admin' ? $request->locale : $request->locale.'-'.$user_id;

		foreach($this->vendor_directories as $directory){
			// set default locale path to copy files to new directory
			$defaultDirPath = $directory.'en';
			// set new locale directory path
			$newDirPath = $directory.$locale;
			if(!File::exists($newDirPath)) File::makeDirectory($newDirPath, 0777, true);

			$newDirPath = $newDirPath.DIRECTORY_SEPARATOR;

	    	// if created locale exists, copy default directory file into new locale
	    	if(File::exists($newDirPath)) {
	    		File::copyDirectory($defaultDirPath,$newDirPath);
	            if(Auth::user()->user_type!='Admin')
	    		foreach ($this->exclude_groups as $exclude){
	                @File::delete($newDirPath.$exclude);
	            }
	    	}

		}
    	// compiling language as json
    	$this->compile_language($request->locale,false);

    	// store success message
		$message = __('translation::translation.language_added');

	    return redirect()
	        ->route('languages.index')
	        ->with('success', $message);
	}

	public function translation_index(Request $request, $language)
	{
		$user_id = Auth::user()->id;
		$language = Auth::user()->user_type=='Admin' ? $language : $language.'-'.$user_id;

	    if ($request->has('language') && $request->get('language') !== $language) {
	        return redirect()->route('languages.translations.index', ['language' => $request->get('language'), 'group' => $request->get('group'), 'filter' => $request->get('filter')]);
	    }

	    $languages = $this->translation->allLanguages();
	    if(Auth::user()->user_type=='Admin') $groups = $this->translation->getGroupsFor('en')->merge('single');
	    else $groups = $this->get_file_list(resource_path('lang').DIRECTORY_SEPARATOR.$language);

	    $translations = $this->translation->filterTranslationsFor($language, $request->get('filter'));
	    $userLanguages = $this->user_language_list($user_id,false);

	    $defaultList = get_language_list();

	    if ($request->has('group') && $request->get('group')) {
	        if ($request->get('group') === 'single') {
	            $translations = $translations->get('single');
	            $translations = new Collection(['single' => $translations]);
	        } else {
	            $translations = $translations->get('group')->filter(function ($values, $group) use ($request) {
	                return $group === $request->get('group');
	            });

	            $translations = new Collection(['group' => $translations]);
	        }
	    }

	    return view('translation::languages.translations.index', compact('language', 'languages', 'groups', 'translations','userLanguages','defaultList'));
	}

	public function create_translation(Request $request, $language)
	{
		$group = [];
		$group_list = $this->translation->allGroup($language);
		foreach($group_list as $value) {
			$group[$value] = $value;
		}
		$data['group_list'] = $group;
		$data['language'] = $language;
	    return view('translation::languages.translations.create', $data);
	}

	public function store_translation(TranslationRequest $request, $language)
	{
	    if ($request->filled('group')) {
	        $namespace = $request->has('namespace') && $request->get('namespace') ? "{$request->get('namespace')}::" : '';
	        $this->translation->addGroupTranslation($language, "{$namespace}{$request->get('group')}", $request->get('key'), $request->get('value') ?: '');
	    } else {
	        $this->translation->addSingleTranslation($language, 'single', $request->get('key'), $request->get('value') ?: '');
	    }

	    return redirect()
	        ->route('languages.translations.index', $language)
	        ->with('success', __('translation::translation.translation_added'));
	}

	public function update_translation(Request $request, $language)
	{
	    if (! Str::contains($request->get('group'), 'single')) {
	        $this->translation->addGroupTranslation($language, $request->get('group'), $request->get('key'), $request->get('value') ?: '');
	    } else {
	        $this->translation->addSingleTranslation($language, $request->get('group'), $request->get('key'), $request->get('value') ?: '');
	    }

	    return ['success' => true];
	}

	public function run_artisan($language='en')
	{
	    if(Auth::user()->user_type!='Admin') abort(403);

	    $output = new BufferedOutput;

		Artisan::call('translation:list-missing-translation-keys', array(), $output);
		return Redirect::route('languages.translations.index',$language);
	}

	public function compile_language($language=null,$return=true){
        $group = request()->group ?? 'custom-landing';
        if(empty($group)) $group = 'custom-landing';
	    $user_id = Auth::user()->id;
        $language_get = $language;
        $language = Auth::user()->user_type=='Admin' ? $language : $language.'-'.$user_id;
	    $dirPath = resource_path('lang').DIRECTORY_SEPARATOR.$language;
        $fileList = File::allFiles($dirPath);
        $arrayAll = [];
        $js_flowbuilder = '';

        foreach($fileList as $file) {
            App::setLocale($language);
        	$filePath = $file->getPathname();
        	$fileName = $file->getFilename();
        	if(in_array($fileName,$this->defaultFiles)) continue;

        	$fileNameOnly = trim($fileName,'.php');
        	$currentFile = trans($fileNameOnly);

            App::setLocale('en');
            $enCurrentFile = trans($fileNameOnly);

            foreach ($currentFile as $k=>$v){
                if(!isset($enCurrentFile[$k]) && isset($currentFile[$k])) unset($currentFile[$k]);
            }
            $arrayAll = array_merge($arrayAll,$currentFile);
        }
        file_put_contents(resource_path('lang').DIRECTORY_SEPARATOR.$language.'.json',json_encode($arrayAll,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));

        if($return){
        	return redirect()
	        ->route('languages.translations.index',['language'=>$language_get,'group'=>$group])
	        ->with('success', __('translation::translation.language_key_compiled'));
        }
    }

	public function create_new_group($locale,$group)
	{
		$locale = $locale;
		$group_name = $group;
		$group = strtolower(str_replace(' ','_',$group_name));

		$path = resource_path('lang').DIRECTORY_SEPARATOR.$locale;
		$group_path = $path.DIRECTORY_SEPARATOR.$group;
		if(File::exists($path)) {
			if(!File::exists($group_path)) {
				fopen($group_path.'.php','w');
				File::put($group_path.'.php',"<?php \r\nreturn [];");
			}
		}

		return Response::json([
			'id'=>$group,
			'text'=>$group_name
		]);

	}

	protected function get_file_list($dir=null){
        $filesInFolder = File::files($dir);
        $files = [];
        foreach($filesInFolder as $path) {
            $file = pathinfo($path);
            if($file['filename']!='docs') // disabled documentation (doc) translation temporarily
            $files[] = $file['filename'] ;
        }
        return $files;

    }

    public function download_languages($language=null)
    {
    	$user_id = Auth::user()->id;
    	$user_type = Auth::user()->user_type;
    	$language = $user_type == "Admin" ? $language.'.json': $language.'-'.$user_id.'.json';
    	$dirPath = resource_path('lang').DIRECTORY_SEPARATOR.$language;
    	return Response::download($dirPath);
    }

}
