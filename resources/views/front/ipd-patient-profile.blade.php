@extends('layouts.front.app')
@section('title','User Profile | Paracare+')
@section('content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>Patient Profile</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('public/front/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Patients</li>
            <li class="breadcrumb-item active">Joshiji J</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid starts-->
  <div class="container-fluid">
    <div class="user-profile">
      <div class="row">
        <!-- user profile first-style start-->
        <div class="col-sm-12">
          <div class="card hovercard text-center common-user-image bg-white">
            <div class="cardheader h-auto" style="background-image:none;">
              <div class="user-image">
                <div class="avatar">
                  <div class="common-align">
                    <div><img id="output" src="{{ asset('public/front/assets/images/dashboard-11/user/12.jpg') }}" alt="Profile Image">
                      <input type="file" accept="image/*" onchange="loadFile(event)">
                      <div class="icon-wrapper" id="cancelButton"><i class="icofont icofont-error"></i></div>
                      <div class="icon-wrapper"><i class="icofont icofont-pencil-alt-5"></i></div>
                    </div>
                    <div class="user-designation"><a target="_blank" class="text-dark" href="">Joshiji J</a>
                      <div class="desc" class="text-dark">Patient</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- user profile first-style end-->
        <div class="col-12">
          <div class="card user-bio">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12">
                  <div class="ttl-info text-start">
                    <h6> <i class="fa-solid fa-user-tie pe-2"></i> Bio</h6><span class="mb-sm-3">Over five years of experience creating visually attractive and user-friendly websites has given me a passion for creating unique and creative websites. skilled in fusing cutting-edge online technologies with beautiful design concepts to create amazing user experiences. robust history in front-end development, UI/UX, and graphic design. highly skilled at solving problems, has a keen eye for detail, and can collaborate with others in a hectic setting.</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-user pe-2"></i>Name</h6><span>Joshiji</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-user-shield pe-2"></i>Guardian Name</h6><span>Mr. Ramji</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-mars pe-2"></i>Gender</h6><span>Male</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-hourglass-half pe-2"></i>Age</h6><span>30 Year</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-phone pe-2"></i>Phone</h6><span>9960237919</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-envelope pe-2"></i>Email</h6><span>joshiji@email.com</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-id-card pe-2"></i>Patient Id</h6><span>1065</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-location-dot pe-2"></i>Address</h6><span>123, Main Street, City</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-user pe-2"></i>Username</h6><span>9960237919</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-key pe-2"></i>Password</h6><span>3o'e![P!</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-ring pe-2"></i>Married Status</h6><span>Single</span>
                  </div>
                </div>
                <div class="col-12">
                  <div class="common-flex justify-content-center">
                    <div class="social-media" data-intro="This is your social details">
                      <ul class="list-inline">
                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Facebook"><a href="https://www.facebook.com/" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Google+"><a href="https://accounts.google.com/" target="_blank"><i class="fa-brands fa-google-plus-g"></i></a></li>
                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="X (Twitter)"><a href="https://twitter.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Instagram"><a href="https://www.instagram.com/" target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="RSS"><a href="https://rss.app/" target="_blank"><i class="fa-solid fa-share-nodes"></i></a></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- user profile menu start-->
        <div class="col-12">
          <div class="row scope-bottom-wrapper user-profile-wrapper">
            <div class="col-xxl-3 user-xl-25 col-xl-4 box-col-4">
              <div class="card">
                <div class="card-body">
                  <ul class="sidebar-left-icons nav nav-pills" id="add-product-pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="cunsultant-register-project-tab" data-bs-toggle="pill" href="#cunsultant-register-project" role="tab" aria-controls="cunsultant-register-project" aria-selected="false">
                            <div class="nav-rounded">
                                <div class="product-icons"><i class="fa-solid fa-user"></i></div>
                            </div>
                            <div class="product-tab-content">
                                <h6> Consultant Register</h6>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="diagnosis-project-tab" data-bs-toggle="pill" href="#diagnosis-project" role="tab" aria-controls="diagnosis-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Diagnosis</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link " id="timeline-project-tab" data-bs-toggle="pill" href="#timeline-project" role="tab" aria-controls="timeline-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-timeline"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Timeline</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="prescription-project-tab" data-bs-toggle="pill" href="#prescription-project" role="tab" aria-controls="prescription-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Prescription</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="pathology-project-tab" data-bs-toggle="pill" href="#pathology-project" role="tab" aria-controls="pathology-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Pathology</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="radiology-project-tab" data-bs-toggle="pill" href="#radiology-project" role="tab" aria-controls="radiology-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Radiology</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="charges-project-tab" data-bs-toggle="pill" href="#charges-project" role="tab" aria-controls="charges-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Charges</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="payment-project-tab" data-bs-toggle="pill" href="#payment-project" role="tab" aria-controls="payment-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Payment</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="billing-project-tab" data-bs-toggle="pill" href="#billing-project" role="tab" aria-controls="billing-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Billing</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="live-consultation-project-tab" data-bs-toggle="pill" href="#live-consultation-project" role="tab" aria-controls="live-consultation-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Live Consultation</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="consolidated-project-tab" data-bs-toggle="pill" href="#consolidated-project" role="tab" aria-controls="consolidated-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Consolidated</h6>
                        </div></a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xxl-9 user-xl-75 col-xl-8 box-col-8e">
              <div class="row">
                <div class="col-12">
                  <div class="tab-content" id="add-product-pills-tabContent">
                    <div class="tab-pane fade show active" id="cunsultant-register-project" role="tabpanel" aria-labelledby="cunsultant-register-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Cunsultant Register</h5>
                            </div>
                            <div class="card-body dark-timeline">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="consultant-register-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Applied Date</th>
                                                <th>Hospital</th>
                                                <th>Appointment Date</th>
                                                <th>Cunsultant</th>
                                                <th>Refrence</th>
                                                <th>Symptoms</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>123456 </td>
                                                <td>Sakhuja Hospital</td>
                                                <td>2025-06-05</td>
                                                <td>Dr. John Doe</td>
                                                <td>Dr. John Doe</td>
                                                <td>Skin Scars</td>
                                                <td>
                                                <ul class="action mb-0">
                                                    <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa-regular fa-eye"></i></a></li>
                                                    <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                    <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                                </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>123456 </td>
                                                <td>Sakhuja Hospital</td>
                                                <td>2025-06-05</td>
                                                <td>Dr. John Doe</td>
                                                <td>Dr. John Doe</td>
                                                <td>Skin Scars</td>
                                                <td>
                                                <ul class="action mb-0">
                                                    <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa-regular fa-eye"></i></a></li>
                                                    <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                    <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                                </ul>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="diagnosis-project" role="tabpanel" aria-labelledby="diagnosis-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Diagnosis</h5>
                            </div>
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="diagnosis-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>
                                            Report Type</th>
                                            <th>Hospital</th>
                                            <th>Report Date</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Histopathology</td>
                                            <td>Sakhuja Hospital</td>
                                            <td>2025-06-05</td>
                                            <td>Skin Scars</td>
                                            <td>
                                            <ul class="action mb-0">
                                                <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                            </ul>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="timeline-project" role="tabpanel" aria-labelledby="timeline-project-tab">
                        <div class="notification">
                        <div class="card">
                            <div class="card-header">
                            <h5>Recent Activity</h5>
                            </div>
                            <div class="card-body dark-timeline">
                            <ul>
                                <li class="d-flex">
                                <div class="activity-dot-primary"></div>
                                <div class="w-100 ms-3">
                                    <p class="d-flex justify-content-between mb-2"><span class="date-content light-background">12th Feb, 2024 </span><span>Today</span></p>
                                    <h6>Freelance Project Discussion<span class="dot-notification"></span></h6><span class="c-o-light">worked hard with the client to make sure the design reflects their objectives and brand identity.</span><span class="c-o-light">Optimised the website for quicker loads by implementing a responsive layout.</span>
                                </div>
                                </li>
                                <li class="d-flex">
                                <div class="activity-dot-warning"></div>
                                <div class="w-100 ms-3">
                                    <p class="d-flex justify-content-between mb-2"><span class="date-content light-background">12th Feb, 2024 </span><span>02:00 PM</span></p>
                                    <h6>Brand Collaboration<span class="dot-notification"></span></h6><span class="c-o-light">improved the user experience by using a sleek, contemporary style that matches the brand's urban, smart look.</span><span class="c-o-light">Multimedia components, including infographic and films, were used to improve user interaction and communicate the campaign's impact.</span>
                                    <div class="recent-images">
                                    <ul>
                                        <li>
                                        <div class="recent-img-wrap"><img src="{{ asset('public/front/assets/images/dashboard-2/order/sub-product/4.png') }}" alt="chair"></div>
                                        </li>
                                        <li>
                                        <div class="recent-img-wrap"><img src="{{ asset('public/front/assets/images/dashboard-2/order/sub-product/8.png') }}" alt="neckless"></div>
                                        </li>
                                        <li>
                                        <div class="recent-img-wrap"><img src="{{ asset('public/front/assets/images/dashboard-2/order/sub-product/11.png')}}" alt="slipper"></div>
                                        </li>
                                        <li>
                                        <div class="recent-img-wrap"><img src="{{ asset('public/front/assets/images/dashboard-2/order/sub-product/7.png')}}" alt="earings"></div>
                                        </li>
                                        <li>
                                        <div class="recent-img-wrap"><img src="{{ asset('public/front/assets/images/dashboard-2/order/sub-product/3.png')}}" alt="men t-shirt"></div>
                                        </li>
                                        <li>
                                        <div class="recent-img-wrap"><img src="{{ asset('public/front/assets/images/dashboard-2/order/sub-product/9.png')}}" alt="men shorts"></div>
                                        </li>
                                    </ul>
                                    </div>
                                </div>
                                </li>
                                <li class="d-flex">
                                <div class="activity-dot-primary"></div>
                                <div class="w-100 ms-3">
                                    <p class="d-flex justify-content-between mb-2"><span class="date-content light-background">08th Feb, 2024 </span><span>5 days ago</span></p>
                                    <h6>Review of Project and Milestones<span class="dot-notification"></span></h6><span class="c-o-light">Having the objective of developing an aesthetically attractive and intuitive e-commerce platform for "Multikart and Fastkart."</span><span class="c-o-light">This entails being aware of the target market, the brand's goal, and the particular features that the website must have.</span>
                                </div>
                                </li>
                                <li class="d-flex">
                                <div class="activity-dot-warning"></div>
                                <div class="w-100 ms-3">
                                    <p class="d-flex justify-content-between mb-2"><span class="date-content light-background">05th Feb, 2024 </span><span>8 days ago</span></p>
                                    <h6>Wireframing Designs<span class="dot-notification"></span></h6><span class="c-o-light mb-1">Any type of group project could have a central idea. Transfer information using the theme so that members of your team can comprehend it.</span>
                                    <div class="project-teammate">
                                    <ul class="common-f-start">
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Sarah Wilson"><img class="common-circle" src="{{ asset('public/front/assets/images/dashboard-11/user/11.jpg') }}" alt="user"></li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Richard Taylor"><img class="common-circle" src="{{ asset('public/front/assets/images/dashboard-11/user/9.jpg') }}" alt="user"></li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Manuel Gilmore">
                                        <div class="common-circle bg-lighter-secondary">M</div>
                                        </li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Jessica Anderson"><img class="common-circle" src="{{ asset('public/front/assets/images/dashboard-11/user/3.jpg') }}" alt="user"></li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="9+ More">
                                        <div class="common-circle bg-lighter-primary">9+</div>
                                        </li>
                                    </ul>
                                    </div>
                                </div>
                                </li>
                            </ul>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="prescription-project" role="tabpanel" aria-labelledby="prescription-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Prescription</h5>
                            </div>
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="prescription-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>
                                            Report Type</th>
                                            <th>Hospital</th>
                                            <th>Report Date</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Histopathology</td>
                                            <td>Sakhuja Hospital</td>
                                            <td>2025-06-05</td>
                                            <td>Skin Scars</td>
                                            <td>
                                            <ul class="action mb-0">
                                                <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                            </ul>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pathology-project" role="tabpanel" aria-labelledby="pathology-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Pathology</h5>
                            </div>
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="pathology-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>
                                            Report Type</th>
                                            <th>Hospital</th>
                                            <th>Report Date</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Histopathology</td>
                                            <td>Sakhuja Hospital</td>
                                            <td>2025-06-05</td>
                                            <td>Skin Scars</td>
                                            <td>
                                            <ul class="action mb-0">
                                                <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                            </ul>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="radiology-project" role="tabpanel" aria-labelledby="radiology-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Radiology</h5>
                            </div>
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="radiology-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>
                                            Report Type</th>
                                            <th>Hospital</th>
                                            <th>Report Date</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Histopathology</td>
                                            <td>Sakhuja Hospital</td>
                                            <td>2025-06-05</td>
                                            <td>Skin Scars</td>
                                            <td>
                                            <ul class="action mb-0">
                                                <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                            </ul>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="charges-project" role="tabpanel" aria-labelledby="charges-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Charges</h5>
                            </div>
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="charges-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>
                                            Report Type</th>
                                            <th>Hospital</th>
                                            <th>Report Date</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Histopathology</td>
                                            <td>Sakhuja Hospital</td>
                                            <td>2025-06-05</td>
                                            <td>Skin Scars</td>
                                            <td>
                                            <ul class="action mb-0">
                                                <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                            </ul>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="payment-project" role="tabpanel" aria-labelledby="payment-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Payment</h5>
                            </div>
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="payment-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>
                                            Report Type</th>
                                            <th>Hospital</th>
                                            <th>Report Date</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Histopathology</td>
                                            <td>Sakhuja Hospital</td>
                                            <td>2025-06-05</td>
                                            <td>Skin Scars</td>
                                            <td>
                                            <ul class="action mb-0">
                                                <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                            </ul>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="billing-project" role="tabpanel" aria-labelledby="billing-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Billing</h5>
                            </div>
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="payment-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>
                                            Report Type</th>
                                            <th>Hospital</th>
                                            <th>Report Date</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Histopathology</td>
                                            <td>Sakhuja Hospital</td>
                                            <td>2025-06-05</td>
                                            <td>Skin Scars</td>
                                            <td>
                                            <ul class="action mb-0">
                                                <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                            </ul>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="live-consultation-project" role="tabpanel" aria-labelledby="live-consultation-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Live Consultation</h5>
                            </div>
                            <div class="card-body">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="live-consultation-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Consultation Title</th>
                                                <th>Date</th>
                                                <th>Created By</th>
                                                <th>Created For</th>
                                                <th>Patient</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Regarding Acne</td>
                                                <td>2025-06-05</td>
                                                <td>Dr. John Doe</td>
                                                <td>Dr. John Doe</td>
                                                <td>Dr. John Doe</td>
                                                <td><span class="badge bg-warning">Pending</span></td>
                                                <td>
                                                    <ul class="action mb-0">
                                                        <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa-regular fa-eye"></i></a></li>
                                                        <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                        <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="consolidated-project" role="tabpanel" aria-labelledby="consolidated-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>User Logs</h5>
                            </div>
                            <div class="card-body">
                                <h4 class="mb-3 text-info">Vitals</h4>
                                <div class="table-responsive custom-scrollbar mb-4">
                                    <table class="table border-bottom-table">
                                    <thead >
                                        <tr class="border-bottom-primary">
                                        <th>SN.</th>
                                        <th>Date</th>
                                        <th>Systolic BP (mmhg)</th>
                                        <th>Diastolic BP (mmhg)</th>
                                        <th>Respiration</th>
                                        <th>Temperature (®F)</th>
                                        <th>Pluse (BPL)</th>
                                        <th>Diabetes (mmol/l)</th>
                                        <th>Height (Feet)</th>
                                        <th>Weight (Kg)</th>
                                        <th>BMI</th>
                                        <th>By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="11">No Data Found.</td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </div>
                                <h4 class="mb-3 text-info">Presciption</h4>
                                <div class="table-responsive custom-scrollbar mb-4">
                                    <table class="table border-bottom-table">
                                    <thead>
                                        <tr class="border-bottom-primary">
                                            <th scope="col">SN.</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">OPD</th>
                                            <th scope="col">Consultant</th>
                                            <th scope="col">Refrence</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <tr>
                                            <td>1</td>
                                            <td>2025-06-05</td>
                                            <td>123456</td>
                                            <td>Dr. John Doe</td>
                                            <td>561561</td>
                                            <td>
                                            <ul class="action mb-0">
                                                        <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa fa-file-pdf"></i></a></li>
                                                        <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                        <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                                    </ul>
                                            </td>
                                       </tr>
                                       <tr>
                                            <td>1</td>
                                            <td>2025-06-05</td>
                                            <td>123456</td>
                                            <td>Dr. John Doe</td>
                                            <td>561561</td>
                                            <td>
                                            <ul class="action mb-0">
                                                        <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa fa-file-pdf"></i></a></li>
                                                        <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                        <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                                    </ul>
                                            </td>
                                       </tr>
                                    </tbody>
                                    </table>
                                </div>
                                <h4 class="mb-3 text-info">Pathology</h4>
                                <div class="table-responsive custom-scrollbar mb-4">
                                    <table class="table border-bottom-table">
                                    <thead>
                                        <tr class="border-bottom-primary">
                                            <th scope="col">SN.</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">OPD</th>
                                            <th scope="col">Consultant</th>
                                            <th scope="col">Test Report</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <tr>
                                            <td>1</td>
                                            <td>2025-06-05</td>
                                            <td>123456</td>
                                            <td>Dr. John Doe</td>
                                            <td>
                                            <ul class="action mb-0">
                                                        <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa fa-file-pdf"></i></a></li>
                                                        <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                        <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                                    </ul>
                                            </td>                                       </tr>
                                       <tr>
                                            <td>1</td>
                                            <td>2025-06-05</td>
                                            <td>123456</td>
                                            <td>Dr. John Doe</td>
                                            <td>
                                            <ul class="action mb-0">
                                                        <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa fa-file-pdf"></i></a></li>
                                                        <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                        <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                                    </ul>
                                            </td>                                       </tr>
                                    </tbody>
                                    </table>
                                </div>
                                <h4 class="mb-3 text-info">Radiology</h4>
                                <div class="table-responsive custom-scrollbar mb-4">
                                    <table class="table border-bottom-table">
                                    <thead>
                                        <tr class="border-bottom-primary">
                                            <th scope="col">SN.</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">OPD</th>
                                            <th scope="col">Consultant</th>
                                            <th scope="col">Test Report</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <tr>
                                            <td>1</td>
                                            <td>2025-06-05</td>
                                            <td>123456</td>
                                            <td>Dr. John Doe</td>
                                            <td>
                                            <ul class="action mb-0">
                                                        <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa fa-file-pdf"></i></a></li>
                                                        <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                        <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                                    </ul>
                                            </td>                                       </tr>
                                       <tr>
                                            <td>1</td>
                                            <td>2025-06-05</td>
                                            <td>123456</td>
                                            <td>Dr. John Doe</td>
                                            <td>
                                            <ul class="action mb-0">
                                                        <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa fa-file-pdf"></i></a></li>
                                                        <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                        <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                                    </ul>
                                            </td>                                       </tr>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/jquery.dataTables.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/dataTables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/autoFill.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/keyTable.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/buttons.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/fixedHeader.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/responsive.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/rowReorder.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('public/front/assets/css/vendors/flatpickr/flatpickr.min.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('public/front/assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatables/dataTables1.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatables/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.autoFill.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/autoFill.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.keyTable.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/keyTable.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.buttons.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.fixedHeader.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/fixedHeader.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/jszip.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/pdfmake.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/vfs_fonts.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.html5.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/buttons.print.min.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.responsive.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/responsive.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/dataTables.rowReorder.js') }}"></script>
<script src="{{ asset('public/front/assets/js/datatable/datatable-extension/rowReorder.bootstrap5.js') }}"></script>
<script src="{{ asset('public/front/assets/js/flat-pickr/flatpickr.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    var table = $('#consultant-register-table,#prescription-table,#diagnosis-table,#pathology-table,#radiology-table,#charges-table,#payment-table,#billing-table,#prescription-table,#live-consultation-table').DataTable({
      dom: "fBrtip",
      buttons: [
        { extend: 'copy', className: 'buttons-copy btn btn-light', text: '<i class=\"fa fa-copy\"></i>', titleAttr: 'Copy' },
        { extend: 'csv', className: 'buttons-csv btn btn-info', text: '<i class=\"fa fa-file-csv\"></i>', titleAttr: 'Export as CSV' },
        { extend: 'excel', className: 'buttons-excel btn btn-success', text: '<i class=\"fa fa-file-excel\"></i>', titleAttr: 'Export as Excel' },
        { extend: 'pdf', className: 'buttons-pdf btn btn-danger', text: '<i class=\"fa fa-file-pdf\"></i>', titleAttr: 'Export as PDF' },
        { extend: 'print', className: 'buttons-print btn btn-primary', text: '<i class=\"fa fa-print\"></i>', titleAttr: 'Print Table' },
        { extend: 'colvis', className: 'buttons-colvis btn btn-dark', text: '<i class=\"fa fa-columns\"></i>', titleAttr: 'Column Visibility' }
      ],
      language: {
        search: '',
        searchPlaceholder: 'Search Leaves...'
      },
      lengthChange: true,
      paging: true,
      info: true,
      ordering: true,
      scrollX: true,
      autoWidth: true,
      responsive: true
    });

    $(table.table().container()).find('.dataTables_filter input').addClass('form-control').css({'width':'300px','display':'inline-block'});
    $('[data-bs-toggle="tooltip"]').tooltip();
    // Flatpickr init for date fields
    flatpickr('input[type="date"]', { dateFormat: 'd-m-Y' });
    flatpickr('input[type="datetime-local"]', { enableTime: true, dateFormat: 'd-m-Y H:i' });
  });

  function deleteVisitor(e) {
    e.preventDefault();
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
        Swal.fire('Deleted!', 'Leaves has been deleted.', 'success');
      }
    });
  }
  $(document).ready(function() {
    // Initialize DataTables for each table
    var table2 = $('#example2').DataTable({ responsive: true });
    // Trigger adjustment after tab is shown
    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function (event) {
        let tabId = $(event.target).attr('href');
        if (tabId === '#visits-project') {
            setTimeout(() => {
                $('#visits-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#diagnosis-project') {
            setTimeout(() => {
                $('#diagnosis-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#prescription-project') {
            setTimeout(() => {
                $('#prescription-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#pathology-project') {
            setTimeout(() => {
                $('#pathology-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#radiology-project') {
            setTimeout(() => {
                $('#radiology-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#charges-project') {
            setTimeout(() => {
                $('#charges-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#payment-project') {
            setTimeout(() => {
                $('#payment-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#billing-project') {
            setTimeout(() => {
                $('#billing-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#live-consultation-project') {
            setTimeout(() => {
                $('#live-consultation-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        }
    });
});
</script>
@endpush