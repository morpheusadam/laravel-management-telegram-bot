@extends('layouts.landing')
@section('title',$title)
@section('meta_title',$meta_title)
@section('meta_description',$meta_description)
@section('meta_keyword',$meta_keyword)
@section('meta_author',$meta_author)
@section('meta_image',$meta_image)
@section('meta_image_width',$meta_image_width)
@section('meta_image_height',$meta_image_height)
@section('content')

<!-- ====== Pricing Start ====== -->
<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
@include('landing.partials.show-pricing')
<!-- ====== Pricing End ====== -->

@endsection

