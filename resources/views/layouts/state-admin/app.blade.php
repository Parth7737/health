<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')  | ParaCare+ HMIS — Uttarakhand</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="" />
    @include('layouts.state-admin.head')
</head>

<body>

    <!-- SIDEBAR -->
    <div class="app-shell">
        @include('layouts.state-admin.sidebar')

        <!-- MAIN -->
        <div class="main">
            <!-- TOPBAR -->
             @include('layouts.state-admin.header')

            <div class="content">
                @yield('content')
            </div><!-- /content -->
        </div><!-- /main -->
    </div><!-- /app-shell -->

    @include('layouts.state-admin.scripts')
</body>

</html>