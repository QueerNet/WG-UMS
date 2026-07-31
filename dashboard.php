<?php include 'app/inc/header.php'; ?>
<?php Session::requireAdmin($access); ?>



<!--====== Start Main Wrapper Section======-->
<section class="d-flex" id="wrapper">

   <?php include 'app/inc/sidebar.php'; ?>

   <?php if ( $access ) { ?>




   <div class="page-content-wrapper">

      <!--  main-content -->
      <div class="main-content">

         <?php include 'app/inc/breadcrumb.php'; ?>








         <!-- Dashboard Box -->
         <div class="row wow animated fadeInUp">
            <div class="col-xl-3 col-sm-6 mb-3">
               <div class="card text-white bg-info o-hidden h-100">
                  <div class="card-body">
                     <div class="card-body-icon">
                        <i class="material-icons float-right text-white md-5em">group_add</i>
                     </div>
                     <div class="mr-5">(

                        <?php  

                    $newUser = $usr->newUsers();

                      if ($newUser) {
                       $count = mysqli_num_rows($newUser);
                       if ($count > 0) {
                        echo $count;
                       }
                      }else{
                           echo " 0 ";
                      }




                    ?>

                        ) New Users</div>
                  </div>
                  <a class="card-footer text-white clearfix small z-1" href="newusers.php">
                     <span class="float-left">View Details</span>
                     <span class="float-right">
                        <i class="material-icons">
                           keyboard_arrow_right
                        </i>
                     </span>
                  </a>
               </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
               <div class="card text-white bg-primary o-hidden h-100">
                  <div class="card-body">
                     <div class="card-body-icon">
                        <i class="material-icons float-right text-white md-5em">supervisor_account</i>
                     </div>
                     <div class="mr-5">(
                        <?php  

                    $activeUser = $usr->onlyActiveUsers();

                      if ($activeUser) {
                       $count = mysqli_num_rows($activeUser);
                       if ($count > 0) {
                        echo $count;
                       }
                      }else{
                          echo " 0 ";
                      }




                    ?>
                        ) Active Online Users</div>
                  </div>
                  <a class="card-footer text-white clearfix small z-1" href="activeusers.php">
                     <span class="float-left">View Details</span>
                     <span class="float-right">
                        <i class="material-icons">
                           keyboard_arrow_right
                        </i>
                     </span>
                  </a>
               </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
               <div class="card text-white bg-danger o-hidden h-100">
                  <div class="card-body">
                     <div class="card-body-icon">
                        <i class="material-icons float-right text-white md-5em">person_outline</i>
                     </div>
                     <div class="mr-5">(
                        <?php  

                    $bandUser = $usr->bandUsers();

                      if ($bandUser) {
                       $count = mysqli_num_rows($bandUser);
                       if ($count > 0) {
                        echo $count;
                       }
                      }else{
                          echo " 0 ";
                      }




                    ?>

                        ) Banned Users</div>
                  </div>
                  <a class="card-footer text-white clearfix small z-1" href="bandusers.php">
                     <span class="float-left">View Details</span>
                     <span class="float-right">
                        <i class="material-icons">
                           keyboard_arrow_right
                        </i>
                     </span>
                  </a>
               </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
               <div class="card text-white bg-success o-hidden h-100">
                  <div class="card-body">
                     <div class="card-body-icon">
                        <i class="material-icons float-right text-white md-5em">people_outline</i>
                     </div>
                     <div class="mr-5">(
                        <?php  

                    $totalUsers = $usr->totalUsers();

                      if ($totalUsers) {
                       $count = mysqli_num_rows($totalUsers);
                       if ($count > 0) {
                        echo $count;
                       }
                      }else{
                          echo " 0 ";
                      }




                    ?>

                        ) Total Users</div>
                  </div>
                  <a class="card-footer text-white clearfix small z-1" href="users.php">
                     <span class="float-left">View Details</span>
                     <span class="float-right">
                        <i class="material-icons">
                           keyboard_arrow_right
                        </i>
                     </span>
                  </a>
               </div>
            </div>
         </div>
         <!-- Dashboard Box -->




         <div class="row mt-3">

            <div class="col-md-12">
               <div class="card ">
                  <div class="card-body">
                     <div class="chart-container">
                        <div>
                           <canvas id="mycanvas"></canvas>
                        </div>
                     </div>
                  </div>
               </div>
            </div>


         </div>














      </div>
      <!--  main-content -->


   </div>
   <?php }else{  ?>

   <?php 

// Delete Role By Id 

if (isset($_GET['delid']) && isset($_GET['remove']) == 'removeid') {
    $delid = $fm->sanitizeid($_GET['delid']);
    $deUserByid = $usr->deleteUserById($delid);
}
 ?>

   <?php 
// Id disable method 
// $disid = $fm->sanitizepageId($_GET['disid']);
if(isset($_GET['disid'])){
    $disid = preg_replace('/[^a-zA-Z0-9-]/', '', $_GET['disid']);
    $disableId = $usr->DisableUserById($disid);
}


// Id Enable method 
// $enid = $fm->sanitizepageId($_GET['enid']);
if(isset($_GET['enid'])){
    $enid = preg_replace('/[^a-zA-Z0-9-]/', '', $_GET['enid']);
    $enableId = $usr->EnableUserById($enid);
}



 ?>



   <div class="page-content-wrapper">
      <!--  Header BreadCrumb -->
      <div class="content-header">
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="userdash.php"><i class="material-icons">home</i>Home</a></li>

            </ol>
         </nav>

      </div>
      <!--  Header BreadCrumb -->
      <?php 
      $completeProfile = $usr->profileCompleteNotify($userEmail, $userid);

      if (isset($completeProfile)) {
        echo $completeProfile;
      }

       ?>
      <?php 
            if (isset($deUserByid)) {
                echo $deUserByid;
            }
            if (isset($enableId)) {
                echo $enableId;
            }
            if (isset($disableId)) {
                echo $disableId;
            }
           ?>
      <!--  main-content -->
      <div class="main-content">



         <!-- Users DataTable-->
         <div class="row wo animated fadeInUp mt-3">
            <div class="col-md-12">
               <div class="card">
                  <div class="card-body mt-3">
                     <div class="table-responsive">
                        <table id="usersTable" class="table table-striped table-borderless" style="width:100%">
                           <thead>
                              <tr>
                                 <th>SL</th>
                                 <th>Avatar</th>
                                 <th>Name</th>
                                 <th>Email</th>
                                 <th>Role</th>
                                 <th>Status</th>
                                 <th class="text-center">Action</th>
                              </tr>
                           </thead>
                           <tbody>

                              <?php 

                                    $userlist = $usr->selectAllUsers();
                                    if ($userlist) {
                                        $i = 0;
                                        foreach ($userlist as $userentry) {
                                          $i++;
                                          
                                 ?>
                              <tr
                                 <?php if ( Session::get("userid") == $userentry['userid']) {echo "class='alert-info'";} ?>>


                                 <td class="pt-4" <?php if ($userentry['status'] == '1') { ?> style='color:red' <?php } ?>>
                                    <?php echo $i; ?>

                                 </td>

                                 <!--User icon section-->
                                <td>
                                   <div id="status-online">
                                      <img id="avatar-css" width="50" height="50" align='middle'
                                         src="app/uploads/userAvatar/User.png" alt="User" title='Online' />

                                      <?php if ($userentry['lastactivity'] == 1) { ?>
                                      <div class="online-icon"> </div>

                                      <?php  }else { ?>

                                      <div class="offline-icon"> </div>

                                      <?php } ?>
                                   </div>
                                </td>
                                <!--User icon end-->

                                 <td class="pt-4"><?php echo $userentry['name']; ?></td>
                                 <td class="pt-4"><?php echo $userentry['email']; ?></td>
                                 <td class="pt-4"><span
                                       class="badge badge-lg badge-secondary text-white"><?php echo $userentry['rolename']; ?></span>
                                 </td>
                                 <td class="pt-4">
                                    <?php if ($userentry['status'] == '0') {?>
                                    <span class="badge badge-lg badge-success text-white">Active</span>
                                    <?php }elseif($userentry['status'] == '1'){  ?>
                                    <span class="badge badge-lg badge-warning text-white">Deactive</span>
                                    <?php } ?>

                                 </td>


                                 <td class="text-center pt-3">






                                    <?php if ( $show || $useronly ) { ?>
                                    <a class="btn btn-secondary"
                                       href="viewuser.php?viewuser=<?php echo $userentry['userid']; ?>">&nbspView
                                       user&nbsp</a>
                                    <?php } ?>


                                    <?php if ($userentry['rolename'] == "sysadmin") { ?>
                                    <?php if ( $access ) { ?>
                                    <a class="btn btn-info
            
            " href="editprofile.php?edituser=<?php echo $userentry['userid']; ?>">&nbspEdit&nbsp</a>
                                    <?php } ?>
                                    <?php }else{ ?>

                                    <?php if ( $edit ) { ?>
                                    <a class="btn btn-info
          
          " href="editprofile.php?edituser=<?php echo $userentry['userid']; ?>">&nbspEdit&nbsp</a>
                                    <?php } ?>

                                    <?php } ?>



                                    <?php if ( Session::get("userid") == $userentry['userid']) { ?>


                                    <?php }else{?>

                                    <?php if ( $banactive ) { ?>
                                    <?php 
            if ($userentry['status'] == '0') {?>
                                    <a class="btn btn-warning text-white"
                                       onclick="return confirm('Are you sure to Delete Deactive ?')"
                                       href="?disid=<?php echo $userentry['userid']; ?>">&nbspDeactive&nbsp</a>
                                    <?php } else{?>
                                    <a class="btn btn-warning text-white"
                                       onclick="return confirm('Are you sure to Delete Active ?')"
                                       href="?enid=<?php echo $userentry['userid']; ?>">&nbspActive&nbsp</a>
                                    <?php } }?>

                                    <?php }?>




                                    <?php if ( Session::get("userid") == $userentry['userid']) { ?>

                                    <?php if ( $delete ) { ?>
                                    <a class="btn btn-danger" onclick="return confirm('You can not Remove account !')"
                                       href="#">&nbspNo Action&nbsp</a>
                                    <?php } ?>

                                    <?php }else{?>


                                    <?php if ( $delete ) { ?>
                                    <a class="btn btn-danger"
                                       onclick="return confirm('Are you sure to Delete account ?')"
                                       href="?remove=removeid&&delid=<?php echo $userentry['userid']; ?>">&nbspDelete&nbsp</a>
                                    <?php } ?>
                                    <?php }?>
                                 </td>





                              </tr>

                              <?php }}else{  ?>

                              <tr>
                                 <td colspan="7" class="text-center">No User created yet !</td>
                              </tr>
                              <?php } ?>

                        </table>
                     </div>
                  </div>
               </div>
            </div>

         </div>

         <!-- Users DataTable-->







      </div>
      <!--  main-content -->
   </div>
   <?php } ?>
</section>

<!--====== End Main Wrapper Section======-->




<?php include 'app/inc/footer.php'; ?>