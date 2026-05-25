<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')  | ParaCare+ HMIS — Uttarakhand</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="" />
    @include('layouts.state-admin.dashboard.head')
</head>

<body>

    <!-- ═══════════════════════ SIDEBAR ═══════════════════════ -->
    @include('layouts.state-admin.dashboard.sidebar')


        <!-- MAIN -->
        <div class="main">
            <!-- TOPBAR -->
             @include('layouts.state-admin.dashboard.header')

            <div class="content" id="mainContent">
                @yield('content')
            </div><!-- /content -->
        </div><!-- /main -->

    @include('layouts.state-admin.dashboard.scripts')
</body>

</html>