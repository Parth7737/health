@extends('layouts.admin.app')
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">
                            <svg class="stroke-icon">
                            <use href="{{ asset('public/front/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
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
            <div class="cardheader" style="background-image: url('{{ asset('public/front/assets/img/pages/profile-banner.png') }}');">
              <div class="user-image">
                <div class="avatar">
                  <div class="common-align">
                    <div>
                        <img id="output" src="{{ auth()->user()->profile_image }}" alt="Profile Image">
                        <!-- <input type="file" accept="image/*" onchange="loadFile(event)"> -->
                        <!-- <div class="icon-wrapper" id="cancelButton"><i class="icofont icofont-error"></i></div>
                        <div class="icon-wrapper"><i class="icofont icofont-pencil-alt-5"></i></div> -->
                    </div>
                    <div class="user-designation"><a target="_blank" href="">{{auth()->user()->name}}</a>
                        <div class="desc">{{ auth()->user()->getRoleNames()->first() }}</div>
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
                    <li class="nav-item"><a class="nav-link" id="password-tab" data-bs-toggle="pill" href="#password" role="tab" aria-controls="password" aria-selected="false">
                        <div class="nav-rounded">
                          <div class="product-icons"><i class="fa-solid fa-gears"></i></div>
                        </div>
                        <div class="product-tab-content">
                          <h6>Update Password</h6>
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
                    <div class="tab-pane fade show active" id="profiles-project" role="tabpanel" aria-labelledby="profiles-project-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>General Information</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="savedata" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label class="col-md-3 form-label">Name</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="text" id="name" value="{{auth()->user()->name}}" placeholder="Name" name="name" required="">
                                        </div>
                                    </div>                                    
                                    
                                    <div class="form-group mt-3">
                                        <label class="col-md-3 form-label">Email</label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input class="form-control" type="email" id="email" name="email" value="{{auth()->user()->email}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="col-md-3 form-label">Gender</label>
                                        <div class="col-md-9">
                                            <select name="gender" id="gender" class="form-control">
                                                <option value="Male" {{auth()->user()->gender == "Male" ? 'selected' : ''}}>Male</option>
                                                <option value="Female" {{auth()->user()->gender == "Female" ? 'selected' : ''}}>Female</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="col-md-3 form-label">Avatar</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="file" id="avatar" name="avatar">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mt-3">
                                        <label class="col-md-3 form-label">Mobile No</label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input class="form-control" type="text" id="mobile_no" name="mobile_no" value="{{auth()->user()->mobile_no}}">
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary ms-auto d-block">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Change Password</h5>
                            </div>
                            <div class="card-body setting-wrapper">
                                <form method="POST" id="changepassword" enctype="multipart/form-data">
                                    <div class="form-group mt-3">
                                        <label class="col-md-3 form-label">Old Password</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="password" id="old_password"  placeholder="Old Password" name="old_password" required="">
                                        </div>
                                    </div> 
                                    
                                    <div class="form-group mt-3">
                                        <label class="col-md-3 form-label">New Password</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="password" id="new_password"  placeholder="New Password" name="new_password" required="">
                                        </div>
                                    </div> 

                                    <div class="form-group mt-3">
                                        <label class="col-md-3 form-label">Confirm Password</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="password" id="confirmation_password"  placeholder="Confirm Password" name="confirmation_password" required="">
                                        </div>
                                    </div> 
                                    
                                    <button class="btn btn-primary ms-auto d-block">Save</button>
                                </form>
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