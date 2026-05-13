
<div class="main-header">
  <div class="main-header-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="index.html" class="logo">
        <img
          src="{{ asset('public/images/logo.jpg') }}"
          alt="navbar brand"
          class="navbar-brand"
          height="20"
        />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>
  <!-- Navbar Header -->
  <nav
    class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
  >
    <div class="container-fluid">
      <!-- <nav
        class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
      >
        <div class="input-group">
          <div class="input-group-prepend">
            <button type="submit" class="btn btn-search pe-1">
              <i class="fa fa-search search-icon"></i>
            </button>
          </div>
          <input
            type="text"
            placeholder="Search ..."
            class="form-control"
          />
        </div>
      </nav> -->

      <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

        <li class="nav-item topbar-user dropdown hidden-caret">
          <a
            class="dropdown-toggle profile-pic"
            data-bs-toggle="dropdown"
            href="#"
            aria-expanded="false"
          >
            <div class="avatar-sm">
              <img
                src="{{ asset('public/images/profile.jpg') }}"
                alt="..."
                class="avatar-img rounded-circle"
              />
            </div>
            <span class="profile-username">
              <span class="op-7"></span>
              <span class="fw-bold">{{ auth()->user()->name }}</span>
            </span>
          </a>
          <ul class="dropdown-menu dropdown-user animated fadeIn">
            <div class="dropdown-user-scroll scrollbar-outer">
              <li>
                <div class="user-box">
                  <div class="avatar-lg">
                    <img
                      src="{{ asset('public/images/profile.jpg') }}"
                      alt="image profile"
                      class="avatar-img rounded"
                    />
                  </div>
                  <div class="u-text">
                    <h4>{{ auth()->user()->name }}</h4>
                    <p class="text-muted">{{ auth()->user()->email }}</p>
                  </div>
                </div>
              </li>
              <li>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">My Profile</a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button class="dropdown-item" type="submit">Logout</button>
              </form>
              </li>
            </div>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
  <!-- End Navbar -->
</div>  