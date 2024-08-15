<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{config('app.localeDirection')}}">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}} - Install</title>
    <link rel="shortcut icon" href="{{config('app.favicon')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/vertical-layout-light/style.css')}}">

  </head>

  <?php
    /*****Curl******/
    $mkdir  = $fileinfo = $curl=$mbstring=$safe_mode=$allow_url_fopen=$set_time_limit=$symlink=$ziparchive=$install_txt_display=$env_file_display=$resource_file_display =$http_file_display =$helpers_file_display = $middleware_file_display = $services_file_display = $config_file_display = $assets_file_display =$routes_file_display =$storage_file_display ="<li class='list-group-item list-group-item list-group-item-danger text-light'><i class='fa fa-times-circle'></i> <b>Failed : </b>Could not check.</li>";

    $mysql_support="";
    $install_allow = 1;

    if($install_txt_permission)
      $install_txt_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>public/install.txt : </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $install_txt_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>public/install.txt : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if($env_file_permission)
      $env_file_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>.env : </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $env_file_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>.env : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if($resource_file_permission)
      $resource_file_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>resources : </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $resource_file_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>resources : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if($helpers_file_permission)
      $helpers_file_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>app/Helpers : </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $helpers_file_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>app/Helpers : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if($http_file_permission)
      $http_file_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>app/Http : </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $http_file_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>app/Http : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if($middleware_file_display)
      $middleware_file_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>app/Http/Middleware : </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $middleware_file_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>app/Http/Middleware : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if($services_file_permission)
      $services_file_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>app/Services + app/Providers: </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $services_file_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>app/Services and app/Providers : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if($config_file_permission)
      $config_file_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>config : </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $config_file_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>config : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if($assets_file_permission)
      $assets_file_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>assets : </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $assets_file_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>assets : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if($routes_file_permission)
      $routes_file_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>routes : </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $routes_file_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>routes : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if($storage_file_permission)
      $storage_file_display="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>storage : </b>Writable</li>";
    else
    {
      $install_allow = 0;
      $storage_file_display="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>storage : </b>Not-writable, need write permission (755 permission recommended)</li>";
    }

    if(function_exists('curl_version'))
    $curl="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>curl : </b>Enabled</li>";
    else
    {
      $install_allow = 0;
      $curl="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>curl : </b>Disabled, please enable curl extension</li>";
    }

    if(extension_loaded('fileinfo'))
    $fileinfo="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>fileinfo : </b>Enabled</li>";
    else
    {
      $install_allow = 0;
      $fileinfo="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>fileinfo : </b>Disabled, please enable fileinfo extension</li>";
    }

    if(function_exists( "mb_detect_encoding" ) )
    $mbstring="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>mbstring : </b>Enabled</li>";
    else
    {
      $install_allow = 0;
      $mbstring="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>mbstring : </b>Disabled, please enable mbstring extension</li>";
    }


    if(function_exists('ini_get'))
    {
      if( ini_get('safe_mode') )
      {
        $install_allow = 0;
        $safe_mode="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>safe_mode : </b>ON, please set safe_mode=off</li>";
      }
      else
      $safe_mode="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>safe_mode : </b>OFF</li>";


      if(ini_get('allow_url_fopen'))
      $allow_url_fopen="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>allow url open : </b>TRUE</li>";
      else
      {
        $install_allow = 0;
        $allow_url_fopen="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>allow url open : </b>FALSE, please make allow_url_fopen=1 in php.ini</li>";
      }

    }

    if(function_exists('mysqli_connect'))
    $mysql_support="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>mysqli : </b>Supported</li>";
    else
    {
      $install_allow = 0;
      $mysql_support="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>mysqli : </b>Unsupported, please enable mysqli extension</li>";
    }

    if(function_exists('set_time_limit'))
    $set_time_limit="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>set_time_limit() : </b>Supported</li>";
    else
    {
      $install_allow = 0;
      $set_time_limit="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>set_time_limit() : </b>Disabled, please enable set_time_limit() function</li>";
    }

    if(function_exists('symlink'))
    $symlink="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>symlink() : </b>Supported</li>";
    else
    {
      $install_allow = 0;
      $symlink="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>set_time_limit() : </b>Disabled, please enable symlink() function</li>";
    }

    if(function_exists('mkdir'))
    $mkdir="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>mkdir() : </b>Supported</li>";
    else
    {
      $install_allow = 0;
      $mkdir="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>mkdir() : </b>Disabled, please enable mkdir() function</li>";
    }

    if(class_exists('ZipArchive'))
    $ziparchive="<li class='list-group-item text-success'><i class='fa fa-check-circle'></i> <b>ziparchive : </b>Supported</li>";
    else
    {
      $install_allow = 0;
      $ziparchive="<li class='list-group-item list-group-item list-group-item-warning'><i class='fa fa-times-circle'></i> <b>ziparchive : </b>Disabled, please enable ziparchive extension</li>";
    }



  ?>

  <body class="{{config('app.localeDirection')}}">
    <div class="container-fluid">
      <div class="row">
          <div class="col-12">
              <a class="text-center" href="{{ url('/') }}"><img  src="{{config('app.logo')}}" class="mt-4" width="200" alt="<?php echo config('product_name');?>"></a><br><br>
           </div>

          <div class="col-12">
            <?php
            if(Session::get('mysql_error')!="")
              {
                echo "<pre class='mt-0 mb-0 ml-auto mr-auto text-danger text-center'><h6 class='text-danger'>";
                echo Session::get('mysql_error');
                Session::forget('mysql_error');
                echo "</h6></pre><br/>";
              }
            ?>

          </div>

          <div class="col-12 col-lg-6">
            <div class="card card-primary">
             <div class="card-header bg-white"><h4 class="mt-2"><i class="far fa-check-circle"></i> Installation </h4></div>

              @if ($errors->any())
                  <div class="alert alert-warning">
                      <h4 class="alert-heading">Something Missing</h4>
                      <p>Something is missing. Please check the required inputs.</p>
                  </div>
              @endif
              <div class="card-body" id="recovery_form">
                <form class="form-horizontal" action="{{ route('installation-submit') }}" method="POST">
                      @csrf
                      <div class="row">
                        <div class="form-group col-12 col-lg-6">
                           <div class="input-row">
                              <label><b>Host Name *</b></label>
                              <input type="text" value="{{ old('host_name') }}" name="host_name" class="form-control col-xs-12"  placeholder="localhost">
                           </div>
                            @if ($errors->has('host_name'))
                              <span class="text-danger"> {{ $errors->first('host_name') }} </span>
                            @endif
                        </div>
                        <div class="form-group col-12 col-lg-6">
                          <div class="input-row">
                             <label><b>Database Name *</b></label>
                             <input type="text" value="<?php echo old('database_name'); ?>" name="database_name" class="form-control col-xs-12"  placeholder="Database Name *">
                          </div>
                            @if ($errors->has('database_name'))
                                <span class="text-danger"> {{ $errors->first('database_name') }} </span>
                            @endif
                        </div>
                      </div>


                      <div class="row">
                        <div class="form-group col-12 col-lg-6">
                         <div class="input-row">

                             <label><b>Database Username *</b></label>
                             <input type="text" value="<?php echo old('database_username'); ?>" name="database_username" class="form-control col-xs-12"  placeholder="Database Username *">

                          </div>
                            @if ($errors->has('database_username'))
                                  <span class="text-danger"> {{ $errors->first('database_username') }} </span>
                            @endif
                        </div>

                        <div class="form-group col-12 col-lg-6">
                           <div class="input-row">
                             <label><b>Database Password *</b> </label>

                             <input type="password" name="database_password"  class="form-control col-xs-12"  placeholder="Database Password ">

                           </div>
                            @if ($errors->has('database_password'))
                                <span class="text-danger"> {{ $errors->first('database_password') }} </span>
                            @endif
                        </div>
                      </div>

                       <div class="row">
                          <div class="form-group col-12">
                            <div class="input-row">

                              <label><?php echo config('settings.product_name') ?> <b>Admin Login Email *</b></label>
                              <input type="email" value="<?php echo old('app_username'); ?>" name="app_username" class="form-control col-xs-12"  placeholder="">
                            </div>
                             @if ($errors->has('app_username'))
                                <span class="text-danger"> {{ $errors->first('app_username') }} </span>
                            @endif
                         </div>
                         <div class="form-group col-12">
                            <div class="input-row">
                              <label><?php echo config('settings.product_name') ?> <b>Admin Login Password *</b></label>
                              <input type="password" name="app_password" class="form-control col-xs-12"  placeholder="">
                            </div>
                            @if ($errors->has('app_password'))
                                <span class="text-danger"> {{ $errors->first('app_password') }} </span>
                            @endif
                         </div>
                       </div>


                      <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg btn-block mt-4" <?php if($install_allow == 0) echo "disabled";?> ><i class="fa fa-check"></i>Install <?php echo config('settings.product_name');?> Now</button><br/><br/>
                      </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="card card-primary">
              <div class="card-header bg-white"><h4 class="mt-2"><i class="fas fa-server"></i> Server Requirements</h4></div>

              <div class="card-body">
                <p class="text-muted" id="msg">
                  <?php if($install_allow==1) :?>
                    <div class="alert alert-success text-center"><b><i class="fa fa-check-circle"></i> Congratulation ! Your server is fully configured to install this application. <br><br><span class="fw-bold text-dark">We have auto update feature that have ability to update files from our cloud storage. Its recommended to make all the files and folders are writable(755 permission recommended) to avoid any future issue while updating.</span></b></div>
                  <?php else : ?>
                    <div class="alert alert-warning text-center"><b><i class="fa fa-warning"></i>Warning ! Please fullfill the below requirements (yellow) first. </b></div>
                  <?php endif; ?>
                </p>

                <div class="row">
                  <div class="col-12 col-lg-6">
                    <h6>File Permission</h6>
                    <ul class="list-group">
                      <?php
                        echo $env_file_display;
                        echo $install_txt_display;
                        echo $http_file_display;
                        echo $helpers_file_display;
                        echo $services_file_display;
                        echo $assets_file_display;
                        echo $config_file_display;
                        echo $resource_file_display;
                        echo $routes_file_display;
                        echo $storage_file_display;
                      ?>
                    </ul>
                  </div>
                  <div class="col-12 col-lg-6">
                    <h6 class="">Server Environment</h6>
                    <ul class="list-group">
                      <?php
                        echo $curl;
                        echo $fileinfo;
                        echo $mbstring;
                        echo $safe_mode;
                        echo $allow_url_fopen;
                        echo $mysql_support;
                        echo $ziparchive;
                        echo $set_time_limit;
                        echo $symlink;
                        echo $mkdir;
                      ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </body>

</html>
