<!DOCTYPE html>
<html lang="{{ get_current_lang() }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }} - @yield('title')</title>
        <link rel="shortcut icon" href="{{ config('app.favicon') }}" type="image/x-icon">

        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

        @if($load_datatable)
            <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/datatables.min.css') }}">
            <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/DataTables-1.10.25/css/dataTables.bootstrap5.min.css') }}">
            <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/ColReorder-1.5.4/css/colReorder.bootstrap5.min.css') }}">
            <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/Buttons-1.7.1/css/buttons.bootstrap5.min.css') }}">
            <link rel="stylesheet" type="text/css" href="{{asset('assets/cdn/css/daterangepicker.css')}}" />
        @endif

        <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/datetimepicker/jquery.datetimepicker.css') }}">
        <link rel="stylesheet" href="{{asset('assets/cdn/css/select2.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/cdn/css/sweetalert2.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/cdn/css/toastr.min.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/cdn/css/all.min.css')}}"/>

        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/OwlCarousel/dist/owl.carousel.min.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/OwlCarousel/dist/owl.theme.default.min.css') }}" />

        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/summernote/summernote-bs4.css') }}" />

        <link rel="stylesheet" href="{{ asset('assets/vendors/chocolat/css/chocolat.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/prism/prism.css') }}">

        <link rel="stylesheet" href="{{ asset('assets/css/component.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/inlinecss.css') }}">

        <script src="{{ asset('assets/cdn/js/jquery-3.6.0.min.js') }}"></script>
        <script src="{{ asset('assets/js/common/include_head.js') }}"></script>

        @stack('styles-header')
        @stack('scripts-header')

    </head>

    <body>

        <audio id="chatNotificationAudio" class="d-none"><source src="{{asset('assets/audio/whatsapp-notification-tone.mp3')}}" type="audio/mp3"></audio>
        <audio id="chatNotificationAudio2" class="d-none"><source src="{{asset('assets/audio/telegram-notification.mp3')}}" type="audio/mp3"></audio>

        @php
            $profile_pic  = !empty(Auth::user()->profile_pic) ? Auth::user()->profile_pic : asset('assets/images/avatar/avatar-1.png');
        @endphp

        <?php
            $pricing_link = $parent_user_id==1 ? env('APP_URL').'/pricing' : route('pricing-plan');
            if($is_agent && Auth()->user()->agent_has_ppu=='1') $pricing_link = route('pricing-plan-ppu');
        ?>

        <div id="app">
            <div id="sidebar" <?php if(!in_array($route_name,$full_width_page_routes)) echo "class='active'";?>>
                <div class="sidebar-wrapper active">
                    <div class="sidebar-header">
                        <a href="{{url('/')}}">
                            <img src="{{ config('app.logo') }}" alt="" class="large-logo">
                            <img src="{{ config('app.favicon') }}" alt="" class="small-logo">
                        </a>
                    </div>

                    <?php
                    $has_telegram_group_access = has_module_access($module_id_telegram_group,$user_module_ids,$is_admin,$is_manager);
                    $has_team_access = has_team_access($is_admin);
                   
                    $telegram_group_menu = [];
                    $admin_menus = [];

                    if($has_telegram_group_access){
                        $telegram_group_menu = ['selected' => ['telegram-group-manager'],'href' => route('telegram-group-manager'),'icon' => 'meeting.png','title' => __('Group Manager')
                        ];
                    }

                    if($is_admin){
                        $admin_menus =  [
                            0 => ['selected' => ['general-settings'], 'href' => route('general-settings'),'icon' =>  $is_admin || $is_agent ? 'settings-5.png' : 'intersection.png', 'title' => $is_admin || $is_agent ? __('Settings') : __('Integration')]
                        ];
                        
                        $package_language_display = __('Package Manager');
                        $user_language_display = __('User Manager');
                        $admin_menus[1] = ['selected' => ['list-package'], 'href' => route('list-package'),'icon' => 'id-card-1.png', 'title' => $package_language_display];
                        $admin_menus[2] = ['selected' => ['list-user'], 'href' => route('list-user'),'icon' => 'settings-4.png', 'title' => $user_language_display];
                        $admin_menus[3] = ['selected' => ['transaction-log'], 'href' => route('transaction-log'),'icon' => $is_member ? 'refresh.png' : 'financial-profit.png', 'title' => $is_member ? __('Transactions') : __('Earnings')];
                        $admin_menus[4] = ['selected' => ['update-list'], 'href' => route('update-list'),'icon' => 'update-3.png', 'title' => __("Update Center")];                
                    }
                    
                    $admin_menus[3] = ['selected' => ['transaction-log'], 'href' => route('transaction-log'),'icon' => $is_member ? 'refresh.png' : 'financial-profit.png', 'title' => $is_member ? __('Transactions') : __('Earnings')];
                    if($is_member){
                        $admin_menus[4] = ['selected' => ['pricing-plan'], 'href' => route('pricing-plan'),'icon' => 'credit-card.png', 'title' =>  __('Renew/Upgrade')];
                    }

                    $sidebar_menu_items['main'] = [
                        'sidebar-title' =>  __('Main Menu'),
                        'sidebar-items' =>  [
                            0 => [
                                'selected' => ['dashboard'],
                                'href' => route('dashboard'),
                                'icon' => 'business-report.png',
                                'title' => __('Dashboard')
                            ],
                            1 => [
                                'selected' => ['connect-bot'],
                                'href' => route('connect-bot'),
                                'icon' => 'telegram.png',
                                'title' => __('Connect Bot')
                            ],
                            2 => $telegram_group_menu                  
                        ],
                    ];

                    if(!empty($admin_menus)){
                        $sidebar_menu_items['admin'] = [
                            'sidebar-title' =>  $is_admin || $is_agent ? __('Administration') : __('Billing'),
                            'sidebar-items' => $admin_menus
                        ];
                    }
                    ?>

                    <div class="sidebar-menu" id="sidebar-menu">
                        <ul class="menu">
                            <div class="dropdown-divider m-0 pb-2"></div>

                            @foreach($sidebar_menu_items as $sec_key=>$section)
                                <?php if(empty($section)) continue; ?>
                                <li class='sidebar-title'><span>{!! $section['sidebar-title'] ?? '' !!}</span></li>
                                @foreach($section['sidebar-items'] as $menu_key=>$menu)
                                    <?php if(empty($menu)) continue; ?>
                                    <li class="sidebar-item {{ in_array($get_selected_sidebar,$menu['selected']) ? 'active' : '' }}">
                                        <a href="{{ $menu['href'] ?? '' }}" class='sidebar-link'>
                                            <?php $icon = isset($menu['icon']) ? asset('assets/images/flaticon/'.$menu['icon']) : '';?>
                                            <img src="{{$icon}}" data-bs-toggle="tooltip" data-bs-original-title="{{strip_tags($section['sidebar-title'].' : '.$menu['title'])}}" data-bs-placement="right"/>
                                            <span>{!! $menu['title'] ?? '' !!}</span>
                                        </a>
                                    </li>
                                @endforeach
                            @endforeach

                        </ul>
                    </div>
                    <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
                </div>
            </div>
            <div id="main">
                <nav class="navbar navbar-header navbar-expand navbar-light" id="notification-navbar">
                    <a class="sidebar-toggler pointer"><span class="navbar-toggler-icon"></span></a>
                    <button class="btn navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">

                        <ul class="navbar-nav d-flex align-items-center navbar-light ms-auto">

                            <li class="dropdown nav-icon me-2">
                                <a href="#" id="notification-dropdown" data-bs-toggle="dropdown"
                                   class="nav-link  dropdown-toggle nav-link-lg nav-link-user">
                                    <div class="d-lg-inline-block">
                                        <i class="far fa-bell notification-icon text-warning"></i><span class="badge bg-danger notification-count">{{count($notifications)}}</span>
                                    </div>
                                </a>
                                @if(count($notifications)>0)
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-md-end dropdown-menu-large overflow-y h-max-500px"  id="notification-list">
                                    <h6 class='pt-2 pb-0 px-4'>{{ __('Notifications') }}</h6>
                                    <div>
                                        <ul class="list-group rounded-none">
                                        @foreach($notifications as $row)
                                            <?php $not_link = $row->linkable=='1' && $row->custom_link!='' ? $row->custom_link : '';?>
                                            <div class="dropdown-divider"></div>
                                            <a href="{{ $not_link  }}" class="notification-mark-seen" data-id="{{$row->id}}">
                                            <li class="list-group-item border-0 align-items-start py-0">
                                                <div class="avatar {{$row->color_class}} me-3 align-items-center">
                                                    <span class="avatar-content"><i class="{{ $row->icon }}"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class='text-bold mb-0'>{{ $row->title }}</h6>
                                                    <p class='text-xs mb-0'>
                                                        <?php
                                                        echo $row->description;
                                                        if(date('Y-m-d',strtotime($row->created_at))==date('Y-m-d',strtotime(date('Y-m-d'))))
                                                        $converted = convert_datetime_to_timezone($row->created_at,'',false,'h:i A');
                                                        else $converted = convert_datetime_to_phrase($row->created_at,true,date('Y-m-d H:i:s'),false);
                                                        echo ' <span class="text-muted">('.$converted.')</span>';
                                                        ?>
                                                    </p>
                                                </div>
                                            </li>
                                            </a>
                                        @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif
                            </li>

                            <li class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user pe-0">
                                    <div class="avatar me-1">
                                        <img src="{{$profile_pic}}" class="border bg-white" alt="" srcset="">
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('account') }}"><i data-feather="user"></i> {{ __('Account') }}</a>
                                    <div class="dropdown-divider"></div>

                                    @if($is_member || $is_agent)
                                        <a class="dropdown-item {{Auth::user()->package_id==1 ? 'text-warning fw-bold' : ''}}" href="{{ $pricing_link }}"><i data-feather="credit-card"></i> {{ Auth::user()->package_id==1 ?__('Upgrade to Pro') : __('Renew / Upgrade') }}</a>
                                        <div class="dropdown-divider"></div>
                                    @endif

                                    @if($is_member)
                                        <a class="dropdown-item" href="{{route('transaction-log')}}"><i data-feather="refresh-ccw"></i> {{ __('Transactions') }}</a>
                                        <div class="dropdown-divider"></div>
                                    @endif

                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"><i data-feather="log-out"></i> {{ __('Logout') }}</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                @yield('content')

                <footer class="">
                    <div class="footer clearfix mb-0 text-muted">
                        <div class="float-start">
                            <span><?php echo date("Y")?> &copy; {{ config('app.name') }}</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>


        <script src="{{ asset('assets/vendors/popper/popper.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/js/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/nicescroll/jquery.nicescroll.min.js') }}"></script>
        <script src="{{ asset('assets/js/main.js') }}"></script>

        @if($load_datatable)
            <script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/datatables/DataTables-1.10.25/js/dataTables.bootstrap5.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/datatables/ColReorder-1.5.4/js/colReorder.bootstrap5.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/datatables/Buttons-1.7.1/js/dataTables.buttons.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/datatables/Buttons-1.7.1/js/buttons.bootstrap5.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/datatables/Buttons-1.7.1/js/buttons.html5.min.js') }}"></script>
            <script src="{{asset('assets/cdn/js/moment.js')}}"></script>
            <script src="{{asset('assets/cdn/js/daterangepicker.min.js')}}"></script> 
        @endif

        <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/datetimepicker/build/jquery.datetimepicker.full.min.js') }}"></script>
        <script src="{{asset('assets/cdn/js/select2.min.js')}}"></script>
        <script src="{{asset('assets/cdn/js/sweetalert2.min.js')}}"></script>
        <script src="{{asset('assets/cdn/js/toastr.min.js')}}"></script>
        <script src="{{ asset('assets/vendors/OwlCarousel/dist/owl.carousel.min.js') }}"></script>

        <script src="{{ asset('assets/vendors/chocolat/js/jquery.chocolat.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/prism/prism.js') }}"></script>
        <script src="{{ asset('assets/vendors/summernote/summernote-bs4.js') }}"></script>

        @include('shared.variables')
        @stack('scripts-footer')
        @stack('styles-footer')

        <script src="{{ asset('assets/js/common/common.js') }}"></script>
        <script src="{{ asset('assets/js/common/include.js') }}"></script>

    </body>

</html>