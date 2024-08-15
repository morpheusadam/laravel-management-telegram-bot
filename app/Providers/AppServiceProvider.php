<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Pagination\Paginator;

use JoeDixon\Translation\Scanner;
use JoeDixon\Translation\Drivers\Translation;
use App\Http\Package\LaravelTranslation\TranslationManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {   

        $this->app->singleton(Translation::class, function ($app) {
            return (new TranslationManager($app, $app['config']['translation'], $app->make(Scanner::class)))->resolve();
        });

        if(env('FORCE_HTTPS_URL')) URL::forceScheme('https');        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {    
        

        Paginator::useBootstrap();

        Queue::before(function (JobProcessing $event) {
        });

        Queue::after(function (JobProcessed $event) {
            $queue = $event->job->getQueue();
            $payload = $event->job->payload();
            $user_id = $payload['user_id'] ?? 0;
            if($queue=='list-user-send-email'){
                $queue_count = DB::table("jobs")->where(['user_id'=>$user_id,'queue'=>$queue])->count();
                if($queue_count==0)
                {
                    $insert_data = [
                        'title'=> __('Email sent to users'),
                        'description'=> __('Email sent to users successfully.'),
                        'created_at' => date("Y-m-d H:i:s"),
                        'user_id' =>$user_id,
                        'color_class' => 'bg-success',
                        'icon' => 'fas fa-paper-plane',
                        'published' => '1',
                        'linkable' => '0'
                    ];
                    DB::table("notifications")->insert($insert_data);
                }
            }

        });
    }
}
