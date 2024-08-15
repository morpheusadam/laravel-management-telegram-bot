@extends('layouts.docs')
@section('title',__('Installation'))
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <ul>
        <li><a href="#server-config">{{__("Server Configuration")}}</a></li>
        <li><a href="#installation-manual">{{__("Installation Manual")}}</a></li>
      </ul>

      <div class="section-header text-center">
        <h1 class="main-header">{{__("How to Install?")}}</h1>
      </div>
      <hr class="main-hr">

      <b id="server-config">{{__("Server Configuration")}} :</b><br><br>
      <ul>
        <li>
          PHP >= v8.0 <br> MySQL > v5.7 (MariaDB > 10.2)</li>
        <li>
          {{__("Required PHP Configuration")}} <br>
         <strong>curl</strong> {{__("Extension")}}, <br>
         <strong>fileinfo</strong> {{__("Extension")}}, <br>
         <strong>mbstring</strong> {{__("Extension")}}, <br>
         <strong>ZipArchive </strong> {{__("Extension")}},<br>
         <strong>set_time_out</strong> {{__("Support")}} , <br>
         <strong>symlink</strong> {{__("Support")}} , <br>
         <strong>safe_mode</strong> {{__("Off")}}, <br>
         <strong>open_base_dir</strong> {{__("No value")}} <br>
         <strong>mysqli </strong> {{__("Support")}}.</li>
        <li>{{__("Your domain need to have SSL enabled.")}}</li>
        <li>{{__("For better performance set the below configurations as long ad possible, so that your server can process script for long time if needed.")}} :  php <b>max_execution_time</b>, mysql <b>connect_timeout</b>, mysql <b>wait_timeout</b> , <b>max_allowed_packet</b> , <b>max_connections</b></li>

      </ul>
        <div class="alert alert-warning">
            [{!!__("We have auto update feature that have ability to update files from our cloud storage. Its recommended to make all the files and folders are writable(755 permission recommended) to avoid any future issue while updating.")!!}
        </div>
      <div class="alert alert-danger">
          [{!!__("Note: If you get `404 page not found or 500 internal error` or other server error then make sure :param1 is enabled in your virtual host and check if :param2 file is uploaded successfully in root or not",['param1'=>"<strong class='text-dark'><u>AllowOverride</u></strong>","param2"=>"<b class='text-dark'><u>.htaccess</u></b>"])!!}]
      </div>

      <br><br>
      <b id="installation-manual" class="">
       {{__("Installation Manual")}} :</b><br><br>
      <ol>
        <li>{{__("Download zipped package file")}}></li>
        <li>{{__("Upload the file to your server and extract")}}</li>
        <li>
            {{__("Make sure the required file permissions are set as shown in the installation page")}}
        </li>
        <li>
         {{__("Run the project url (using https://) via browser and you will find a interface to provide the installation settings")}} (<strong>https://yourdomain.com</strong>):
          <ul>
            <li>
              <strong>{{__("Hostname")}} </strong>: {{__("Database host name / IP (usually localhost)")}}</li>
            <li>
              <strong>{{__("Database Name")}}</strong> :  {{__("Create a mysql database on your host and write that name here")}}</li>
            <li>
              <strong>{{__("Database username")}}</strong>&nbsp; :  {{__("Username of the created database")}}</li>
            <li>
              <strong>{{__("Database password")}}</strong> :  {{__("Password of the created database")}}</li>
            <li>
              <strong>{{__("Admin Panel Login Email")}}</strong>:  {{__("This will be used to login as admin")}}</li>
            <li>
              <strong>{{__("Admin Panel Login Password")}}</strong> :  {{__("Password to log in as admin")}}</li>
          </ul>
        </li>
        <li>
          {{__("Click install button. If the button is disabled meaning you do not meet all the server requirements.")}}</li>
        <li>
          {{__("You are done. Log in with your admin username and password and start using.")}}</li>
        <li>{{__("If installation page appears again after you hit submit and processing done that means system could not delete public/install.txt and you have to delete that file manually.")}}</li>
      </ol>
      <img src="assets/docs/images/install.png" class="img-fluid"
      />
    </div>

    </section>
</div>
@endsection
