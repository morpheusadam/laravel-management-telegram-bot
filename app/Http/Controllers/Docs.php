<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Docs extends Home
{

    public function __construct()
    {

    }

    public function installation(){
        $data = app('App\Http\Controllers\Landing')->make_view_data();
        $data['body'] = 'docs.installation';
        return $this->docs_viewcontroller($data);
    }

    public function administration(){
        $data = app('App\Http\Controllers\Landing')->make_view_data();
        $data['body'] = 'docs.administration';
        return $this->docs_viewcontroller($data);
    }

    public function tools(){
        $data = app('App\Http\Controllers\Landing')->make_view_data();
        $data['body'] = 'docs.tools';
        return $this->docs_viewcontroller($data);
    }


}
