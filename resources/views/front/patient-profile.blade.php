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
                        <a class="nav-link active" id="visits-project-tab" data-bs-toggle="pill" href="#visits-project" role="tab" aria-controls="visits-project" aria-selected="false">
                            <div class="nav-rounded">
                                <div class="product-icons"><i class="fa-solid fa-user"></i></div>
                            </div>
                            <div class="product-tab-content">
                                <h6>Visits</h6>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item"> <a class="nav-link " id="timeline-project-tab" data-bs-toggle="pill" href="#timeline-project" role="tab" aria-controls="timeline-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-timeline"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Timeline</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="diagnosis-project-tab" data-bs-toggle="pill" href="#diagnosis-project" role="tab" aria-controls="diagnosis-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Diagnosis</h6>
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
                    <div class="tab-pane fade show active" id="visits-project" role="tabpanel" aria-labelledby="visits-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Visits</h5>
                            </div>
                            <div class="card-body dark-timeline">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="visits-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>OPD No</th>
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
                                                    <li class="view" data-bs-toggle="tooltip" title="health"><a href="#"  data-bs-toggle="modal" data-bs-target="#vitalsSocialModal"><i class="text-danger fa-solid fa-heart"></i></a></li>
                                                        <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa-regular fa-eye"></i></a></li>
                                                        <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>

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
                                                        <li class="view"><a href="#" data-bs-toggle="tooltip" title="health" data-bs-toggle="modal" data-bs-target="#vitalsSocialModal" ><i class="text-danger fa-solid fa-heart"></i></a></li>
                                                        <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa-regular fa-eye"></i></a></li>
                                                        <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>

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
  <div class="modal fade" id="vitalsSocialModal" tabindex="-1" aria-labelledby="vitalsSocialModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="vitalsSocialModalLabel">Vitals & Social</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs mb-3" id="vitalsSocialTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="vitals-tab" data-bs-toggle="tab" data-bs-target="#vitalsTab" type="button" role="tab">Vitals</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#socialTab" type="button" role="tab">Social</button>
          </li>
        </ul>
        <div class="tab-content" id="vitalsSocialTabContent">
          <!-- Vitals Tab -->
          <div class="tab-pane fade show active" id="vitalsTab" role="tabpanel">
            <div class="row g-3 align-items-center justify-content-center">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-3 d-flex flex-column align-items-center">
                            <div class="vital-box vital-orange mb-4 w-100">
                            <div class="d-flex align-items-center mb-2"><i class="fa-solid fa-heart me-2"></i>Respiration</div>
                            <div class="d-flex align-items-center mb-2">

                                <input type="number" class="form-control vital-input"  />
                                <span class="text-dark text-lg fw-normal">Raspiration</span>
                            </div>
                            </div>
                        <div class="vital-box vital-green w-100">
                            <div class="d-flex align-items-center mb-2"><i class="fa-solid fa-heart me-2"></i>Diabetes</div>
                            <div class="d-flex align-items-center mb-2">
                                <input type="number" class="form-control vital-input"  />
                                <span class="text-dark text-lg fw-normal">mmol/l</span>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div id="svg-man-placeholder" class="mb-4" style="display:flex;align-items:center;justify-content:center;">
                            <!-- SVG man goes here -->
                                <div class="human-body">
                                    <svg data-position="head" class="head" xmlns="http://www.w3.org/2000/svg" width="56.594" height="95.031" viewBox="0 0 56.594 95.031"><path d="M15.92 68.5l8.8 12.546 3.97 13.984-9.254-7.38-4.622-15.848zm27.1 0l-8.8 12.546-3.976 13.988 9.254-7.38 4.622-15.848zm6.11-27.775l.108-11.775-21.16-14.742L8.123 26.133 8.09 40.19l-3.24.215 1.462 9.732 5.208 1.81 2.36 11.63 9.72 11.018 10.856-.324 9.56-10.37 1.918-11.952 5.207-1.81 1.342-9.517zm-43.085-1.84l-.257-13.82L28.226 11.9l23.618 15.755-.216 10.37 4.976-17.085L42.556 2.376 25.49 0 10.803 3.673.002 24.415z"/></svg>
                                    <svg data-position="shoulder" class="shoulder" xmlns="http://www.w3.org/2000/svg" width="109.532" height="46.594" viewBox="0 0 109.532 46.594"><path d="M38.244-.004l1.98 9.232-11.653 2.857-7.474-2.637zm33.032 0l-1.98 9.232 11.653 2.857 7.474-2.637zm21.238 10.54l4.044-2.187 12.656 14 .07 5.33S92.76 10.66 92.515 10.535zm-1.285.58c-.008.28 17.762 18.922 17.762 18.922l.537 16.557-6.157-10.55L91.5 30.988 83.148 15.6zm-74.224-.58L12.962 8.35l-12.656 14-.062 5.325s16.52-17.015 16.764-17.14zm1.285.58C18.3 11.396.528 30.038.528 30.038L-.01 46.595l6.157-10.55 11.87-5.056L26.374 15.6z"/></svg>
                                    <svg data-position="arm" class="arm" xmlns="http://www.w3.org/2000/svg" width="156.344" height="119.25" viewBox="0 0 156.344 119.25"><path d="M21.12 56.5a1.678 1.678 0 0 1-.427.33l.935 8.224 12.977-13.89 1.2-8.958A168.2 168.2 0 0 0 21.12 56.5zm1.387 12.522l-18.07 48.91 5.757 1.333 19.125-39.44 3.518-22.047zm-5.278-18.96l2.638 18.74-17.2 46.023L.01 113.05l6.644-35.518zm118.015 6.44a1.678 1.678 0 0 0 .426.33l-.934 8.222-12.977-13.89-1.2-8.958A168.2 168.2 0 0 1 135.24 56.5zm-1.39 12.52l18.073 48.91-5.758 1.333-19.132-39.44-3.52-22.05zm5.28-18.96l-2.64 18.74 17.2 46.023 2.658-1.775-6.643-35.518zm-103.1-12.323a1.78 1.78 0 0 1 .407-.24l3.666-27.345L33.07.015l-7.258 10.58-6.16 37.04.566 4.973a151.447 151.447 0 0 1 15.808-14.87zm84.3 0a1.824 1.824 0 0 0-.407-.24l-3.666-27.345L123.3.015l7.258 10.58 6.16 37.04-.566 4.973a151.447 151.447 0 0 0-15.822-14.87zM22.288 8.832l-3.3 35.276-2.2-26.238zm111.79 0l3.3 35.276 2.2-26.238z"/></svg>
                                    <svg data-position="cheast" class="cheast" xmlns="http://www.w3.org/2000/svg" width="86.594" height="45.063" viewBox="0 0 86.594 45.063"><path d="M19.32 0l-9.225 16.488-10.1 5.056 6.15 4.836 4.832 14.07 11.2 4.616 17.85-8.828-4.452-34.7zm47.934 0l9.225 16.488 10.1 5.056-6.15 4.836-4.833 14.07-11.2 4.616-17.844-8.828 4.45-34.7z"/></svg>
                                    <svg data-position="stomach" class="stomach" xmlns="http://www.w3.org/2000/svg" width="75.25" height="107.594" viewBox="0 0 75.25 107.594"><path d="M19.25 7.49l16.6-7.5-.5 12.16-14.943 7.662zm-10.322 8.9l6.9 3.848-.8-9.116zm5.617-8.732L1.32 2.15 6.3 15.6zm-8.17 9.267l9.015 5.514 1.54 11.028-8.795-5.735zm15.53 5.89l.332 8.662 12.286-2.665.664-11.826zm14.61 84.783L33.28 76.062l-.08-20.53-11.654-5.736-1.32 37.5zM22.735 35.64L22.57 46.3l11.787 3.166.166-16.657zm-14.16-5.255L16.49 35.9l1.1 11.25-8.8-7.06zm8.79 22.74l-9.673-7.28-.84 9.78L-.006 68.29l10.564 14.594 5.5.883 1.98-20.735zM56 7.488l-16.6-7.5.5 12.16 14.942 7.66zm10.32 8.9l-6.9 3.847.8-9.116zm-5.617-8.733L73.93 2.148l-4.98 13.447zm8.17 9.267l-9.015 5.514-1.54 11.03 8.8-5.736zm-15.53 5.89l-.332 8.662-12.285-2.665-.664-11.827zm-14.61 84.783l3.234-31.536.082-20.532 11.65-5.735 1.32 37.5zm13.78-71.957l.166 10.66-11.786 3.168-.166-16.657zm14.16-5.256l-7.915 5.514-1.1 11.25 8.794-7.06zm-8.79 22.743l9.673-7.28.84 9.78 6.862 12.66-10.564 14.597-5.5.883-1.975-20.74z"/></svg>
                                    <svg data-position="legs" class="legs" xmlns="http://www.w3.org/2000/svg" width="93.626" height="286.625" viewBox="0 0 93.626 286.625"><path d="M17.143 138.643l-.664 5.99 4.647 5.77 1.55 9.1 3.1 1.33 2.655-13.755 1.77-4.88-1.55-3.107zm20.582.444l-3.32 9.318-7.082 13.755 1.77 12.647 5.09-14.2 4.205-7.982zm-26.557-12.645l5.09 27.29-3.32-1.777-2.656 8.875zm22.795 42.374l-1.55 4.88-3.32 20.634-.442 27.51 4.65 26.847-.223-34.39 4.87-13.754.663-15.087zM23.34 181.24l1.106 41.267 8.853 33.28-9.628-4.55-16.045-57.8 5.533-36.384zm15.934 80.536l-.664 18.415-1.55 6.435h-4.647l-1.327-4.437-1.55-.222.332 4.437-5.864-1.778-1.55-.887-6.64-1.442-.22-5.214 6.418-10.87 4.426-5.548 10.844-4.437zM13.63 3.076v22.476l15.71 31.073 9.923 30.85L38.23 66.1zm25.49 30.248l.118-.148-.793-2.024L21.9 12.992l-1.242-.44L31.642 40.93zM32.865 44.09l6.812 17.6 2.274-21.596-1.344-3.43zM6.395 61.91l.827 25.34 12.816 35.257-3.928 10.136L3.5 88.133zM30.96 74.69l.345.826 6.47 15.48-4.177 38.342-6.594-3.526 5.715-35.7zm45.5 63.953l.663 5.99-4.647 5.77-1.55 9.1-3.1 1.33-2.655-13.755-1.77-4.88 1.55-3.107zm-20.582.444l3.32 9.318 7.08 13.755-1.77 12.647-5.09-14.2-4.2-7.987zm3.762 29.73l1.55 4.88 3.32 20.633.442 27.51-4.648 26.847.22-34.39-4.867-13.754-.67-15.087zm10.623 12.424l-1.107 41.267-8.852 33.28 9.627-4.55 16.046-57.8-5.533-36.384zM54.33 261.777l.663 18.415 1.546 6.435h4.648l1.328-4.437 1.55-.222-.333 4.437 5.863-1.778 1.55-.887 6.638-1.442.222-5.214-6.418-10.868-4.426-5.547-10.844-4.437zm25.643-258.7v22.476L64.26 56.625l-9.923 30.85L55.37 66.1zM54.48 33.326l-.118-.15.793-2.023L71.7 12.993l1.24-.44L61.96 40.93zm6.255 10.764l-6.812 17.6-2.274-21.595 1.344-3.43zm26.47 17.82l-.827 25.342-12.816 35.256 3.927 10.136 12.61-44.51zM62.64 74.693l-.346.825-6.47 15.48 4.178 38.342 6.594-3.527-5.715-35.7zm19.792 51.75l-5.09 27.29 3.32-1.776 2.655 8.875zM9.495-.007l.827 21.373-7.028 42.308-3.306-34.155zm2.068 27.323L26.24 59.707l3.307 26-6.2 36.58L9.91 85.046l-.827-38.342zM84.103-.01l-.826 21.375 7.03 42.308 3.306-34.155zm-2.066 27.325L67.36 59.707l-3.308 26 6.2 36.58 13.436-37.24.827-38.34z"/></svg>
                                    <svg data-position="hands" class="hands" xmlns="http://www.w3.org/2000/svg" width="205" height="38.938" viewBox="0 0 205 38.938"><path d="M21.255-.002l2.88 6.9 8.412 1.335.664 12.458-4.427 17.8-2.878-.22 2.8-11.847-2.99-.084-4.676 12.6-3.544-.446 4.4-12.736-3.072-.584-5.978 13.543-4.428-.445 6.088-14.1-2.1-1.25-7.528 12.012-3.764-.445L12.4 12.9l-1.107-1.78L.665 15.57 0 13.124l8.635-7.786zm162.49 0l-2.88 6.9-8.412 1.335-.664 12.458 4.427 17.8 2.878-.22-2.8-11.847 2.99-.084 4.676 12.6 3.544-.446-4.4-12.736 3.072-.584 5.978 13.543 4.428-.445-6.088-14.1 2.1-1.25 7.528 12.012 3.764-.445L192.6 12.9l1.107-1.78 10.628 4.45.665-2.447-8.635-7.786z"/></svg>
                                </div>
                            </div>
                            <div id="area">
                                Area: <span id="data"></span>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex flex-column align-items-center">
                            <div class="vital-box vital-red mb-4 w-100">
                            <div class="d-flex align-items-center mb-2"><i class="fa-solid fa-heart me-2"></i>Pulse</div>
                            <div class="d-flex align-items-center mb-2">
                                <input type="number" class="form-control vital-input"  />
                                <span class="text-dark text-lg fw-normal">BPL</span>
                            </div>

                            </div>
                            <div class="vital-box vital-green mb-4 w-100">
                            <div class="d-flex align-items-center mb-2"><i class="fa-solid fa-heart me-2"></i>Systolic BP</div>
                            <div class="d-flex align-items-center mb-2">
                                <input type="number" class="form-control vital-input"  />
                                <span class="text-dark text-lg fw-normal">mmhg</span>
                            </div>
                            </div>
                            <div class="vital-box vital-green mb-4 w-100">
                            <div class="d-flex align-items-center mb-2"><i class="fa-solid fa-heart me-2"></i>Diastolic BP</div>
                            <div class="d-flex align-items-center mb-2">

                            <input type="number" class="form-control vital-input"  />
                            <span class="text-dark text-lg fw-normal">BP</span>

                            </div>
                            </div>
                            <div class="vital-box vital-red w-100">
                            <div class="d-flex align-items-center mb-2"><i class="fa-solid fa-heart me-2"></i>Temperature</div>
                            <div class="d-flex align-items-center mb-2">

                            <input type="number" class="form-control vital-input" />
                            <span class="text-dark text-lg fw-normal">°F</span>

                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex flex-column align-items-center">
                    <h3 class="mb-4">BMI Calculator</h3>
                    <div class="vital-box vital-blue mb-3 w-100">
                        <div class="d-flex align-items-center mb-2"><i class="fa-solid fa-arrows-up-down me-2"></i>Height</div>
                        <div class="d-flex align-items-center mb-2">
                            <input type="number" class="form-control vital-input" />
                            <span class="text-dark text-lg fw-normal">Feet</span>
                        </div>
                    </div>
                    <div class="vital-box vital-blue mb-3 w-100">
                        <div class="d-flex align-items-center mb-2"><i class="fa-solid fa-circle-half-stroke me-2"></i>Weight</div>
                        <div class="d-flex align-items-center mb-2">
                            <input type="number" class="form-control vital-input" />
                            <span class="text-dark text-lg fw-normal">Kg</span>
                        </div>
                    </div>
                    <div class="vital-box vital-orange w-100">
                    <div class="d-flex align-items-center mb-2"><i class="fa-solid fa-calculator me-2"></i>BMI</div>
                    <div class="d-flex align-items-center mb-2">

                    <input type="number" class="form-control vital-input"  />
                    <span class="text-dark text-lg fw-normal">BMI</span>

                    </div>
                    </div>
                </div>
            </div>
          </div>
          <!-- Social Tab -->
          <div class="tab-pane fade" id="socialTab" role="tabpanel">
            <form id="socialForm">
              <div class="row">
                <div class="col-md-6">
                    <div class="shadow rounded mb-3">
                        <div class="p-4">
                            <label class="form-label fw-bold text-success d-block mb-4">Any Known Allergies</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="allergyNut">
                                <label class="form-check-label" for="allergyNut">Nut Allergies</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="allergyLactose">
                                <label class="form-check-label" for="allergyLactose">Lactose Intolerant</label>
                            </div>
                        </div>
                    </div>
                    <div class="shadow rounded mb-3">
                        <div class="p-4">
                            <label class="form-label fw-bold text-success d-block mb-4">Any Allergic Reaction To</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="reactionPeanut">
                                <label class="form-check-label" for="reactionPeanut">Peanut</label>
                            </div>
                        </div>
                    </div>
                    <div class="shadow rounded mb-3">
                        <div class="p-4">
                            <label class="form-label fw-bold text-success d-block mb-4">Social History</label>
                            <div class="row g-2 mb-2">
                                <div class="col-md-4"><input type="text" class="form-control" placeholder="Occupation" /></div>
                                <div class="col-md-4"><select class="form-select"><option>Married</option></select></div>
                                <div class="col-md-4"><input type="text" class="form-control" placeholder="Place of Birth" /></div>
                                <div class="col-md-4"><input type="text" class="form-control" placeholder="Current Location" /></div>
                                <div class="col-md-4"><input type="text" class="form-control" placeholder="Years in Current Location" /></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="shadow rounded mb-3">
                        <div class="p-4">
                            <label class="form-label fw-bold text-success d-block mb-4">Habits</label>
                            <div id="habitsRows">
                            <div class="row g-2 mb-2 habit-row">
                                <div class="col-md-5">
                                <select class="form-select" name="habit[]"><option>Select</option></select>
                                </div>
                                <div class="col-md-5">
                                <select class="form-select" name="habit_status[]"><option>Status</option></select>
                                </div>
                                <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-outline-primary btn-sm add-habit me-1"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-habit d-none"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="shadow rounded mb-3">
                        <div class="p-4">
                            <label class="form-label fw-bold text-success d-block mb-4">Family History</label>
                            <div id="familyRows">
                            <div class="row g-2 mb-2 family-row">
                                <div class="col-md-2"><select class="form-select" name="disease[]"><option>Disease</option></select></div>
                                <div class="col-md-2"><select class="form-select" name="relation[]"><option>Relation</option></select></div>
                                <div class="col-md-2"><input type="text" class="form-control" name="age[]" placeholder="Age" /></div>
                                <div class="col-md-4"><input type="text" class="form-control" name="comments[]" placeholder="Comments" /></div>
                                <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-outline-primary btn-sm add-family me-1"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-family d-none"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
              <div class="text-end">
                <button type="submit" class="btn btn-success">Save</button>
              </div>
            </form>
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
<style>
       .vital-box {
        border-radius: 12px;
        padding: 18px 20px 10px 20px;
        margin-bottom: 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        background: #fff;
        min-width: 180px;
        min-height: 90px;
        font-weight: 500;
        font-size: 1.1rem;
    }
    .vital-orange { background: linear-gradient(90deg, #fff7e6 0%, #fbb040 100%); }
    .vital-red { background: linear-gradient(90deg, #fff0f0 0%, #f77c7c 100%); }
    .vital-green { background: linear-gradient(90deg, #f0fff0 0%, #7cf77c 100%); }
    .vital-blue { background: linear-gradient(90deg, #e6f0ff 0%, #40a1fb 100%); }
    .vital-input { font-size: 1.2rem; font-weight: 600; border: none; background: transparent; border-bottom: 1px dashed #bbb; border-radius: 0; text-align: right; }
    .vital-input:focus { outline: none; box-shadow: none; border-color: #007bff; background: #fff; }

    .vital-box input.form-control.vital-input {
    color: #00000085;
    background: transparent;
    border: none;
    border-bottom: 2px dashed;
    display: inline-block;
    flex: 1;
    border-radius: 0;
}

    .human-body {
        width: 207px;
    position: relative;
    padding-top: 500px;
    height: 500px;
    display: block;
    margin: 20px auto;
}
.human-body svg:hover {
    cursor: pointer;
}
.human-body svg:hover path {
    fill: #ff7d16;
}
.human-body svg {
    position: absolute;
    left: 50%;
    fill: #57c9d5;
}
.human-body svg.head {
    margin-left: -28.5px;
    top: -6px;
}
.human-body svg.shoulder {
    margin-left: -53.5px;
    top: 69px;
}
.human-body svg.arm {
    margin-left: -78px;
    top: 112px;
}
.human-body svg.cheast {
    margin-left: -43.5px;
    top: 88px;
}
.human-body svg.stomach {
    margin-left: -37.5px;
    top: 130px;
}
.human-body svg.legs {
    margin-left: -46.5px;
    top: 205px;
    z-index: 9999;
}
.human-body svg.hands {
    margin-left: -102.5px;
    top: 224px;
}
#area {
    display: block;
    width: 100%;
    clear: both;
    padding: 10px;
    text-align: center;
    font-size: 25px;
    font-family: Courier New;
    color: #a5a5a5;
}

#area #data {
    color: black;
}
</style>
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
    var table = $('#visits-table').DataTable({
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
    var diagnosistable = $('#diagnosis-table').DataTable({
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
    var liveconsultationTable = $('#live-consultation-table').DataTable({
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
        } else {
            setTimeout(() => {
                $('#live-consultation').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        }
    });
});

    // Habits clone/remove
    $(document).on('click', '.add-habit', function() {
        var row = $(this).closest('.habit-row').clone();
        row.find('input,select').val('');
        row.find('.remove-habit').removeClass('d-none');
        $('#habitsRows').append(row);
    });

    $(document).on('click', '.remove-habit', function() {
        $(this).closest('.habit-row').remove();
    });

    // Family clone/remove
    $(document).on('click', '.add-family', function() {
        var row = $(this).closest('.family-row').clone();
        row.find('input,select').val('');
        row.find('.remove-family').removeClass('d-none');
        $('#familyRows').append(row);
    });

    $(document).on('click', '.remove-family', function() {
        $(this).closest('.family-row').remove();
    });

    window.onload = function () {
        const pieces = document.getElementsByTagName('svg');
        for (var i = 0; pieces.length; i++) {
            let _piece = pieces[i];
            _piece.onclick = function(t) {
                if (t.target.getAttribute('data-position') != null) document.getElementById('data').innerHTML = t.target.getAttribute('data-position');
                if (t.target.parentElement.getAttribute('data-position') != null) document.getElementById('data').innerHTML = t.target.parentElement.getAttribute('data-position');
            }
        }
    }
</script>
@endpush