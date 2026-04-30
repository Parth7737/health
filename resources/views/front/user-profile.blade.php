@extends('layouts.front.app')
@section('title','User Profile | Paracare+')
@section('content')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6">
          <h3>User Profile</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('public/front/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Users</li>
            <li class="breadcrumb-item active">User Profile</li>
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
          <div class="card hovercard text-center common-user-image">
            <div class="cardheader" style="background-image: url('{{ asset('public/front/assets/images/other-images/bg-profile.jpg') }}');">
              <div class="user-image">
                <div class="avatar">
                  <div class="common-align">
                    <div><img id="output" src="{{ asset('public/front/assets/images/dashboard-11/user/12.jpg') }}" alt="Profile Image">
                      <input type="file" accept="image/*" onchange="loadFile(event)">
                      <div class="icon-wrapper" id="cancelButton"><i class="icofont icofont-error"></i></div>
                      <div class="icon-wrapper"><i class="icofont icofont-pencil-alt-5"></i></div>
                    </div>
                    <div class="user-designation"><a target="_blank" href="">William C. Jennings</a>
                      <div class="desc">Designer</div>
                    </div>
                  </div>
                  <div class="follow">
                    <div>
                      <div class="follow-num counter" data-target="258690">0</div><span>Follower</span>
                    </div>
                    <div>
                      <div class="follow-num counter" data-target="659887">0</div><span>Following</span>
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
                    <h6><i class="fa-solid fa-envelope pe-2"></i>Staff ID</h6><span>938657083</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-calendar-days pe-2"></i>Role</h6><span>Super Admin
                    </span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start">
                    <h6><i class="fa-solid fa-phone pe-2"></i>Designation</h6><span>Manger</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>Department</h6><span>P1</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>Department</h6><span>P1</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>HOD</h6><span>Dr Saurabh Sakhuja
                    </span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>Line Manager</h6><span>Dr Saurabh Sakhuja
                    </span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>Specialist</h6><span>Heart</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>EPF No
                    </h6><span>534353</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>Basic Salary
                    </h6><span>50k</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>Contract Type
                    </h6><span>1 Year</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>Work Shift
                    </h6><span>9 TO 5</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>Location</h6><span>B69 Libby Street Beverly Hills</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="ttl-info text-start pb-0">
                    <h6><i class="fa-solid fa-location-arrow pe-2"></i>Date Of Joining
                    </h6><span>01-01-1970
                    </span>
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
                        <a class="nav-link active" id="profiles-project-tab" data-bs-toggle="pill" href="#profiles-project" role="tab" aria-controls="profiles-project" aria-selected="false">
                            <div class="nav-rounded">
                                <div class="product-icons"><i class="fa-solid fa-user"></i></div>
                            </div>
                            <div class="product-tab-content">
                                <h6>Profile</h6>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item"> <a class="nav-link " id="target-project-tab" data-bs-toggle="pill" href="#target-project" role="tab" aria-controls="target-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-timeline"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Recent Activity</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="budget-project-tab" data-bs-toggle="pill" href="#budget-project" role="tab" aria-controls="budget-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Leaves</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="attendance-project-tab" data-bs-toggle="pill" href="#attendance-project" role="tab" aria-controls="attendance-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Attendance</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="documents-project-tab" data-bs-toggle="pill" href="#documents-project" role="tab" aria-controls="documents-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Documents</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" id="userlog-project-tab" data-bs-toggle="pill" href="#userlog-project" role="tab" aria-controls="userlog-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-list-check"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>User Logs</h6>
                        </div></a>
                    </li>
                    <li class="nav-item"><a class="nav-link" id="team-project-tab" data-bs-toggle="pill" href="#team-project" role="tab" aria-controls="team-project" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-regular fa-bell"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Notifications</h6>
                        </div></a></li>
                    <li class="nav-item"><a class="nav-link" id="attachment-tab" data-bs-toggle="pill" href="#attachment" role="tab" aria-controls="attachment" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-gears"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Settings</h6>
                        </div></a></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xxl-9 user-xl-75 col-xl-8 box-col-8e">
              <div class="row">
                <div class="col-12">
                  <div class="tab-content" id="add-product-pills-tabContent">
                  <div class="tab-pane fade show active" id="profiles-project" role="tabpanel" aria-labelledby="profiles-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>More User Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                  <!-- Contact Information Card -->
                                  <div class="col-md-6 col-lg-4">
                                    <div class="card shadow-sm h-100">
                                      <div class="card-header bg-light d-flex align-items-center">
                                        <i class="fa-solid fa-address-book fa-lg text-primary me-2"></i>
                                        <h6 class="mb-0">Contact Information</h6>
                                      </div>
                                      <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                          <li><strong>Phone:</strong> <span class="text-muted">+91 9876543210</span></li>
                                          <li><strong>Email:</strong> <span class="text-muted">john.doe@email.com</span></li>
                                          <li><strong>Gender:</strong> <span class="text-muted">Male</span></li>
                                          <li><strong>Date of Birth:</strong> <span class="text-muted">15-08-1990</span></li>
                                        </ul>
                                      </div>
                                    </div>
                                  </div>
                                  <!-- Address Card -->
                                  <div class="col-md-6 col-lg-4">
                                    <div class="card shadow-sm h-100">
                                      <div class="card-header bg-light d-flex align-items-center">
                                        <i class="fa-solid fa-location-dot fa-lg text-success me-2"></i>
                                        <h6 class="mb-0">Address</h6>
                                      </div>
                                      <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                          <li><strong>Current:</strong> <span class="text-muted">123, Green Avenue, Mumbai</span></li>
                                          <li><strong>Permanent:</strong> <span class="text-muted">456, Blue Street, Pune</span></li>
                                        </ul>
                                      </div>
                                    </div>
                                  </div>
                                  <!-- Bank Account Details Card -->
                                  <div class="col-md-6 col-lg-4">
                                    <div class="card shadow-sm h-100">
                                      <div class="card-header bg-light d-flex align-items-center">
                                        <i class="fa-solid fa-building-columns fa-lg text-warning me-2"></i>
                                        <h6 class="mb-0">Bank Account Details</h6>
                                      </div>
                                      <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                          <li><strong>Account Title:</strong> <span class="text-muted">John Doe</span></li>
                                          <li><strong>Bank Name:</strong> <span class="text-muted">HDFC Bank</span></li>
                                          <li><strong>Account No.:</strong> <span class="text-muted">1234567890</span></li>
                                          <li><strong>IFSC:</strong> <span class="text-muted">HDFC0001234</span></li>
                                        </ul>
                                      </div>
                                    </div>
                                  </div>
                                  <!-- Social Media Links Card -->
                                  <div class="col-md-6 col-lg-4">
                                    <div class="card shadow-sm h-100">
                                      <div class="card-header bg-light d-flex align-items-center">
                                        <i class="fa-brands fa-linkedin fa-lg text-info me-2"></i>
                                        <h6 class="mb-0">Social Media</h6>
                                      </div>
                                      <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                          <li><i class="fa-brands fa-facebook-f text-primary me-1"></i> <span class="text-muted">facebook.com/johndoe</span></li>
                                          <li><i class="fa-brands fa-twitter text-info me-1"></i> <span class="text-muted">twitter.com/johndoe</span></li>
                                          <li><i class="fa-brands fa-linkedin text-primary me-1"></i> <span class="text-muted">linkedin.com/in/johndoe</span></li>
                                          <li><i class="fa-brands fa-instagram text-danger me-1"></i> <span class="text-muted">instagram.com/johndoe</span></li>
                                        </ul>
                                      </div>
                                    </div>
                                  </div>
                                  <!-- Emergency Information Card -->
                                  <div class="col-md-6 col-lg-4">
                                    <div class="card shadow-sm h-100">
                                      <div class="card-header bg-light d-flex align-items-center">
                                        <i class="fa-solid fa-triangle-exclamation fa-lg text-danger me-2"></i>
                                        <h6 class="mb-0">Emergency Info</h6>
                                      </div>
                                      <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                          <li><strong>Emergency Contact:</strong> <span class="text-muted">+91 9123456789</span></li>
                                          <li><strong>Blood Group:</strong> <span class="text-muted">B+</span></li>
                                          <li><strong>Allergies:</strong> <span class="text-muted">None</span></li>
                                        </ul>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="target-project" role="tabpanel" aria-labelledby="target-project-tab">
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
                    <div class="tab-pane fade" id="budget-project" role="tabpanel" aria-labelledby="budget-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Leaves</h5>
                            </div>
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="leaves-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                        <tr>
                                            <th>
                                            Leave Type</th>
                                            <th>Leave Date</th>
                                            <th>Days</th>
                                            <th>Apply Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Sick Leave</td>
                                            <td>15-02-2024</td>
                                            <td>2</td>
                                            <td>10-02-2024</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                            <td>
                                            <ul class="action mb-0">
                                                <li class="view"><a href="#" data-bs-toggle="tooltip" title="Show" onclick="showVisitorDetails('Sagar')"><i class="fa-regular fa-eye"></i></a></li>
                                                <li class="edit"><a href="#" data-bs-toggle="tooltip" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a></li>
                                                <li class="delete"><a href="#" data-bs-toggle="tooltip" title="Delete" onclick="deleteVisitor(event)"><i class="fa-solid fa-trash-can"></i></a></li>
                                            </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Sick Leave</td>
                                            <td>15-02-2024</td>
                                            <td>2</td>
                                            <td>10-02-2024</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
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
                    <div class="tab-pane fade" id="attendance-project" role="tabpanel" aria-labelledby="attendance-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Attendance Record</h5>
                            </div>
                            <div class="card-body">
                                <!-- Attendance Legend -->
                                <div class="attendance-legend mb-4">
                                    <div class="d-flex gap-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge rounded-circle bg-success me-2" style="width: 12px; height: 12px;display: inline-block;"></span>
                                            <span>Present</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge rounded-circle bg-danger me-2" style="width: 12px; height: 12px;display: inline-block;"></span>
                                            <span>Absent</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge rounded-circle bg-warning me-2" style="width: 12px; height: 12px;display: inline-block;"></span>
                                            <span>Half Day</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge rounded-circle bg-info me-2" style="width: 12px; height: 12px;display: inline-block;"></span>
                                            <span>Leave</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attendance Summary Boxes -->
                                <div class="row row-cols-5 g-3 mb-4">
                                    <div class="col ">
                                        <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
                                            <div>
                                                <div class="fw-bold">Total Present</div>
                                                <div class="fs-4">0</div>
                                            </div>
                                            <i class="fa-regular fa-square-check fa-2x text-success"></i>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
                                            <div>
                                                <div class="fw-bold">Total Late</div>
                                                <div class="fs-4">0</div>
                                            </div>
                                            <i class="fa-regular fa-square-check fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
                                            <div>
                                                <div class="fw-bold">Total Absent</div>
                                                <div class="fs-4">95</div>
                                            </div>
                                            <i class="fa-regular fa-square-check fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
                                            <div>
                                                <div class="fw-bold">Total Half Day</div>
                                                <div class="fs-4">0</div>
                                            </div>
                                            <i class="fa-regular fa-square-check fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
                                            <div>
                                                <div class="fw-bold">Total Holiday</div>
                                                <div class="fs-4">0</div>
                                            </div>
                                            <i class="fa-regular fa-square-check fa-2x text-white"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Monthly Attendance Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered attendance-table">
                                        <thead>
                                            <tr>
                                                <th class="bg-light">Day</th>
                                                <th class="text-center">Jan</th>
                                                <th class="text-center">Feb</th>
                                                <th class="text-center">Mar</th>
                                                <th class="text-center">Apr</th>
                                                <th class="text-center">May</th>
                                                <th class="text-center">Jun</th>
                                                <th class="text-center">Jul</th>
                                                <th class="text-center">Aug</th>
                                                <th class="text-center">Sep</th>
                                                <th class="text-center">Oct</th>
                                                <th class="text-center">Nov</th>
                                                <th class="text-center">Dec</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($day = 1; $day <= 31; $day++)
                                            <tr>
                                                <td class="bg-light fw-bold">{{ $day }}</td>
                                                @for ($month = 1; $month <= 12; $month++)
                                                <td class="text-center">
                                                    @php
                                                        // Dummy data - replace with actual attendance data
                                                        $status = rand(1, 4);
                                                        $color = match($status) {
                                                            1 => 'success', // Present
                                                            2 => 'danger',  // Absent
                                                            3 => 'warning', // Half Day
                                                            4 => 'info',    // Leave
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge rounded-circle bg-{{ $color }}"
                                                          style="width: 12px; height: 12px;"
                                                          data-bs-toggle="tooltip"
                                                          title="{{ match($status) {
                                                              1 => 'Present',
                                                              2 => 'Absent',
                                                              3 => 'Half Day',
                                                              4 => 'Leave',
                                                              default => 'Unknown'
                                                          } }}">
                                                    </span>
                                                </td>
                                                @endfor
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="documents-project" role="tabpanel" aria-labelledby="documents-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Documents</h5>
                            </div>
                            <div class="card-body ">
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="documents-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Document</th>
                                                <th>Type</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa-regular fa-file-pdf text-danger me-2 fa-lg"></i>
                                                        <span>Employee Handbook 2024</span>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-danger">.PDF</span></td>
                                                <td class="text-end">
                                                    <a href="#" class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-download me-1"></i>Download
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa-regular fa-file-excel text-success me-2 fa-lg"></i>
                                                        <span>Salary Statement Jan 2024</span>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-success">.XLSX</span></td>
                                                <td class="text-end">
                                                    <a href="#" class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-download me-1"></i>Download
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa-regular fa-file-word text-primary me-2 fa-lg"></i>
                                                        <span>Offer Letter</span>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-primary">.DOC</span></td>
                                                <td class="text-end">
                                                    <a href="#" class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-download me-1"></i>Download
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa-regular fa-file-excel text-info me-2 fa-lg"></i>
                                                        <span>Attendance Report 2023</span>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-info">.CSV</span></td>
                                                <td class="text-end">
                                                    <a href="#" class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-download me-1"></i>Download
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="userlog-project" role="tabpanel" aria-labelledby="userlog-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>User Logs</h5>
                            </div>
                            <div class="card-body ">
                            <div class="row g-3 mb-4">
    <div class="col-md-4 col-12">
      <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
        <div>
          <div class="fw-bold">Created At</div>
          <div style="font-size:1.1rem;">08-01-2024 19:33 PM</div>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-12">
    <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
    <div>
          <div class="fw-bold">Created By</div>
          <div  style="font-size:1.1rem;">Master Admin</div>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-12">
      <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
        <div>
          <div class="fw-bold">Updated At</div>
          <div  style="font-size:1.1rem;">23-05-2025 03:26 AM</div>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-12">
      <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
        <div>
          <div class="fw-bold">Updated By</div>
          <div  style="font-size:1.1rem;">Dr Saurabh Sakhuja</div>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-12">
      <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
        <div>
          <div class="fw-bold">Last Password Changed</div>
          <div style="font-size:1.1rem;">08-09-2024 13:42 PM</div>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-12">
      <div class="card bg-dark border-0 shadow-none p-3 d-flex flex-row align-items-center justify-content-between">
        <div>
          <div class="fw-bold">Last Login At</div>
          <div  style="font-size:1.1rem;">29-04-2025 06:17 AM</div>
        </div>
      </div>
    </div>
  </div>
                                <div class="dt-ext table-responsive custom-scrollbar html-expert-table">
                                    <table id="userlog-table" class="display table-striped w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>
                                                IP Address</th>
                                                <th>Role</th>
                                                <th>From</th>
                                                <th>User Agent</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>127.0.0.1</td>
                                                <td>Admin</td>
                                                <td>127.0.0.1</td>
                                                <td>Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36</td>
                                                <td>2024-06-05 10:00:00</td>
                                            </tr>
                                            <tr>
                                                <td>127.0.0.1</td>
                                                <td>Admin</td>
                                                <td>127.0.0.1</td>
                                                <td>Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36</td>
                                                <td>2024-06-05 10:00:00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="team-project" role="tabpanel" aria-labelledby="team-project-tab">
                        <div class="card">
                        <div class="card-header">
                            <h5>Notifications</h5>
                        </div>
                        <div class="card-body">
                            <div class="user-notifications">
                            <h6>Today </h6>
                            <ul>
                                <li>
                                <div> <img class="img-fluid" src="{{ asset('public/front/assets/images/dashboard-11/user/12.jpg') }}" alt="user">
                                    <div>
                                    <p>Weekly check-in session arranged<span class="ps-2">2h ago</span></p><span>August 30, 2024 at 10:00 AM has been set aside for the weekly check-in meeting.</span>
                                    </div>
                                </div>
                                </li>
                                <li>
                                <div> <img class="img-fluid" src="{{ asset('public/front/assets/images/dashboard-11/user/1.jpg') }}" alt="user">
                                    <div>
                                    <p>Finishing the wireframing phase<span class="ps-2">5h ago</span></p><span>High-fidelity mockups will be created by the design team.</span>
                                    </div>
                                </div>
                                </li>
                                <li>
                                <div> <img class="img-fluid" src="{{ asset('public/front/assets/images/dashboard-11/user/5.jpg') }}" alt="user">
                                    <div>
                                    <p>Customer input received<span class="ps-2">10h ago</span></p><span>Before august 25, 2024, the design team will deliver updated mockups.</span>
                                    </div>
                                </div>
                                </li>
                            </ul>
                            <h6>Yesterday</h6>
                            <ul>
                                <li>
                                <div> <img class="img-fluid" src="{{ asset('public/front/assets/images/dashboard-11/user/2.jpg') }}" alt="user">
                                    <div>
                                    <p>Scheduled usability testing<span class="ps-2">Yesterday</span></p><span>We will collect user input and fix any problems found during the testing process.</span>
                                    </div>
                                </div>
                                </li>
                                <li>
                                <div> <img class="img-fluid" src="{{ asset('public/front/assets/images/dashboard-11/user/4.jpg') }}" alt="user">
                                    <div>
                                    <p>Meeting for the final client review<span class="ps-2">Yesterday</span></p><span>Reviewing the finished website and making any final tweaks before its official debut is the aim of the meeting.</span>
                                    </div>
                                </div>
                                </li>
                            </ul>
                            <h6>18 Feb</h6>
                            <ul>
                                <li>
                                <div> <img class="img-fluid" src="{{ asset('public/front/assets/images/dashboard-11/user/7.jpg') }}" alt="user">
                                    <div>
                                    <p>Confirmed date of website launch<span class="ps-2">18 Feb</span></p><span>Plans are in place to guarantee a successful and seamless launch.Keep checking back for further information.</span>
                                    </div>
                                </div>
                                </li>
                            </ul>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="attachment" role="tabpanel" aria-labelledby="attachment-tab">
                        <div class="card">
                        <div class="card-header">
                            <h5>Settings</h5>
                        </div>
                        <div class="card-body setting-wrapper">
                            <div class="row g-md-3 g-2">
                            <div class="col-md-3">
                                <label class="form-label mb-0">Reminders</label>
                            </div>
                            <div class="col-sm-9">
                                <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input switch-primary check-size" type="checkbox" role="switch" checked="">
                                <p><i data-feather="info"></i>To stay on top of crucial tasks and deadlines, set up alerts and notifications.</p>
                                </div>
                            </div>
                            </div>
                            <div class="row g-md-3 g-2">
                            <div class="col-md-3">
                                <label class="form-label mb-0">Select Language</label>
                            </div>
                            <div class="col-sm-9">
                                <select class="form-select">
                                <option selected="">Select your language</option>
                                <option value="1">English</option>
                                <option value="2">French</option>
                                <option value="3">Gujarati</option>
                                <option value="4">Hindi</option>
                                <option value="5">Japanese</option>
                                <option value="6">Marathi</option>
                                <option value="7">Russian</option>
                                </select>
                            </div>
                            </div>
                            <div class="row g-md-3 g-2">
                            <div class="col-md-3">
                                <label class="form-label mb-0">Recent Activity </label>
                            </div>
                            <div class="col-md-9">
                                <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input switch-primary check-size" type="checkbox" role="switch">
                                <p><i data-feather="info"></i>See a history of your most recent platform interactions and actions.</p>
                                </div>
                            </div>
                            </div>
                            <div class="row g-md-3 g-2">
                            <div class="col-md-3">
                                <label class="form-label mb-0">Two-factor Authentications</label>
                            </div>
                            <div class="col-md-9">
                                <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input switch-primary check-size" type="checkbox" role="switch" checked="">
                                <p><i data-feather="info"></i>By enabling an extra step of verification during login, you can improve account security.</p>
                                </div>
                            </div>
                            </div>
                            <div class="row g-md-3 g-2">
                            <div class="col-md-3">
                                <label class="form-label mb-0">Post Notifications</label>
                            </div>
                            <div class="col-md-9">
                                <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input switch-primary check-size" type="checkbox" role="switch" checked="">
                                <p><i data-feather="info"></i>Control and personalise the platform's alerts for new content and updates.</p>
                                </div>
                            </div>
                            </div>
                            <div class="row g-md-3 g-2">
                            <div class="col-md-3">
                                <label class="form-label mb-0">Remove Accounts</label>
                            </div>
                            <div class="col-md-9">
                                <div class="form-check form-switch form-check-inline">
                                <input class="form-check-input switch-primary check-size" type="checkbox" role="switch" checked="">
                                <p><i data-feather="info"></i>Permanently remove your account and all related information from the platform.</p>
                                </div>
                                <div class="common-flex mt-3"><a class="btn button-light-danger disabled" href="#!" role="button">Disable Account</a><a class="btn btn-danger" href="#!" role="button">Delete Account</a></div>
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
    var table = $('#leaves-table').DataTable({
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
    var attandancetable = $('#attandance-table').DataTable({
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
    var documentstable = $('#documents-table').DataTable({
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
    var userlogtable = $('#userlog-table').DataTable({
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
        if (tabId === '#budget-project') {
            setTimeout(() => {
                $('#leaves-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#attendance-project') {
            setTimeout(() => {
                $('#attandance-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#documents-project') {
            setTimeout(() => {
                $('#documents-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else if (tabId === '#userlog-project') {
            setTimeout(() => {
                $('#userlog-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        } else {
            setTimeout(() => {
                $('#team-table').DataTable().columns.adjust().responsive.recalc();
            }, 200); // delay to ensure visibility before adjusting
        }
    });
});
</script>
@endpush