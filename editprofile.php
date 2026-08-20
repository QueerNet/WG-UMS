<?php include 'app/inc/user-header.php'; ?>


<!--====== Start Main Wrapper Section======-->
<section class="d-flex" id="wrapper">

   <?php 
   
   if ( $ROLE['ROLENAME']=='sysadmin' ) {
      include 'app/inc/sidebar.php';
   } else {
      include 'app/inc/user-sidebar.php';
   } ?>

   <?php 


   $edituser = isset($_GET['edituser']) ? $_GET['edituser'] : '';
   if (!isset($edituser)) {
      // header("Location:users.php");
      echo '<strong>Error !</strong> Something went wrong...</div>';
      // echo "<script>location.href='users.php';</script>";
  }else{
     $edituser = preg_replace('/[^a-zA-Z0-9-]/', '', $edituser);
      $useredit = $usr->editUserById($edituser, $ROLE['ROLENAME']);
  }

  

 ?>





   <?php 

    if (isset($useredit)) {
      $result = $useredit->fetch_assoc()
    

 ?>
   <div class="page-content-wrapper">
      <!--  Header BreadCrumb -->
      <div class="content-header">
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="user-dash.php"><i class="bi bi-house-door-fill"></i>Home</a></li>
               <li class="breadcrumb-item"><a href="editprofile.php?edituser=<?php echo Session::get("userid")?>.php">My Account</a></li>
               <li class="breadcrumb-item active" aria-current="page"><?php echo e($result['name']); ?></li>
            </ol>
         </nav>
         <div class="create-item">
            <?php if ( $access ) { ?>
               <a href="users.php" class="btn btn-secondary"><i class="bi bi-arrow-left-short"></i>Back To Userlist</a>
            <?php } else { ?>
               <a href="user-account.php?myid=<?php echo Session::get("userid")?>" class="btn btn-secondary"><i class="bi bi-arrow-left-short"></i>Back to account</a>
            <?php } ?>
         </div>
      </div>
      <!--  Header BreadCrumb -->
      <?php 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $updateUser = $usr->updateUserById($_POST, $_FILES, $edituser);


}


 ?>
      <?php 

            if (isset($updateUser)) {
              echo e($updateUser);
            }

           ?>
      <!-- Create New User -->
      <div class="main-content">

         <div class="card editprofile">
            <div class="card-body mt-5 mb-5">

               <form action="" method="POST" enctype="multipart/form-data">

                  <div class="form-group row">
                     <div class="col-md-2">Name</div>
                     <div class="col-md-8">
                        <input id="name" type="text" value="<?php echo e($result['name']); ?>" class="form-control"
                           name="name" value="" autofocus="">

                     </div>
                  </div>
                  
                  <div class="form-group row">
                     <div class="col-md-2">E-Mail Address</div>
                     <div class="col-md-4">
                        <input id="email" type="email" readonly="readonly" value="<?php echo $result['email']; ?>"
                           class="form-control" name="email" value="" autofocus="">

                     </div>
                  </div>
                  
                  <?php if ( $access ) { ?>
                  <div class="form-group row">
                     <div class="col-md-2">Role</div>
                     <div class="col-md-4">


                        <select class="form-control" id="rolename" name="rolename">
                           <option>Select user Role</option>
                           <?php 

                                    $rolelist = $rol->selectAllRole();
                                    if ($rolelist) {
                                        
                                        while ($roleitem = $rolelist->fetch_assoc()) {
                                            
                                          

                                 ?>



                           <option <?php if ($result['rolename'] == $roleitem['rolename']) { ?> selected="selected"
                              <?php }?> value="<?php echo $roleitem['rolename']; ?>">

                              <?php if (isset($roleitem['rolename'])) {
                                      echo $roleitem['rolename'];
                                    } ?>

                           </option>

                           <?php echo TRUE;}}else{  ?>

                           <option>No user Role created Yet !</option>
                           <?php } ?>

                        </select>
                     </div>
                  </div>
                  <?php }else{ ?>
                  <div class="form-group row">
                     <div class="col-md-2">Role</div>
                     <div class="col-md-4">


                        <select class="form-control" id="rolename" name="rolename">


                           <option value="<?php echo e($result['rolename']); ?>"><?php echo e($result['rolename']); ?>
                           </option>

                        </select>
                     </div>
                  </div>

                  <?php } ?>
                  <div class="form-group row">
                     <div class="col-md-2">Status</div>
                     <div class="col-md-4">
                        <select class="form-control" id="status" name="status">

                           <?php if ( Session::get("userid") == $result['userid']) { ?>
                           <?php if ($result['status'] == '0') {?>
                           <option value="0" selected="selected">Active</option>

                           <?php }elseif($result['status'] == '1'){  ?>
                           <option value="1" selected="selected">Deactive</option>

                           <?php } ?>
                           <?php }else{ ?>
                           <?php if ( isset($edit) == '$edit') { ?>
                           <?php if ($result['status'] == '0') {?>
                           <option value="0" selected="selected">Active</option>
                           <option value="1">Deactive</option>
                           <?php }elseif($result['status'] == '1'){  ?>
                           <option value="1" selected="selected">Deactive</option>
                           <option value="0">Active</option>
                           <?php } ?>
                           <?php } else{?>

                           <?php if ($result['status'] == '0') {?>
                           <option value="0" selected="selected">Active</option>
                           <?php }elseif($result['status'] == '1'){  ?>
                           <option value="1" selected="selected">Deactive</option>
                           <?php } ?>
                           <?php } ?>
                           <?php } ?>





                        </select>
                     </div>
                  </div>
                  <div class="form-group row">
                     <div class="col-md-2">Account update Date</div>
                     <div class="col-md-4">

                        <div class="input-group date" id="id_0">
                           <span class="input-with-icon">
                           <input type="text" name="create_date" value="<?php echo e($result['create_date']); ?>"
                              class="form-control" required /><i class="bi bi-calendar-week" style="max-height:25px"></i>
                           </span>
                        </div>

                     </div>
                  </div>
                  <div class="form-group pt-2 row">
                     <div class="col-md-2"></div>
                     <div class="col-md-4">
                        <button class="btn btn-success" type="submit" name="update">Update User</button>
                     </div>
                  </div>

               </form>

            </div>
         </div>
      </div>
      <!-- Create New User-->

      <?php
        } elseif ($access){
          echo "<script>window.location='users.php';</script>";
        } else {
         echo '<strong>Error !</strong> Something went wrong...</div>';
        }
      ?>


      <div class="row mt-3">

         <div class="col-md-12">
            <div class="card ">
               
            </div>
         </div>


      </div>

   </div>
   <!--  main-content -->
   </div>

</section>

<!--====== End Main Wrapper Section======-->

<?php include 'app/inc/footer.php'; ?>