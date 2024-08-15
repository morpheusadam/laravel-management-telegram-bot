<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class Agency extends Home
{

    public function __construct()
    {
        $this->set_global_userdata(true,['Admin','Agent'],['Manager']);
    }

    private function detailed_feature_elements()
    {
        return array(
            'header_image' => array(
                array(
                    'label'=>__( 'Banner Image 1'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'banner_image1',
                    'value'=> 'assets/landing/new/images/bannerimage1.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Banner Image 2'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'banner_image2',
                    'value'=> 'assets/landing/new/images/bannerimage2.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Banner Image 3'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'banner_image3',
                    'value'=> 'assets/landing/new/images/bannerimage3.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Banner Image 4'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'banner_image4',
                    'value'=> 'assets/landing/new/images/bannerimage4.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Banner Image 4'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'banner_image4',
                    'value'=> 'assets/landing/new/images/bannerimage4.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Feature Image'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'feature_image',
                    'value'=> 'assets/landing/new/images/device-feature.png',
                    'placeholder' => '',
                    'upload'=>true,
                ), 
                array(
                    'label'=>__('Feature 1'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'details_feature_1_img',
                    'value'=> 'assets/landing/new/images/details_feature_1_img.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__("Feature 2"),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'details_feature_2_img',
                    'value'=> 'assets/landing/new/images/details_feature_2_img.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),       
                array(
                    'label'=>__("Feature 3"),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'details_feature_3_img',
                    'value'=> 'assets/landing/new/images/details_feature_3_img.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__("Feature 4"),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'details_feature_4_img',
                    'value'=> 'assets/landing/new/images/details_feature_4_img.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__("Feature 5"),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'details_feature_5_img',
                    'value'=> 'assets/landing/new/images/details_feature_5_img.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__("Feature 6"),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'details_feature_6_img',
                    'value'=> 'assets/landing/new/images/details_feature_6_img.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'About Image 1'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'about_image1',
                    'value'=> 'assets/landing/new/images/about-frame.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'About Image 2'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'about_image2',
                    'value'=> 'assets/landing/new/images/about-screen.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Beautiful Design Image 1'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'design_image1',
                    'value'=> 'assets/landing/new/images/modern01.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Beautiful Design Image 2'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'design_image2',
                    'value'=> 'assets/landing/new/images/modern02.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Beautiful Design Image 3'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'design_image3',
                    'value'=> 'assets/landing/new/images/modern03.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'How it Work Image 1'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'how_it_work1',
                    'value'=> 'assets/landing/new/images/download_app.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'How it Work Image 2'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'how_it_work2',
                    'value'=> 'assets/landing/new/images/create_account.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'How it Work Image 3'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'how_it_work3',
                    'value'=> 'assets/landing/new/images/enjoy_app.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Screenshot Image 1'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'ui_image1',
                    'value'=> 'assets/landing/new/images/screen-1.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Screenshot Image 2'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'ui_image2',
                    'value'=> 'assets/landing/new/images/screen-2.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Screenshot Image 3'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'ui_image3',
                    'value'=> 'assets/landing/new/images/screen-3.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Screenshot Image 4'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'ui_image4',
                    'value'=> 'assets/landing/new/images/screen-4.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Screenshot Image 5'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'ui_image5',
                    'value'=> 'assets/landing/new/images/screen-5.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Screenshot Image 6'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'ui_image6',
                    'value'=> 'assets/landing/new/images/screen-6.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Start Using Image 1'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'start_using_image1',
                    'value'=> 'assets/landing/new/images/download-screen01.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),
                array(
                    'label'=>__( 'Start Using Image 2'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'start_using_image2',
                    'value'=> 'assets/landing/new/images/download-screen02.png',
                    'placeholder' => '',
                    'upload'=>true,
                ),                
                array(
                    'label'=>__( 'Watch Video'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'header_image',
                    'value'=> 'assets/landing/new/video/intro.mp4',
                    'placeholder' => '',
                    'upload'=>true,
                )
            )
        );
    }

    private function customer_reviews()
    {
        
        $reviews = [];
        for($i=1;$i<=10;$i++){
            $reviews["review_".$i]= [
                0=>array(
                    'label'=>__('Name'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'review_'.$i.'_name',
                    'value'=> '',
                    'placeholder' => '',
                ),
                1=>array(
                    'label'=>__('Designation'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'review_'.$i.'_designation',
                    'value'=> '',
                    'placeholder' => '',
                ),
                2=>array(
                    'label'=>__('Avatar'),
                    'field'=>'input',
                    'type'=>'text',
                    'name' => 'review_'.$i.'_avatar',
                    'value'=> '',
                    'placeholder' => '',
                    'upload'=>true,
                ),
               3=> array(
                    'label'=>__('Review'),
                    'field'=>'textarea',
                    'type'=>'text',
                    'name' => 'review_'.$i.'_description',
                    'value'=> '',
                    'placeholder' => '',
                )
            ];
        }

        return $reviews;
    }

    private function company_elements()
    {
        return array(
            array(
                'label'=>__('Company Email'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_email',
                'value'=> 'admin@'.get_domain_only(url('/')),
                'placeholder' => '',
            ),
            array(
                'label'=>__('Company Address'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_address',
                'value'=> 'Holding #, nth Floor, City, Country',
                'placeholder' => '',
            ),
            array(
                'label'=>__('SEO Meta Title'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_title',
                'value'=> "Telegram Group Management Bot",
                'placeholder' => '',
            ),
            array(
                'label'=>__('SEO Meta Description'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_short_description',
                'value'=> "As Telegram continues to grow in popularity, managing a vibrant and engaged community becomes both exciting and challenging for group administrators. The constant influx of messages, images, and media can quickly lead to clutter and, in some cases, unwelcome spam. Fortunately, there is a powerful solution at your disposal.",
                'placeholder' => '',
            ),
            array(
                'label'=>__('SEO Meta Image'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_cover_image',
                'value'=> asset('assets/landing/images/hero/hero-image-2.png'),
                'placeholder' => '',
                'upload'=>true,
            ),
            array(
                'label'=>__('SEO Meta Keywords'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_keywords',
                'value'=> 'telegram,group management,group bot,anti spam bot',
                'placeholder' => '',
            ),
            array(
                'label'=>__('Facebook Messenger URL'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_fb_messenger',
                'value'=> 'https://m.me/xxxxxx',
                'placeholder' => '',
            ),
            array(
                'label'=>__('Facebook Page URL'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_fb_page',
                'value'=> 'https://facebook.com/xxxxxx',
                'placeholder' => '',
            ),
            array(
                'label'=>__('Telegram Bot URL'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_telegram_bot',
                'value'=> 'https://t.me/xxxxxx_bot',
                'placeholder' => '',
            ),
            array(
                'label'=>__('Telegram Channel URL'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_telegram_channel',
                'value'=> 'https://t.me/xxxxxx',
                'placeholder' => '',
            ),
            array(
                'label'=>__('Youtube Channel URL'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_youtube_channel',
                'value'=> 'https://www.youtube.com/xxxxxx',
                'placeholder' => '',
            ),
            array(
                'label'=>__('Twitter Profile URL'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_twitter_account',
                'value'=> 'https://twitter.com/xxxxxx',
                'placeholder' => '',
            ),
            array(
                'label'=>__('Instagram Profile URL'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_instagram_account',
                'value'=> 'https://instagram.com/xxxxxx',
                'placeholder' => '',
            ),
            array(
                'label'=>__('Linkedin Profile URL'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_linkedin_channel',
                'value'=> 'https://linkedin.com/company/xxxxxx',
                'placeholder' => '',
            ),
            array(
                'label'=>__('Support Desk URL'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'company_support_url',
                'value'=> '',
                'placeholder' => '',
            ),
            array(
                'label'=>__('Documentation URL'),
                'field'=>'input',
                'type'=>'text',
                'name' => 'links_docs_url',
                'value'=> route('docs'),
                'placeholder' => '',
            )
        );
    }


    public function get_agency_landing_page_data()
    {
        $data['settings_data'] = array();
        $data['settings_data']['details_features'] = $this->detailed_feature_elements();
        $data['settings_data']['customer_reviews'] = $this->customer_reviews();
        $data['settings_data']['company_elements'] = $this->company_elements();

        $xdata = DB::table('settings')->select('agency_landing_settings')->where('user_id',$this->user_id)->first();
        $data['xdata'] = isset($xdata->agency_landing_settings) ? json_decode($xdata->agency_landing_settings) : null;
        $data['body'] = 'member.settings.agency-landing-settings';
        return $this->viewcontroller($data);
    }

    public function submit_agency_landing_form_data(Request $request)
    {
        if(config('app.is_demo')=='1') return \redirect(route('restricted-access'));
        
        $submitted_data =(object) $request->all();
        if(isset($submitted_data->_token)) unset($submitted_data->_token);
        if(!isset($submitted_data->disable_landing_page)) $submitted_data->disable_landing_page='0';
        if(!isset($submitted_data->disable_review_section)) $submitted_data->disable_review_section='0';
        if(!isset($submitted_data->enable_dark_mode)) $submitted_data->enable_dark_mode='0';

        $insert_data['agency_landing_settings'] = json_encode($submitted_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $update_data = $insert_data;
        $insert_data['user_id'] = $this->user_id;
        $insert_data['email_settings'] = '{"default":null,"sender_email":null,"sender_name":null}';
        $insert_data['auto_responder_signup_settings'] = '{"mailchimp":[],"sendinblue":[],"activecampaign":[],"mautic":[],"acelle":[]}';
        $insert_data['upload_settings'] = '{"bot":{"image":"1","video":"20","audio":"5","file":"20"}}';
        $insert_data['timezone'] = 'Europe/Dublin';
        $insert_data['updated_at'] = date('Y-m-d H:i:s');
        DB::table('settings')->upsert($insert_data,['user_id'],$update_data);
        return redirect()->route('agency-landing-editor')->with('status',__("Data has been updated successfully."));

    }

    public function reset_editor()
    {
        if(config('app.is_demo')=='1') return \redirect(route('restricted-access'));
        DB::table("settings")->where('user_id',$this->user_id)->update(["agency_landing_settings"=>'']);
        return redirect()->route('agency-landing-editor')->with('status',__("Reset successfully"));
    }

    public function upload_media(Request $request) {
        $rules = (['file' => 'mimes:png,jpg,jpeg,webp|max:2048']);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'error' => true,
                'message' => $validator->errors()->first(),
            ]);
        }

        $upload_dir_subpath = 'agency/'.$this->user_id;

        $file = $request->file('file');
        $extension = $request->file('file')->extension();
        $filename = time().'.'.$extension;

        if(env('AWS_UPLOAD_ENABLED')){
            try {
                $upload2S3 = Storage::disk('s3')->putFileAs($upload_dir_subpath, $file,$filename);
                return response()->json([
                    'error' => false,
                    'filename' =>  Storage::disk('s3')->url($upload2S3)
                ]);
            }
            catch (\Exception $e){
                $error_message = $e->getMessage();
                if(empty($error_message)) $error_message =  __('Something went wrong.');
                return response()->json([
                    'error' => true,
                    'message' => $error_message
                ]);
            }
        }
        else{

            if ($request->file('file')->storeAs('public/'.$upload_dir_subpath, $filename)) {
                return Response::json([
                    'error' => false,
                    'filename' =>  asset('storage').'/'.$upload_dir_subpath.'/'.$filename
                ]);
            } else {
                return Response::json([
                    'error' => true,
                    'message' => __('Something went wrong.'),
                ]);
            }
        }
    }
}
