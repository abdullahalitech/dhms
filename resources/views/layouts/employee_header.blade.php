<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Employee - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="{{url('template/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.css" rel="stylesheet"/>
    <!-- Custom styles for this template-->
    <link href="{{url('template/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>

    <!-- Standalone -->
    <link href="{{url('template/css/datepicker/datepicker.min.css')}}" rel="stylesheet" />
    <!-- For Bootstrap 4 -->
    <link href="{{url('template/css/datepicker/datepicker-bs4.min.css')}}" rel="stylesheet" />
    <!-- For Bulma -->
    <link href="{{url('template/css/datepicker/datepicker-bulma.min.css')}}" rel="stylesheet" />
    <!-- For Foundation -->
    <link href="{{url('template/css/datepicker/datepicker-foundation.min.css')}}" rel="stylesheet" />

    <style>
        .w-150{
            width:150px!important;
        }

        .w-125{
            width:125px!important;
        }

        .parsley-errors-list {
            color: red;
            margin-top: 5px;
            font-size: 13px;
        }

        .parsley-error {
            border-color: red;
        }
        input[type="search"] {
            border: 1px solid #eee !important;
            border-radius: 5px !important;
            padding: 4px 8px;
            font-size: 15px;
        }
        input[type="search"]:focus-visible{
            outline:none!important;
        }
        div#employee-table_filter, div#platform-table_filter, div#project-table_filter {
            text-align:end !important;
        }
        .table-bordered:empty {
        
        }
        .border-2{
            border-bottom:2px solid;
        }
        .codex-editor.codex-editor--narrow {
            max-height: 200px;
            overflow: auto;
        }
        .select2-container{
            width:100% !important;
            
        }
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #aaaaaa63;
            border-radius: 4px;
            height: 37px;
            padding: 4px 0px;
        }
        .fs-14{
            font-size:14px !important;
        }
        .datepicker-dropdown .datepicker-picker{
            border-radius:5px !important;
            border:1px solid #eee !important;
        }
        .custom-nav-link:hover{
            color:#fff !important
        }
        .active{
            color:#fff !important;
        }

        .datepicker-grid {
            color:#000;
        }
        .dataTables_paginate {
            text-align: center;
        }

        .paginate_button {
            background-color: transparent;
            text-decoration: none;
            padding: 1px 7px;
            border-radius: 2px;
            cursor: pointer;
            margin-right: 5px;
            color: #6c757d;
            border: 1px solid #85858594;
        }

        .paginate_button:hover {
            background-color: #24b3fd;
            color: #fff;
            border-color: #24b3fd;
        }

        .current {
            background-color: #24b3fd;
            color: #fff;
            border-color: #24b3fd;
        }

        .table-responsive::-webkit-scrollbar {
            display: none;
        }

    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

       

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-primary text-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <!-- <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form> -->
                    <a class="sidebar-brand d-flex align-items-center " href="{{url('user/dashboard')}}">
                        <!-- <div class="sidebar-brand-icon rotate-n-15">
                            <i class="fas fa-laugh-wink"></i>
                        </div> -->
                        <div class="sidebar-brand-text mx-3"> <img src="{{url('images/logo.png')}}"  class="w-125"/></div>
                    </a>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav mx-auto w-100 d-flex justify-content-center">
                        <li class="nav-item ">
                            <a class="nav-link custom-nav-link @if( Route::getCurrentRoute()->getName() == 'employeeDashboard' ) active @endif" href="{{url('user/dashboard')}}">
                                
                                <span>Dashboard</span></a>
                        </li>

                        <!-- Nav Item - Pages Collapse Menu -->
                        <li class="nav-item ">
                            <a class="nav-link custom-nav-link @if( Route::getCurrentRoute()->getName() == 'userProject' ) active @endif" href="{{url('user/projects')}}">
                                
                                <span>My Projects</span></a>
                        </li>

                        <li class="nav-item ">
                            <a class="nav-link custom-nav-link @if( Route::getCurrentRoute()->getName() == 'platform' ) active @endif" href="{{url('user/projects/shared')}}">
                                
                                <span>Shared Projects</span></a>
                        </li>
                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        
                        

                    
                    </ul>

                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                            <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-white small">{{Auth::user()->name}}</span>
                                <img class="img-profile rounded-circle"
                                    src="{{url('template/img/undraw_profile.svg')}}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item" href="route('logout')" onclick="event.preventDefault();
                                                                this.closest('form').submit();">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </a>
                                </form>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->