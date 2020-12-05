<!-- <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item nav-profile">
            <div class="nav-link">
              <div class="user-wrapper">
                <div class="profile-image">
                  <img src="{{ asset('assets/images/faces/logo.png')}}" alt="profile image">
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
          <li class="nav-item">
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
          </li>
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
      </nav>-->

<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar">

        <!-- sidebar menu-->
        <ul class="sidebar-menu" data-widget="tree">
           
            <li class="header nav-small-cap">PERSONAL</li>
            

            <li class="treeview" title="Create campaigns and manage via these settins.">
                <a href="#">
                    <i class="fa fa fa-lg fa-list-alt icon text-red2"></i>
                </a>
                <ul class="treeview-menu text-red2">
                   

                    <li><a href="./admin_development.php?ADD=110"><i class=""></i>Dial Queue</a></li>
                    <li><a href="./admin_development.php?ADD=10101010"><i class=""></i>Dial Strategies</a></li>
                   
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-lg fa-database text-red2" title="Create data-lists, load data, setup data movement rules and search your data for unique leads."></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./admin_development.php?ADD=111"><i class=""></i>Create</a></li>
                    <li><a href="./admin_development.php?ADD=100"><i class=""></i>Manage</a></li>
                    <li><a href="./admin_development.php?ADD=101"><i class=""></i>Load Data</a></li>
                    <!--<li><a href="./admin_development.php?ADD=102"><i class=""></i>Load Single Record</a></li>-->
                    <!--<li><a href="./admin_development.php?ADD=500"><i class=""></i>Data Rules</a></li>-->
                    <!--<li><a href="./admin_development.php?ADD=103"><i class=""></i>Status Email Rules</a></li>-->
                    <!--<li><a href="./admin_development.php?ADD=104"><i class=""></i>Data Archive Rules</a></li>-->
                    <li><a href="./admin_development.php?ADD=105"><i class=""></i>Data Search</a></li>
                    <li><a href="./admin_development.php?ADD=106"><i class=""></i>Recording Download</a></li>

                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-lg fa-user text-red2" title="Create user, manage callbacks, manage agent settings."></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./admin_development.php?ADD=108"><i class=""></i>Create</a></li>
                    <li><a href="./admin_development.php?ADD=109"><i class=""></i>Manage</a></li>
                    <li><a href="./admin_development.php?ADD=50"><i class=""></i>User Access Roles</a></li>
                    <li><a href="./admin_development.php?ADD=107"><i class=""></i>Manage Callback</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-lg fa-group text-red2"title="Create teams, manage settings from each team including security rules."></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./admin_development.php?ADD=501"><i class=""></i>Create</a></li>
                    <li><a href="./admin_development.php?ADD=502"><i class=""></i>Manage</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-lg fa-arrow-down text-red2"title="Create inbound groups, numbers and VIR's."></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="./admin_development.php?ADD=503"><i class=""></i>Number Create</a></li>
                    <li><a href="./admin_development.php?ADD=504"><i class=""></i>Number Manage</a></li>
                    <li><a href="./admin_development.php?ADD=505"><i class=""></i>Group Create</a></li>
                    <li><a href="./admin_development.php?ADD=506"><i class=""></i>Group Manage</a></li>
                    <li><a href="./admin_development.php?ADD=507"><i class=""></i>IVR Create</a></li>
                    <li><a href="./admin_development.php?ADD=508"><i class=""></i>IVR Manage</a></li>

                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-lg fa-wrench text-red2"title="Manage system settings, phones, calltimes, carriers, DNC and statuses."></i>

                </a>
                <ul class="treeview-menu">
                    <li><a href="./admin_development.php?ADD=1111111111"><i class=""></i>Call Times Create</a></li>
                    <li><a href="./admin_development.php?ADD=100000000"><i class=""></i>Call Times Manage</a></li>
                    <li><a href="./admin_development.php?ADD=11111111111"><i class=""></i>Phones Create</a></li>
                    <li><a href="./admin_development.php?ADD=10000000000"><i class=""></i>Phones Manage</a></li>
                    <li><a href="./admin_development.php?ADD=543"><i class=""></i>Manage Audio Files</a></li>
                    <li><a href="./admin_development.php?ADD=532"><i class=""></i>Manage DNC</a></li>
                    <li><a href="./admin_development.php?ADD=544"><i class=""></i>Manage SMTP</a></li>
                    <li><a href="./admin_development.php?ADD=533"><i class=""></i>System Statuses</a></li>
                    <li><a href="./admin_development.php?ADD=534"><i class=""></i>Admin Logs</a></li>
                </ul>
            </li>
            
                <li class="treeview">
                <a href="#">
                    <i class="fa fa-lg fa fa-gear text-red2"title="Manage Carrier."></i>

                </a>
                <ul class="treeview-menu">
                    <li><a href="./admin_development.php?ADD=546"><i class=""></i>Carrier</a></li>
                  
                </ul>
                </li>


            <li><a href="./admin_development.php?ADD=10"></a></li>

                <li class="">
                <a href="./admin.php">
                    <i class="fa fa-sign-in"></i> <span>OLD Version</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-right pull-right"></i>
                    </span>
                </a>
            </li>
        </ul>
    </section>
</aside>
