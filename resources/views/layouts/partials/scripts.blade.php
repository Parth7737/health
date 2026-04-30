    <!-- latest jquery-->
    <script src="{{asset('public/front/assets/js/jquery.min.js')}}"></script>
    <!-- Bootstrap js-->
    <script src="{{asset('public/front/assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
    <!-- feather icon js-->
    <script src="{{asset('public/front/assets/js/icons/feather-icon/feather.min.js')}}"></script>
    <script src="{{asset('public/front/assets/js/icons/feather-icon/feather-icon.js')}}"></script>
    <!-- scrollbar js-->
    <script src="{{asset('public/front/assets/js/scrollbar/simplebar.min.js')}}"></script>
    <script src="{{asset('public/front/assets/js/scrollbar/custom.js')}}"></script>
    <!-- Sidebar jquery-->
    <script src="{{asset('public/front/assets/js/config2.js')}}"></script>
    <!-- Plugins JS start-->
    <script src="{{asset('public/front/assets/js/sidebar-menu.js')}}"></script>
    <script src="{{asset('public/front/assets/js/sidebar-pin.js')}}"></script>
    <script src="{{asset('public/front/assets/js/slick/slick.min.js')}}"></script>
    <script src="{{asset('public/front/assets/js/slick/slick.js')}}"></script>
    <script src="{{asset('public/front/assets/js/header-slick.js')}}"></script>
    <script src="{{asset('public/front/assets/js/form-validation-custom.js')}}"></script>
    <script src="{{asset('public/front/assets/js/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('public/front/assets/js/select/bootstrap-select.min.js')}}"></script>
    <!-- Plugins JS Ends-->
    
    <!-- Theme js-->
    <script src="{{asset('public/front/assets/js/script.js')}}"></script>
    <script src="{{asset('public/front/assets/js/script1.js')}}"></script>
    <!-- <script src="{{asset('public/front/assets/js/theme-customizer/customizer.js')}}"></script> -->
    <script src="{{ asset('public/front/assets/js/sweet-alert/sweetalert.min.js') }}"></script>

    <script>
        function sendmsg(type, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: type,
                title: message
            });
        }

        function loader(type = 'show') {
            const fullPageLoader = document.querySelector('.full-page-loader');
            if(type == 'show') {
             fullPageLoader.style.display = 'flex';
            } else {
                fullPageLoader.style.display = 'none';
            }
        }
    </script>
    @stack('scripts')
    <script>
        let MAINURL = "{{ url('/') }}";
    </script>
    <script src="{{ asset('public/js/common.js') }}"></script>

    @isset($pathurl)
        <script src="{{ asset('public/modules/'.$pathurl.'.js') }}"></script>
    @endisset