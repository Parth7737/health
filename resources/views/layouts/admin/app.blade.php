@php 
    $logo = App\Models\BusinessSetting::where('key','front_logo')->value('value');
    if($logo){
        $logo = asset('public/storage/'.$logo);
    }else{
        $logo = asset('public/front/assets/img/paracare-logo.png');
    }
@endphp 
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Paracare - Sakhuja Hospital">
    <meta name="keywords" content="Paracare - Sakhuja Hospital">
    <meta name="author" content="Paracare - Sakhuja Hospital">
    <!-- <link rel="icon" href="{{$logo}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{$logo}}" type="image/x-icon"> -->
    <title>@yield('title') - Paracare</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="" />
    <x-route-js :routes="$routes ?? []" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('public/front/assets/images/favicon.png')}}" />
    @include('layouts.admin.head')
</head>

<body>
    <!-- loader starts-->
    <div class="loader-wrapper">
      <div class="loader-index"> <span></span></div>
      <svg>
        <defs></defs>
        <filter id="goo">
          <fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur>
          <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo"> </fecolormatrix>
        </filter>
      </svg>
    </div>
    <!-- loader ends-->
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper default-wrapper" id="pageWrapper">
            @include('layouts.admin.header')

            <!-- Page Body Start-->
            <div class="page-body-wrapper default-menu default-menu">
                @include('layouts.admin.sidebar')

                <div class="page-body">
                    @yield('content')
                </div>
                @include('layouts.admin.footer')
            </div>
        </div>
        <!-- Status Modal -->
        <div class="modal fade view_modal_data" id="view_modal_dataModel" tabindex="-1" aria-labelledby="view_modal_dataModelLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" id="ajax_view_modal">
            </div>
          </div>
        </div>

        <!-- Add Modal -->
        <div class="modal fade add-datamodal" id="add-datamodal" tabindex="-1" aria-labelledby="add-dataModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" id="ajaxdata">
            </div>
          </div>
        </div>
    </div>
    <div class="full-page-loader">
      <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    @include('layouts.admin.scripts')
</body>
</html>
