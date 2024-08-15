<?php

namespace App\Http\Package\LaravelTranslation;

use JoeDixon\Translation\Console\Commands\ListMissingTranslationKeys;
use Illuminate\Support\Facades\File;

class MissingTranslations extends ListMissingTranslationKeys
{
    public $global_output = [];
    public function handle()
	{
     	$path_array = config('translation.scan_groups');
     	$output = [];
     	foreach($path_array as $group_name=>$groups){
     		$result = [];
     		foreach($groups as $key=>$single_file){
     			$path = $single_file['path'] ?? '';
     			$scan_mode = $single_file['scan_mode'] ?? '';
     			$type = $single_file['type'] ?? '';
     			$result = $this->generate($path,$result,$scan_mode,$type);
     		}
     		if(!empty($result)){
     			asort($result);
	     		$values = array_values($result);
	     		foreach($values as $value) {
	     			if(isset($output[$value]) || is_numeric($value)) unset($values[$value]);
	     		}
	     		$final = array_combine($values, $values);

		    	$this->write_file($final,$group_name);
		    	$output = array_merge($output,$final);
     		}
     	}
     	$result_missing = [];
     	$missingTranslations = $this->translation->findMissingTranslations('en');

     	foreach ($missingTranslations as $language => $types) {
            foreach ($types as $type => $keys) {
                foreach ($keys as $key => $value) {
                	if(!isset($output[$key]) && !is_numeric($key))
                	$result_missing[$key] = $key;
                }
            }
        }

        if(!empty($result_missing)){
        	asort($result_missing);
            $this->write_file($result_missing,'misc');
        }

        return true;
	}

	public function generate($path,$result,$scan_mode,$type)
	{
		if(empty($path)) return [];

	    $matchingPattern =
	        '[^\w]'. // Must not start with any alphanum or _
	        '(?<!->)'. // Must not start with ->
	        '('.implode('|', config('translation.translation_methods')).')'. // Must start with one of the functions
	        "\(". // Match opening parentheses
	        "[\'\"]". // Match " or '
	        '('. // Start a new group to match:
	        '.+'. // Must start with group
	        ')'. // Close group
	        "[\'\"]". // Closing quote
	        "[\),]";  // Close parentheses or new parameter

		$file_list = [];
		if($scan_mode=='file'){
			array_push($file_list,$path);
		}
		else if($scan_mode=='directory'){
			$scanDir = \File::allFiles($path);
			foreach($scanDir as $file) {
	    		array_push($file_list,$file->getpathName());
	    	}
		}
        else if($scan_mode=='copyFile'){
            if($type=='flowbuilder'){
                $content = file_get_contents($path);
                $search = ['var ',' = ',';'];
                $replace = ["'","'=>",','];
                $content = str_replace($search,$replace,$content);
                $str = "<?php return array(\r\n".$content."\n);";
                $fileName = resource_path('lang').DIRECTORY_SEPARATOR.'en'.DIRECTORY_SEPARATOR.$type.'.php';
                if(!File::exists($fileName)) {
                    fopen($fileName,'w');
                }
                File::put($fileName,$str);
            }
        }

        if(!empty($file_list))
    	foreach($file_list as $file) {
    		$getFileContents = file_get_contents($file);
    		if(preg_match_all("/$matchingPattern/siU", $getFileContents,$matches)) {
    			foreach($matches[2] as $match) {
    			    if(!in_array($match,$this->global_output))
                    {
                        array_push($result,$match);
                        array_push($this->global_output,$match);
                    }
    			}
    		}
    	}
    	return !empty($result) ? array_unique($result) : [];
	}

	protected function write_file($final=[],$group_name = '',$lang='en'){
	    if(empty($final) || empty($group_name)) return false;
        $str = "<?php  \r\nreturn ".var_export($final, true).';';
        $fileName = resource_path('lang').DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.$group_name.'.php';
        if(!File::exists($fileName)) {
            fopen($fileName,'w');
        }
        File::put($fileName,$str);
        return true;
    }
}
