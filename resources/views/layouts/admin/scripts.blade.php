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
    <script src="{{asset('public/front/assets/vendor/libs/moment/moment.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>
    <script src="{{asset('public/front/assets/js/select/bootstrap-select.min.js')}}"></script>
    <!-- Plugins JS Ends-->
    
    <script src="{{asset('public/front/assets/js/demo.js')}}"></script>
    <!-- Theme js-->
    <script src="{{asset('public/front/assets/js/script.js')}}"></script>
    <script src="{{asset('public/front/assets/js/script1.js')}}"></script>
    <script src="{{asset('public/front/assets/js/theme-customizer/customizer.js')}}"></script>
    <script src="{{ asset('public/front/assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{asset('public/front/assets/js/sanitize.js') }}"></script>

    <script>
        @if (session('success'))
            successMessage("{{ session('success') }}");
        @endif
        @if (session('error'))
            errorMessage("{{ session('error') }}");
        @endif
        function errorMessage(msg){            
            sendmsg('error',msg);
        }
        function successMessage(msg){
            sendmsg('success',msg);
        }
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
        
        function ldrshow() {
            const fullPageLoader = document.querySelector('.full-page-loader');
            fullPageLoader.style.display = 'flex';
        }
        function ldrhide() {
            const fullPageLoader = document.querySelector('.full-page-loader');
            fullPageLoader.style.display = 'none';
        }

        function loadSelect2() {
            const select2 = $('.select2');

            // Select2 Country
            if (select2.length) {
                select2.each(function () {
                var $this = $(this);
                // select2Focus($this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Select value',
                    dropdownParent: $this.parent()
                });
                });
            }
        }

        function mobileinput(input) {
            // Allow only alphanumeric characters and limit to 12
            input.value = input.value.replace(/[^0-9]/g, '');
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
        }

        $("body").on("input",".sanitize",function(){            
            let value = $(this).val();  
            value = value.replace(/[^a-zA-Z0-9 @.-]/g, '');
            let at = 0, dt = 0, dc = 0;
            value = value.split('').filter((c) => {
                if (c === '@') { at++; return at <= 1; }
                if (c === '.') { dt++; return dt <= 1; }
                if (c === '-') { dc++; return dc <= 1; }
                return true;
            }).join('');
            $(this).val(value);
        })
    </script>
    @stack('scripts')
    <script>
        let MAINURL = "{{ url('/') }}";
    </script>
    <script src="{{ asset('public/js/common.js') }}"></script>

    @isset($pathurl)
        <script src="{{ asset('public/modules/sa/'.$pathurl.'.js') }}"></script>
    @endisset