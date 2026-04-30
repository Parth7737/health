<div class="loader-overlay" style="display:none;">
    <div class="spinner-grow text-success" style="width: 5rem; height: 5rem;"  role="status">
        </div>
</div>
<script src="{{asset('public/front/assets/vendor/libs/jquery/jquery.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/js/bootstrap.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/node-waves/node-waves.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/hammer/hammer.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/i18n/i18n.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
    <!-- <script src="{{asset('public/front/assets/vendor/js/menu.js')}}"></script> -->
    <script src="{{asset('public/front/assets/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/moment/moment.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>

    <script src="{{asset('public/front/assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <!-- <script src="{{asset('public/front/assets/js/app-logistics-dashboard.js')}}"></script> -->

    <script src="{{asset('public/front/assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <!-- <script src="{{asset('public/front/assets/js/main.js')}}"></script>     -->
    <script src="{{asset('public/front/assets/js/forms-pickers.js')}}"></script>
    <script src="{{asset('public/front/assets/js/demo.js')}}"></script>
    <script src="{{asset('public/front/assets/js/forms-selects.js')}}"></script>

       <!-- Bootstrap Notify -->
    <script src="{{asset('public/front/assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{asset('public/front/assets/js/ui-toasts.js') }}"></script>
    <script src="{{ asset('public/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{asset('public/front/assets/js/zoom.js')}}"></script>
    <script src="{{asset('public/front/assets/js/sanitize.js') }}"></script>
    <script src="{{asset('public/front/assets/js/pages-account-settings-security.js') }}"></script>
    
    <script>
        @if (session('success'))
            successMessage("{{ session('success') }}");
        @endif
        @if (session('error'))
            errorMessage("{{ session('error') }}");
        @endif
        function errorMessage(msg){            
            var shortCutFunction = 'error',
            title = 'Error',
            
            prePositionClass =
            typeof toastr.options.positionClass === 'undefined' ? 'toast-top-right' : toastr.options.positionClass;
            toastr.options.showDuration = 300;
            toastr.options = {
                maxOpened: 1,
                autoDismiss: true,
                closeButton: true,
                newestOnTop: true,
                progressBar:true,
                positionClass: 'toast-top-right',
                onclick: null,
            };    
            var $toast = toastr[shortCutFunction](msg, title);
            $toastlast = $toast;
        }
        function successMessage(msg){
            var shortCutFunction = 'success',
            title = 'Success',
            
            prePositionClass =
            typeof toastr.options.positionClass === 'undefined' ? 'toast-top-right' : toastr.options.positionClass;
            toastr.options.showDuration = 300;
            toastr.options = {
                maxOpened: 1,
                autoDismiss: true,
                closeButton: true,
                newestOnTop: true,
                progressBar:true,
                positionClass: 'toast-top-right',
                onclick: null,
            };    
            var $toast = toastr[shortCutFunction](msg, title); 
            $toastlast = $toast;
        }

        function ldrshow() {
            $('.loader-overlay').show();
        }
        function ldrhide() {
            $('.loader-overlay').hide();
        }

        function loadSelect2() {
            const select2 = $('.select2');

            // Select2 Country
            if (select2.length) {
                select2.each(function () {
                var $this = $(this);
                select2Focus($this);
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

    <!-- <script src="{{asset('public/front/assets/js/xzIWsKouuw.js')}}"></script> -->

    @stack('scripts')
    
    @yield('script')
