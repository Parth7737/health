<!doctype html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Paracare - Sakhuja Hospital">
    <meta name="keywords" content="Paracare - Sakhuja Hospital">
    <meta name="author" content="Paracare - Sakhuja Hospital">
    <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="../assets/images/favicon.png" type="image/x-icon">
    <title>Paracare - Sakhuja Hospital</title>
    <title>@yield('title')</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('public/front/assets/img/favicon/favicon.ico')}}" />
    @include('layouts.front.head')
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
            @include('layouts.front.header')

            <!-- Page Body Start-->
            <div class="page-body-wrapper default-menu default-menu">
                @include('layouts.front.sidebar')

                <div class="page-body">
                    @yield('content')
                </div>
                @include('layouts.front.footer')
            </div>
        </div>
    </div>
    @include('layouts.front.scripts')

<!-- IPD Bed Status Modal -->
<div class="modal fade" id="bedStatusModal" tabindex="-1" aria-labelledby="bedStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="bedStatusModalLabel">IPD Bed Status</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <!-- Bed 1: Available -->
          <div class="col-md-3">
            <div class="card border-success">
              <div class="card-body text-center">
                <!-- Bed SVG Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 -960 960 960" width="40" fill="#28a745">
                  <path d="M40-200v-600h80v400h320v-320h320q66 0 113 47t47 113v360h-80v-120H120v120H40Zm240-240q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35Zm240 40h320v-160q0-33-23.5-56.5T760-640H520v240ZM280-520q17 0 28.5-11.5T320-560q0-17-11.5-28.5T280-600q-17 0-28.5 11.5T240-560q0 17 11.5 28.5T280-520Z"/>
                </svg>
                <h6 class="mt-2">Room 101 - Bed 1</h6>
                <span class="badge bg-success">Available</span>
              </div>
            </div>
          </div>
          <!-- Bed 2: Occupied -->
          <div class="col-md-3">
            <div class="card border-danger">
              <div class="card-body text-center">
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 -960 960 960" width="40" fill="#dc3545">
                  <path d="M40-200v-600h80v400h320v-320h320q66 0 113 47t47 113v360h-80v-120H120v120H40Zm240-240q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35Zm240 40h320v-160q0-33-23.5-56.5T760-640H520v240ZM280-520q17 0 28.5-11.5T320-560q0-17-11.5-28.5T280-600q-17 0-28.5 11.5T240-560q0 17 11.5 28.5T280-520Z"/>
                </svg>
                <h6 class="mt-2">Room 101 - Bed 2</h6>
                <span class="badge bg-danger">Occupied</span>
              </div>
            </div>
          </div>
          <!-- Bed 3: Reserved -->
          <div class="col-md-3">
            <div class="card border-warning">
              <div class="card-body text-center">
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="http://www.w3.org/2000/svg" width="40" fill="#ffc107">
                  <path d="M40-200v-600h80v400h320v-320h320q66 0 113 47t47 113v360h-80v-120H120v120H40Zm240-240q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35Zm240 40h320v-160q0-33-23.5-56.5T760-640H520v240ZM280-520q17 0 28.5-11.5T320-560q0-17-11.5-28.5T280-600q-17 0-28.5 11.5T240-560q0 17 11.5 28.5T280-520Z"/>
                </svg>
                <h6 class="mt-2">Room 102 - Bed 1</h6>
                <span class="badge bg-warning text-dark">Reserved</span>
              </div>
            </div>
          </div>
          <!-- Bed 4: Cleaning -->
          <div class="col-md-3">
            <div class="card border-info">
              <div class="card-body text-center">
                <!-- Cleaning SVG Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 0 24 24" width="40" fill="#17a2b8"><path d="M16.24 11.51l-4.95-4.95a1.003 1.003 0 00-1.42 1.42l4.95 4.95a1.003 1.003 0 001.42-1.42zM3 17.25V21h3.75l11.06-11.06-3.75-3.75L3 17.25zm2.92 2.92H5v-1.92l9.06-9.06 1.92 1.92-9.06 9.06z"/></svg>
                <h6 class="mt-2">Room 102 - Bed 2</h6>
                <span class="badge bg-info text-dark">Cleaning</span>
              </div>
            </div>
          </div>
          <!-- Bed 5: Maintenance -->
          <div class="col-md-3">
            <div class="card border-secondary">
              <div class="card-body text-center">
                <!-- Maintenance SVG Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 0 24 24" width="40" fill="#6c757d"><path d="M22.7 19.3l-2.4-2.4c.4-.8.7-1.7.7-2.6 0-3.3-2.7-6-6-6-.9 0-1.8.2-2.6.7l-2.4-2.4c-.4-.4-1-.4-1.4 0s-.4 1 0 1.4l2.4 2.4c-.5.8-.7 1.7-.7 2.6 0 3.3 2.7 6 6 6 .9 0 1.8-.2 2.6-.7l2.4 2.4c.4.4 1 .4 1.4 0s.4-1 0-1.4zM12 17c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5z"/></svg>
                <h6 class="mt-2">Room 103 - Bed 1</h6>
                <span class="badge bg-secondary">Maintenance</span>
              </div>
            </div>
          </div>
          <!-- Bed 6: Isolation -->
          <div class="col-md-3">
            <div class="card border-dark">
              <div class="card-body text-center">
                <!-- Isolation SVG Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 0 24 24" width="40" fill="#343a40"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                <h6 class="mt-2">Room 103 - Bed 2</h6>
                <span class="badge bg-dark">Isolation</span>
              </div>
            </div>
          </div>
          <!-- Bed 7: Reserved (VIP) -->
          <div class="col-md-3">
            <div class="card border-primary">
              <div class="card-body text-center">
                <!-- VIP SVG Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 0 24 24" width="40" fill="#007bff"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                <h6 class="mt-2">Room 104 - Bed 1</h6>
                <span class="badge bg-primary">VIP Reserved</span>
              </div>
            </div>
          </div>
          <!-- Bed 8: Discharged -->
          <div class="col-md-3">
            <div class="card border-light">
              <div class="card-body text-center">
                <!-- Discharged SVG Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 0 24 24" width="40" fill="#adb5bd"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-1 14H6v-2h12v2zm0-4H6v-2h12v2zm0-4H6V7h12v2z"/></svg>
                <h6 class="mt-2">Room 104 - Bed 2</h6>
                <span class="badge bg-light text-dark">Discharged</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



    <!-- OT Status Modal -->
<div class="modal fade" id="otStatusModal" tabindex="-1" aria-labelledby="otStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title text-white" id="otStatusModalLabel">OT Room Status</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <!-- OT Room 1: Active -->
          <div class="col-md-4">
            <div class="card border-success">
              <div class="card-body text-center">
                <!-- OT Room SVG Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="960 0 24 24" width="40" fill="#28a745">
                  <path d="M280-240h40v-60h320v60h40v-160q0-33-23.5-56.5T600-480H460v140H320v-180h-40v280Zm110-120q21 0 35.5-14.5T440-410q0-21-14.5-35.5T390-460q-21 0-35.5 14.5T340-410q0 21 14.5 35.5T390-360ZM160-120v-480l320-240 320 240v480H160Zm80-80h480v-360L480-740 240-560v360Zm240-270Z"/>
                </svg>
                <h6 class="mt-2">OT101</h6>
                <span class="badge bg-success">Active</span>
              </div>
            </div>
          </div>
          <!-- OT Room 2: Idle -->
          <div class="col-md-4">
            <div class="card border-secondary">
              <div class="card-body text-center">
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="960 0 24 24" width="40" fill="#6c757d">
                  <path d="M280-240h40v-60h320v60h40v-160q0-33-23.5-56.5T600-480H460v140H320v-180h-40v280Zm110-120q21 0 35.5-14.5T440-410q0-21-14.5-35.5T390-460q-21 0-35.5 14.5T340-410q0 21 14.5 35.5T390-360ZM160-120v-480l320-240 320 240v480H160Zm80-80h480v-360L480-740 240-560v360Zm240-270Z"/>
                </svg>
                <h6 class="mt-2">OT102</h6>
                <span class="badge bg-secondary">Idle</span>
              </div>
            </div>
          </div>
          <!-- OT Room 3: Under Maintenance -->
          <div class="col-md-4">
            <div class="card border-danger">
              <div class="card-body text-center">
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="960 0 24 24" width="40" fill="#dc3545">
                  <path d="M280-240h40v-60h320v60h40v-160q0-33-23.5-56.5T600-480H460v140H320v-180h-40v280Zm110-120q21 0 35.5-14.5T440-410q0-21-14.5-35.5T390-460q-21 0-35.5 14.5T340-410q0 21 14.5 35.5T390-360ZM160-120v-480l320-240 320 240v480H160Zm80-80h480v-360L480-740 240-560v360Zm240-270Z"/>
                </svg>
                <h6 class="mt-2">OT103</h6>
                <span class="badge bg-danger">Under Maintenance</span>
              </div>
            </div>
          </div>
          <!-- OT Room 4: Cleaning -->
          <div class="col-md-4">
            <div class="card border-info">
              <div class="card-body text-center">
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 0 24 24" width="40" fill="#17a2b8"><path d="M16.24 11.51l-4.95-4.95a1.003 1.003 0 00-1.42 1.42l4.95 4.95a1.003 1.003 0 001.42-1.42zM3 17.25V21h3.75l11.06-11.06-3.75-3.75L3 17.25zm2.92 2.92H5v-1.92l9.06-9.06 1.92 1.92-9.06 9.06z"/></svg>
                <h6 class="mt-2">OT104</h6>
                <span class="badge bg-info text-dark">Cleaning</span>
              </div>
            </div>
          </div>
          <!-- OT Room 5: Reserved -->
          <div class="col-md-4">
            <div class="card border-warning">
              <div class="card-body text-center">
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 0 24 24" width="40" fill="#ffc107"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                <h6 class="mt-2">OT105</h6>
                <span class="badge bg-warning text-dark">Reserved</span>
              </div>
            </div>
          </div>
          <!-- OT Room 6: Disinfecting -->
          <div class="col-md-4">
            <div class="card border-light">
              <div class="card-body text-center">
                <svg xmlns="http://www.w3.org/2000/svg" height="40" viewBox="0 0 24 24" width="40" fill="#adb5bd"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-1 14H6v-2h12v2zm0-4H6v-2h12v2zm0-4H6V7h12v2z"/></svg>
                <h6 class="mt-2">OT106</h6>
                <span class="badge bg-light text-dark">Disinfecting</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


</body>
</html>
