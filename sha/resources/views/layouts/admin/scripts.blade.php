
    <!--   Core JS Files   -->
    <script src="{{ asset('public/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('public/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('public/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('public/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('public/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('public/js/plugin/chart.js/chart.min.js') }}"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('public/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('public/js/plugin/chart-circle/circles.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('public/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('public/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset('public/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('public/js/plugin/jsvectormap/world.js') }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('public/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('public/js/kaiadmin.min.js') }}"></script>


    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="{{ asset('public/js/setting-demo.js') }}"></script>
    <!-- <script src="{{ asset('public/js/demo.js') }}"></script> -->
    <script src="{{asset('public/front/assets/vendor/libs/select2/select2.js')}}"></script>
    @if ($errors->any())
        <script>
            @foreach($errors->all() as $error)
              errorMessage($error);
            @endforeach
        </script>
    @endif
    <script>
        $(".select2").select2();
        @if (session('success'))
            successMessage("{{ session('success') }}");
        @endif
        @if (session('error'))
            errorMessage("{{ session('error') }}");
        @endif
        function errorMessage(msg){
            var content = {};
            content.message =msg;
            content.title = "Error";
            content.icon = "fa fa-exclamation-triangle";
            $.notify(content, {
            type: 'danger',
            placement: {
                from: 'top',
                align: 'right',
            },
            time: 1000,
            delay: 3000,
            });
        }
        function successMessage(msg){
            var content = {};
            content.message =msg;
            content.title = "Success";
            content.icon = "fa fa-check-circle";
            $.notify(content, {
            type: 'success',
            placement: {
                from: 'top',
                align: 'right',
            },
            time: 1000,
            delay: 0,
            });
        }
        function form_alert(id, message) {
            swal({
              title: "Are you sure?",
              text: message,
              type: "warning",
              buttons: {
                cancel: {
                  visible: true,
                  text: "No, cancel!",
                  className: "btn btn-danger",
                },
                confirm: {
                  text: "Yes!",
                  className: "btn btn-success",
                },
              },
            }).then((willDelete) => {
              if (willDelete) {
                $('#'+id).submit()
              }
            });
        }
    </script>
    @stack('scripts')