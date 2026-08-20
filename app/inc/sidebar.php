<?php Session::requireAdmin($access); ?>
<!--   Left Sidebar  -->
      <aside>
        <!-- Wrapper -->
        <div class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="wrapper-sidebar"
          style="--bs-offcanvas-width: 250px;" aria-labelledby="sidebarLabel">
          <!-- Toggle -->
          <div class="offcanvas-header">
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                  data-bs-target="#wrapper-sidebar" aria-label="Close"></button>
          </div>
          <!-- Actual sidebar -->
        <div class="offcanvas-body p-0 sidebar">
          <ul>
              <?php if (  $access  ) { ?>
            <li><a 
              <?php 

                $path = $_SERVER['SCRIPT_FILENAME'];
                $current = basename($path, '.php');
                if ($current == 'dashboard') {
                  echo " class='active' ";
                }

               ?>
             href="dashboard.php"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></li>
            <li><a 
              <?php 

                $path = $_SERVER['SCRIPT_FILENAME'];
                $current = basename($path, '.php');
                if ($current == 'users') {
                  echo " class='active' ";
                }elseif ($current == 'adduser') {
                  echo " class='active' ";
                }elseif ($current == 'viewuser') {
                  echo " class='active' ";
                }elseif ($current == 'editprofile') {
                  echo " class='active' ";
                }elseif ($current == 'newusers') {
                  echo " class='active' ";
                }elseif ($current == 'activeusers') {
                  echo " class='active' ";
                }elseif ($current == 'bandusers') {
                  echo " class='active' ";
                }

               ?>
             href="users.php"><i class="bi bi-people"></i><span>Users</span></a></li>
           

            <li><a 
              <?php 

                $path = $_SERVER['SCRIPT_FILENAME'];
                $current = basename($path, '.php');
                if ($current == 'role') {
                  echo " class='active' ";
                }elseif ($current == 'editrole') {
                  echo " class='active' ";
                }elseif ($current == 'createrole') {
                  echo " class='active' ";
                }


               ?>
             href="role.php">
             <!-- <i class="bi bi-person-rolodex"></i> -->
             <i class="bi bi-person-workspace"></i>
             <span>Roles</span></a></li>


            <li><a 
              <?php 

                $path = $_SERVER['SCRIPT_FILENAME'];
                $current = basename($path, '.php');
                if ($current == 'permissions') {
                  echo " class='active' ";
                }

               ?>
             href="permissions.php"><i class="bi bi-person-fill-lock"></i><span>Permissions</span></a></li>
            <li><a 
              <?php 

                $path = $_SERVER['SCRIPT_FILENAME'];
                $current = basename($path, '.php');
                if ($current == 'settings') {
                  echo " class='active' ";
                }

               ?>


             href="settings.php"><i class="bi bi-gear"></i><span>General Settings</span></a></li>

             
           <?php }else{ ?>
            <li><a 
              <?php 

                $path = $_SERVER['SCRIPT_FILENAME'];
                $current = basename($path, '.php');
                if ($current == 'dashboard') {
                  echo " class='active' ";
                }elseif ($current == 'newusers') {
                  echo " class='active' ";
                }elseif ($current == 'activeusers') {
                  echo " class='active' ";
                }elseif ($current == 'bandusers') {
                  echo " class='active' ";
                }

               ?>
             href="dashboard.php"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></li>
            <li><a 
              <?php 

                $path = $_SERVER['SCRIPT_FILENAME'];
                $current = basename($path, '.php');
                if ($current == 'users') {
                  echo " class='active' ";
                }elseif ($current == 'adduser') {
                  echo " class='active' ";
                }elseif ($current == 'viewuser') {
                  echo " class='active' ";
                }elseif ($current == 'editprofile') {
                  echo " class='active' ";
                }elseif ($current == 'newusers') {
                  echo " class='active' ";
                }elseif ($current == 'activeusers') {
                  echo " class='active' ";
                }elseif ($current == 'bandusers') {
                  echo " class='active' ";
                }

               ?>
             href="users.php"><i class="bi bi-people"></i><span>Users</span></a></li>
            <li><a 
              <?php 

                $path = $_SERVER['SCRIPT_FILENAME'];
                $current = basename($path, '.php');
                if ($current == 'permissions') {
                  echo " class='active' ";
                }

               ?>
             href="permissions.php"><i class="bi bi-person-fill-lock"></i><span>Permissions</span></a></li>
              <?php } ?>
            </ul>
        </div>
      </aside>
  <!--   Left Sidebar  -->