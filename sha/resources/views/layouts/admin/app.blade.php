<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title')</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"/>
    <link
      rel="icon"
      href="{{ asset('public/images/logo.jpg') }}"
      type="image/x-icon"
    />

    <!-- Fonts and icons -->
    <script src="{{ asset('public/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["{{ asset('public/css/fonts.min.css') }}"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>
    @include('layouts.admin.head')
    </head>
<body>
    
    <div class="wrapper">
        @include('layouts.admin.sidebar')
        <div class="main-panel">
            
            @include('layouts.admin.header')
            
            <div class="container">
                <div class="page-inner">
                    
                    <div class="page-header">
                        <h4 class="page-title">{{ @$main_li??'' }}</h4>
                        <ul class="breadcrumbs">
                            <li class="nav-home">
                            <a href="{{ route('dashboard') }}">
                                <i class="icon-home"></i>
                            </a>
                            </li>
                            @if(@$main_li)
                            <li class="separator">
                            <i class="icon-arrow-right"></i>
                            </li>
                              <li class="nav-item">
                                <a href="javascript:;">{{ $main_li }}</a>
                              </li>
                            @endif
                            @if(@$sub_li)
                            <li class="separator">
                            <i class="icon-arrow-right"></i>
                            </li>
                            <li class="nav-item">
                            <a href="javascript:;">{{ $sub_li }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    @yield('content')
                
            </div>
            
            @include('layouts.admin.footer')
        </div>
        
    </div>
    @include('layouts.admin.scripts')
</body>
</html>
