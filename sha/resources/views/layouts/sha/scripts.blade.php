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
    <script src="{{asset('public/front/assets/vendor/js/menu.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/moment/moment.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>

    <script src="{{asset('public/front/assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('public/front/assets/js/app-logistics-dashboard.js')}}"></script>

    <script src="{{asset('public/front/assets/js/main.js')}}"></script>
    <script src="{{asset('public/front/assets/js/forms-pickers.js')}}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{asset('public/front/assets/js/demo.js')}}"></script>

    <!-- toastr JS -->
    <script src="{{asset('public/front/assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{asset('public/front/assets/js/ui-toasts.js') }}"></script>
    <!-- Sweet Alert -->
    <script src="{{ asset('public/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{asset('public/front/assets/js/zoom.js')}}"></script>
    <script src="{{asset('public/front/assets/js/sanitize.js') }}"></script>

    <script>
        $(".select2").select2();
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
            var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
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
            var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
            $toastlast = $toast;
        }
    </script>

    <!-- <script src="{{asset('public/front/assets/js/xzIWsKouuw.js')}}"></script> -->

    @stack('scripts')