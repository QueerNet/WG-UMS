<?php include 'app/inc/header.php'; ?>



<?php 

// Delete Role By Id 
$delid = isset($_GET['delid']) ? $_GET['delid'] : '';
if (isset($_GET['delid']) && isset($_GET['remove']) == 'removeid') {
    $delid = preg_replace('/[^a-zA-Z0-9-]/', '', $_GET['delid']);
    $deUserByid = $usr->deleteUserById($delid);
}
 ?>

<?php 
// Id disable method 
$disid = isset($_GET['disid']) ? $_GET['disid'] : '';
if(isset($_GET['disid'])){
    $disid = preg_replace('/[^a-zA-Z0-9-]/', '', $_GET['disid']);
    $disableId = $usr->DisableUserById($disid);
}


// Id Enable method 
$enid = isset($_GET['enid']) ? $_GET['enid'] : '';
if(isset($_GET['enid'])){
    $enid = preg_replace('/[^a-zA-Z0-9-]/', '', $_GET['enid']);
    $enableId = $usr->EnableUserById($enid);
}



 ?>




<!--====== Start Main Wrapper Section======-->
<section class="d-flex" id="wrapper">

   <?php include 'app/inc/sidebar.php'; ?>

   <div class="page-content-wrapper">
      <!--  Header BreadCrumb -->
      <div class="content-header">
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="dashboard.php"><i class="bi bi-house-door-fill"></i>Home</a></li>

               <li class="breadcrumb-item active" aria-current="page">Users</li>
            </ol>
         </nav>
         <div class="create-item">


            <?php if ( $create ) { ?>
            <a href="adduser.php" class="theme-primary-btn btn btn-primary"><i class="bi bi-person-plus-fill"></i>
            &nbsp Create user</a>
            <?php } ?>



         </div>
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
         <div class="row mt-3">
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



                                    <?php if ( Session::get("userid") == $userentry['userid']  || $userentry['rolename'] == "sysadmin") { ?>


                                    <?php }else{?>

                                    <?php if ( $banactive ) { ?>
                                    <?php 
            if ($userentry['status'] == '0') {?>
                                    <a class="btn btn-warning text-white"
                                       onclick="return confirm('Are you sure to Deactive ?')"
                                       href="?disid=<?php echo $userentry['userid']; ?>">&nbspDeactive&nbsp</a>
                                    <?php } else{?>
                                    <a class="btn btn-warning text-white"
                                       onclick="return confirm('Are you sure to Active ?')"
                                       href="?enid=<?php echo $userentry['userid']; ?>">&nbspActive&nbsp</a>
                                    <?php } }?>

                                    <?php }?>




                                    <?php if ( Session::get("userid") == $userentry['userid'] || $userentry['rolename'] == "sysadmin") { ?>

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




         <!-- Section below
         <div class="row mt-3">
         </div>
         -->



      </div>
      <!--  main-content -->
   </div>

</section>

<!--====== End Main Wrapper Section======-->

<?php include 'app/inc/footer.php'; ?>