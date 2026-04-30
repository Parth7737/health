@extends('layouts.front.app')
@section('title','Demo Code | Paracare+')
@section('content')
<div class="container-fluid">
    <div class="row">
    <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Message Toasts</h5>
                    <p class="f-m-light mt-1">
                       Use<code> position-fixed</code>  class to <code> [top/end/start/bottom]</code> toasts.</p>
                    <div class="card-header-right">
                      <ul class="list-unstyled card-option">
                        <li><i class="fa-solid fa-gear fa-spin"></i></li>
                        <li><i class="view-html fa-solid fa-code"></i></li>
                        <li><i class="icofont icofont-maximize full-card"></i></li>
                        <li><i class="icofont icofont-minus minimize-card"></i></li>
                        <li><i class="icofont icofont-refresh reload-card"></i></li>
                        <li><i class="icofont icofont-error close-card"></i></li>
                      </ul>
                    </div>
                  </div>
                  <div class="card-body common-flex common-toasts">
                    <button class="btn btn-success" id="liveToastBtn6" type="button">Success Toast</button>
                    <div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
                      <div class="toast" id="liveToast6" role="alert" aria-live="polite" aria-atomic="true">
                        <div class="common-space alert-light-success">
                          <div class="toast-body"><i class="close-search stroke-success" data-feather="check-square"></i>Success: We've updated your info</div>
                          <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                      </div>
                    </div>
                    <button class="btn btn-warning" id="liveToastBtn5" type="button">Warning Toast</button>
                    <div class="toast-container position-fixed top-50 end-0 p-3 toast-index toast-rtl">
                      <div class="toast" id="liveToast5" role="alert" aria-live="polite" aria-atomic="true">
                        <div class="common-space alert-light-warning">
                          <div class="toast-body"><i class="close-search stroke-warning" data-feather="alert-triangle"></i>Software drivers needed to be updated in advance</div>
                          <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                      </div>
                    </div>
                    <button class="btn btn-danger" id="liveToastBtn4" type="button">Error Toast</button>
                    <div class="toast-container position-fixed bottom-0 end-0 p-3 toast-index toast-rtl">
                      <div class="toast" id="liveToast4" role="alert" aria-live="polite" aria-atomic="true">
                        <div class="common-space alert-light-danger">
                          <div class="toast-body"><i class="close-search stroke-danger" data-feather="x-circle"></i>A database connection error has occurred</div>
                          <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                      </div>
                    </div>
                    <div class="code-box-copy">
                      <button class="code-box-copy__btn btn-clipboard" data-clipboard-target="#message-toast-copy" title="Copy"><i class="icofont icofont-copy-alt"></i></button>
                      <pre class="custom-scrollbar"><code class="language-html" id="message-toast-copy">&lt;div class="card-body common-flex common-toasts"&gt;
 &lt;button class="btn btn-success" id="liveToastBtn6" type="button"&gt;Success Toast&lt;/button&gt;
 &lt;div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl"&gt;
   &lt;div class="toast" id="liveToast6" role="alert" aria-live="polite" aria-atomic="true"&gt;
     &lt;div class="common-space alert-light-success"&gt;
       &lt;div class="toast-body"&gt;
         &lt;i class="close-search stroke-success" data-feather="check-square"&gt;&lt;/i&gt;Success: We've updated your info
       &lt;/div&gt;
       &lt;button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"&gt;&lt;/button&gt;
     &lt;/div&gt;
   &lt;/div&gt;
 &lt;/div&gt;
 &lt;button class="btn btn-warning" id="liveToastBtn5" type="button"&gt;Warning Toast&lt;/button&gt;
 &lt;div class="toast-container position-fixed top-50 end-0 p-3 toast-index toast-rtl"&gt;
   &lt;div class="toast" id="liveToast5" role="alert" aria-live="polite" aria-atomic="true"&gt;
     &lt;div class="common-space alert-light-warning"&gt;
       &lt;div class="toast-body"&gt;
         &lt;i class="close-search stroke-warning" data-feather="alert-triangle"&gt;&lt;/i&gt;Software drivers needed to be updated in advance
       &lt;/div&gt;
       &lt;button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"&gt;&lt;/button&gt;
     &lt;/div&gt;
   &lt;/div&gt;
 &lt;/div&gt;
 &lt;button class="btn btn-danger" id="liveToastBtn4" type="button"&gt;Error Toast&lt;/button&gt;
 &lt;div class="toast-container position-fixed bottom-0 end-0 p-3 toast-index toast-rtl"&gt;
   &lt;div class="toast" id="liveToast4" role="alert" aria-live="polite" aria-atomic="true"&gt;
     &lt;div class="common-space alert-light-danger"&gt;
       &lt;div class="toast-body"&gt;
         &lt;i class="close-search stroke-danger" data-feather="x-circle"&gt;&lt;/i&gt;A database connection error has occurred
       &lt;/div&gt;
       &lt;button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"&gt;&lt;/button&gt;
     &lt;/div&gt;
   &lt;/div&gt;
 &lt;/div&gt;
&lt;/div&gt;</code></pre>
                    </div>
                  </div>
                </div>
              </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Toast & Sweet Alert Demo</h4>
                </div>
                <div class="card-body">
                    <!-- Toast Examples -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Toast Notifications</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-primary" onclick="showToast('success')">Success Toast</button>
                                <button class="btn btn-danger" onclick="showToast('error')">Error Toast</button>
                                <button class="btn btn-warning" onclick="showToast('warning')">Warning Toast</button>
                                <button class="btn btn-info" onclick="showToast('info')">Info Toast</button>
                            </div>
                        </div>
                    </div>

                    <!-- Message Toast Examples -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Message Toast Notifications</h5>
                            <div class="notification-dropdown">
                                <ul>
                                    <li class="b-l-primary border-4 toast default-show-toast align-items-center text-light border-0 fade show" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                                        <div class="d-flex justify-content-between">
                                            <div class="toast-body">
                                                <p>Delivery processing</p>
                                            </div>
                                            <button class="btn-close btn-close-white me-2 m-auto" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                    </li>
                                    <li class="b-l-success border-4 toast default-show-toast align-items-center text-light border-0 fade show" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                                        <div class="d-flex justify-content-between">
                                            <div class="toast-body">
                                                <p>Order Complete</p>
                                            </div>
                                            <button class="btn-close btn-close-white me-2 m-auto" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                    </li>
                                    <li class="b-l-secondary border-4 toast default-show-toast align-items-center text-light border-0 fade show" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                                        <div class="d-flex justify-content-between">
                                            <div class="toast-body">
                                                <p>Tickets Generated</p>
                                            </div>
                                            <button class="btn-close btn-close-white me-2 m-auto" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                    </li>
                                    <li class="b-l-warning border-4 toast default-show-toast align-items-center text-light border-0 fade show" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                                        <div class="d-flex justify-content-between">
                                            <div class="toast-body">
                                                <p>Delivery Complete</p>
                                            </div>
                                            <button class="btn-close btn-close-white me-2 m-auto" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Sweet Alert Examples -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Sweet Alert Examples</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-primary" onclick="showSweetAlert('success')">Success Alert</button>
                                <button class="btn btn-danger" onclick="showSweetAlert('error')">Error Alert</button>
                                <button class="btn btn-warning" onclick="showSweetAlert('warning')">Warning Alert</button>
                                <button class="btn btn-info" onclick="showSweetAlert('info')">Info Alert</button>
                                <button class="btn btn-secondary" onclick="showSweetAlert('confirm')">Confirm Dialog</button>
                            </div>
                        </div>
                    </div>

                    <!-- Code Examples -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <h5 class="mb-3">Code Examples</h5>
                            <div class="card">
                                <div class="card-body">
                                    <h6>Toast Notifications</h6>
                                    <pre class="bg-light text-dark p-3 rounded">
// Basic Toast
function showToast(type) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    Toast.fire({
        icon: type,
        title: 'This is a ' + type + ' toast!'
    });
}

// Custom Toast
Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
}).fire({
    icon: 'success',
    title: 'Custom toast with hover pause'
});</pre>

                                    <h6 class="mt-4">Sweet Alerts</h6>
                                    <pre class="bg-light text-dark p-3 rounded">
// Basic Alert
function showSweetAlert(type) {
    Swal.fire({
        icon: type,
        title: 'Good job!',
        text: 'This is a ' + type + ' alert!',
        confirmButtonText: 'OK'
    });
}

// Confirm Dialog
Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
}).then((result) => {
    if (result.isConfirmed) {
        Swal.fire(
            'Deleted!',
            'Your file has been deleted.',
            'success'
        )
    }
});

// Custom Alert with HTML
Swal.fire({
    title: '<strong>HTML <u>example</u></strong>',
    icon: 'info',
    html: 'You can use <b>bold text</b>, ' +
          '<a href="//sweetalert2.github.io">links</a> ' +
          'and other HTML tags',
    showCloseButton: true,
    showCancelButton: true,
    focusConfirm: false,
    confirmButtonText: 'Great!',
    confirmButtonAriaLabel: 'Thumbs up!',
    cancelButtonText: 'Cancel',
    cancelButtonAriaLabel: 'Thumbs down'
});
</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loader Examples -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Loading Animations</h5>
                    <p class="f-m-light mt-1">Various loading animations for different use cases</p>
                    <div class="card-header-right">
                        <ul class="list-unstyled card-option">
                            <li><i class="fa-solid fa-gear fa-spin"></i></li>
                            <li><i class="view-html fa-solid fa-code"></i></li>
                            <li><i class="icofont icofont-maximize full-card"></i></li>
                            <li><i class="icofont icofont-minus minimize-card"></i></li>
                            <li><i class="icofont icofont-refresh reload-card"></i></li>
                            <li><i class="icofont icofont-error close-card"></i></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Spinner Loader -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="mb-3">Spinner Loader</h6>
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <button class="btn btn-primary mt-3" onclick="showLoader('spinner')">Show Spinner</button>
                                </div>
                            </div>
                        </div>

                        <!-- Pulse Loader -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="mb-3">Pulse Loader</h6>
                                    <div class="pulse-loader"></div>
                                    <button class="btn btn-primary mt-3" onclick="showLoader('pulse')">Show Pulse</button>
                                </div>
                            </div>
                        </div>

                        <!-- Dots Loader -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="mb-3">Dots Loader</h6>
                                    <div class="dots-loader">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <button class="btn btn-primary mt-3" onclick="showLoader('dots')">Show Dots</button>
                                </div>
                            </div>
                        </div>

                        <!-- Full Page Loader -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="mb-3">Full Page Loader</h6>
                                    <div class="full-page-loader">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mt-3" onclick="showLoader('fullpage')">Show Full Page</button>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Loader -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="mb-3">Progress Loader</h6>
                                    <div class="progress-loader">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <button class="btn btn-primary mt-3" onclick="showLoader('progress')">Show Progress</button>
                                </div>
                            </div>
                        </div>

                        <!-- Skeleton Loader -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="mb-3">Skeleton Loader</h6>
                                    <div class="skeleton-loader">
                                        <div class="skeleton-header"></div>
                                        <div class="skeleton-body">
                                            <div class="skeleton-line"></div>
                                            <div class="skeleton-line"></div>
                                            <div class="skeleton-line"></div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mt-3" onclick="showLoader('skeleton')">Show Skeleton</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Code Examples -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Loader Code Examples</h6>
                                    <pre class="bg-light text-dark p-3 rounded">
// CSS Loaders
.pulse-loader {
    width: 40px;
    height: 40px;
    background: #007bff;
    border-radius: 50%;
    animation: pulse 1.2s ease-in-out infinite;
}

.dots-loader span {
    display: inline-block;
    width: 10px;
    height: 10px;
    background: #007bff;
    border-radius: 50%;
    margin: 0 3px;
    animation: dots 1.4s infinite ease-in-out both;
}

.full-page-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

// JavaScript Loader Functions
function showLoader(type) {
    switch(type) {
        case 'spinner':
            // Show spinner loader
            break;
        case 'pulse':
            // Show pulse loader
            break;
        case 'dots':
            // Show dots loader
            break;
        case 'fullpage':
            // Show full page loader
            break;
        case 'progress':
            // Show progress loader
            break;
        case 'skeleton':
            // Show skeleton loader
            break;
    }
}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Vitals & Social Modal Trigger -->

<!-- Vitals & Social Modal -->

@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('public/front/assets/css/vendors/sweetalert2.css') }}">

<style>
    pre {
        margin: 0;
        white-space: pre-wrap;
    }

    /* Loader Styles */
    .pulse-loader {
        width: 40px;
        height: 40px;
        background: #007bff;
        border-radius: 50%;
        margin: 0 auto;
        animation: pulse 1.2s ease-in-out infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.8); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(0.8); opacity: 0.5; }
    }

    .dots-loader {
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .dots-loader span {
        display: inline-block;
        width: 10px;
        height: 10px;
        background: #007bff;
        border-radius: 50%;
        animation: dots 1.4s infinite ease-in-out both;
    }

    .dots-loader span:nth-child(1) { animation-delay: -0.32s; }
    .dots-loader span:nth-child(2) { animation-delay: -0.16s; }

    @keyframes dots {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    .full-page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .progress-loader {
        width: 100%;
        height: 4px;
        background: #f0f0f0;
        border-radius: 2px;
        overflow: hidden;
    }

    .skeleton-loader {
        width: 100%;
        padding: 15px;
    }

    .skeleton-header {
        height: 20px;
        background: #f0f0f0;
        border-radius: 4px;
        margin-bottom: 15px;
        animation: skeleton-loading 1.5s infinite;
    }

    .skeleton-line {
        height: 12px;
        background: #f0f0f0;
        border-radius: 4px;
        margin-bottom: 10px;
        animation: skeleton-loading 1.5s infinite;
    }

    @keyframes skeleton-loading {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }

</style>
@endpush

@push('scripts')
<script src="{{ asset('public/front/assets/js/sweet-alert/sweetalert.min.js') }}"></script>
<script>
    // Toast Notifications
    function showToast(type) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        Toast.fire({
            icon: type,
            title: 'This is a ' + type + ' toast!'
        });
    }

    // Sweet Alerts
    function showSweetAlert(type) {
        if (type === 'confirm') {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                }
            });
        } else {
            Swal.fire({
                icon: type,
                title: 'Good job!',
                text: 'This is a ' + type + ' alert!',
                confirmButtonText: 'OK'
            });
        }
    }

    const toastTrigger4 = document.getElementById("liveToastBtn4");
  const toastLiveExample4 = document.getElementById("liveToast4");
  if (toastTrigger4) {
    toastTrigger4.addEventListener("click", () => {
      const toast = new bootstrap.Toast(toastLiveExample4);

      toast.show();
    });
  }

  const toastTrigger5 = document.getElementById("liveToastBtn5");
  const toastLiveExample5 = document.getElementById("liveToast5");
  if (toastTrigger5) {
    toastTrigger5.addEventListener("click", () => {
      const toast = new bootstrap.Toast(toastLiveExample5);

      toast.show();
    });
  }
  const toastTrigger6 = document.getElementById("liveToastBtn6");
  const toastLiveExample6 = document.getElementById("liveToast6");
  if (toastTrigger6) {
    toastTrigger6.addEventListener("click", () => {
      const toast = new bootstrap.Toast(toastLiveExample6);

      toast.show();
    });
  }

    // Loader Functions
    function showLoader(type) {
        switch(type) {
            case 'spinner':
                Swal.fire({
                    title: 'Loading...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    Swal.close();
                }, 2000);
                break;

            case 'pulse':
                Swal.fire({
                    title: 'Processing...',
                    html: '<div class="pulse-loader"></div>',
                    allowOutsideClick: false,
                    showConfirmButton: false
                });
                setTimeout(() => {
                    Swal.close();
                }, 2000);
                break;

            case 'dots':
                Swal.fire({
                    title: 'Loading...',
                    html: '<div class="dots-loader"><span></span><span></span><span></span></div>',
                    allowOutsideClick: false,
                    showConfirmButton: false
                });
                setTimeout(() => {
                    Swal.close();
                }, 2000);
                break;

            case 'fullpage':
                const fullPageLoader = document.querySelector('.full-page-loader');
                fullPageLoader.style.display = 'flex';
                setTimeout(() => {
                    fullPageLoader.style.display = 'none';
                }, 2000);
                break;

            case 'progress':
                Swal.fire({
                    title: 'Uploading...',
                    html: '<div class="progress-loader"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div></div>',
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                let progress = 0;
                const interval = setInterval(() => {
                    progress += 10;
                    const progressBar = document.querySelector('.progress-bar');
                    if (progressBar) {
                        progressBar.style.width = progress + '%';
                    }
                    if (progress >= 100) {
                        clearInterval(interval);
                        Swal.close();
                    }
                }, 200);
                break;

            case 'skeleton':
                Swal.fire({
                    title: 'Loading Content...',
                    html: '<div class="skeleton-loader"><div class="skeleton-header"></div><div class="skeleton-body"><div class="skeleton-line"></div><div class="skeleton-line"></div><div class="skeleton-line"></div></div></div>',
                    allowOutsideClick: false,
                    showConfirmButton: false
                });
                setTimeout(() => {
                    Swal.close();
                }, 2000);
                break;
        }
    }

</script>
@endpush
