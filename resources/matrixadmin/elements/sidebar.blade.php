 <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item nav-profile">
            <div class="nav-link">
              <div class="user-wrapper">
                <div class="profile-image">
                  <!--<img src="{{ asset('assets/images/faces/logo.png')}}" alt="profile image">-->
                  <img src="{{ asset('assets/images/faces/api-admin.jpg')}}" alt="profile image">
                </div>
                <div class="text-wrapper">
                  <p class="profile-name">{{Auth::user()->name}}</p>
                  <div>
                    <small class="designation text-muted">Manager</small>
                    <span class="status-indicator online"></span>
                  </div>
                </div>
              </div>
              <button class="btn btn-success btn-block">New Project
                <i class="mdi mdi-plus"></i>
              </button>
            </div>
          </li>   
          <li class="nav-item active">
            <a class="nav-link" href="{{route('dashboard')}}">
              <i class="menu-icon mdi mdi-television"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#O2Return" aria-expanded="false" aria-controls="O2Return">
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title">O2 Return Process</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="O2Return">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('AutomationO2ReturnProcessFile')}}">File</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{route('AutomationO2ReturnProcessData')}}">Data</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#O2FreeSim" aria-expanded="false" aria-controls="O2FreeSim">
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title">O2 Free Sim Process</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="O2FreeSim">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('AutomationO2FreeSimProcessFile')}}">File</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{route('AutomationO2FreeSimProcessData')}}">Data</a>
                </li>
              </ul>
            </div>
          </li>
<!--          <li class="nav-item">
            <a class="nav-link" href="pages/forms/basic_elements.html">
              <i class="menu-icon mdi mdi-backup-restore"></i>
              <span class="menu-title">Form elements</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="pages/charts/chartjs.html">
              <i class="menu-icon mdi mdi-chart-line"></i>
              <span class="menu-title">Charts</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="pages/tables/basic-table.html">
              <i class="menu-icon mdi mdi-table"></i>
              <span class="menu-title">Tables</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="pages/icons/font-awesome.html">
              <i class="menu-icon mdi mdi-sticker"></i>
              <span class="menu-title">Icons</span>
            </a>
          </li>-->
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
              <i class="menu-icon mdi mdi-restart"></i>
              <span class="menu-title">Crons</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="auth">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('CronManage')}}">Management</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{route('CronAdd')}}">Add</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#Octopus" aria-expanded="false" aria-controls="Octopus">
              <i class="menu-icon mdi mdi-restart"></i>
              <span class="menu-title">Octopus</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="Octopus">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('Octopus')}}">Lead Day View</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{route('OctopusManagement')}}">Lead Listings</a>
                </li>
              </ul>
            </div>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#User" aria-expanded="false" aria-controls="User">
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title">User Listings</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="User">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('UserAdd')}}">Add</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{route('UserListings')}}">Manage</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{route('DBCOnnections')}}">
              <i class="menu-icon mdi mdi-sticker"></i>
              <span class="menu-title">Database Connections</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{route('SupplierLeadManage')}}">
              <i class="menu-icon mdi mdi-sticker"></i>
              <span class="menu-title">Supplier Leads</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="pages/icons/font-awesome.html">
              <i class="menu-icon mdi mdi-sticker"></i>
              <span class="menu-title">Web Settings</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#Email" aria-expanded="false" aria-controls="Email">
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title">Email Address Listings</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="Email">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('EmailAdd')}}">Add</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{route('EmailManage')}}">Manage</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#APIManagment" aria-expanded="false" aria-controls="APIManagment">
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title">API Management</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="APIManagment">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('LeadManage')}}">Lead Listings</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{route('APIFieldValidationManage')}}">Fields Validations</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#O2UNICA" aria-expanded="false" aria-controls="O2UNICA">
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title">O2UNICA Management</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="O2UNICA">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('O2UNICAFileImport')}}">File Import</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{route('O2UNICAFileData')}}">File Data</a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </nav>