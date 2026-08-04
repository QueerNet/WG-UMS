<?php include 'app/inc/user-header.php'; ?>



<?php 

$myid = isset($_GET['myid']) ? $_GET['myid'] : '';
  if (!isset($myid) && $myid == NULL) {
      //header("user-dash.php");
      echo "<script>location.href='user-dash.php';</script>";
      exit();

  }else{
    $myid = preg_replace('/[^a-zA-Z0-9-]/', '', $myid);
    $myprofile = $usr->getUserById($myid);
  }

   

 ?>


<!--====== Start Main Wrapper Section======-->
<section class="d-flex" id="wrapper">

   <?php include 'app/inc/user-sidebar.php'; ?>

   <div class="page-content-wrapper">

      <?php 

            if ($myprofile) {
                
                while ($result = $myprofile->fetch_assoc()) {

           ?>

      <!--  Header BreadCrumb -->
      <div class="content-header">
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="user-dash.php"><i class="bi bi-house-door-fill"></i>Home</a></li>
               <?php if ( isset($edit) == '$edit') { ?>
               <li class="breadcrumb-item"><a href="editprofile.php?edituser=<?php echo Session::get("userid")?>">My
                     profile</a></li>
               <?php } ?>

               <li class="breadcrumb-item active" aria-current="page"><?php echo $result['name']; ?></li>
            </ol>
         </nav>
         <div class="create-item">

            <?php if ( isset($edit) == '$edit') { ?>
            <a href="editprofile.php?edituser=<?php echo Session::get("userid")?>"
               class="btn btn-primary"><i class="bi bi-pencil-square"></i>&nbspEdit profile</a>
            <a href="user-changepass.php" name='export' class=" btn btn-secondary"><i class="bi bi-key-fill"></i>&nbspPassword
               change</a>
            <?php } ?>



         </div>
      </div>
      <!--  Header BreadCrumb -->
      <!-- Create New User -->
      <div class="main-content">

         <div class="card">
            <div class="card-body mt-3 mb-5">

               <div class="viewuser row">

                  <div class="col-md-6">
                     <div class="form-group row">
                        <div class="user-thumb d-mobile">
                           <img id="profile-photo" align='middle' src="app/uploads/userAvatar/User.png" alt="your image"
                              title='' />
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-4">Name</div>
                        <div class="col-md-8">
                           <?php echo $result['name']; ?>

                        </div>
                     </div>



                     <div class="form-group row">
                        <div class="col-md-4">E-Mail Address</div>
                        <div class="col-md-8">
                           <?php echo $result['email']; ?>

                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-md-4">Role</div>
                        <div class="col-md-8">
                           <span
                              class="badge badge-lg text-bg-secondary"><?php echo $result['rolename']; ?></span>
                        </div>
                     </div>
                      
                     <div class="form-group row">
                        <div class="col-md-4">Status</div>
                        <div class="col-md-8">
                           <?php if ($result['status'] == '0') {?>
                           <span class="badge badge-lg text-bg-success">Active</span>
                           <?php }elseif($result['status'] == '1'){  ?>
                           <span class="badge badge-lg text-bg-warning">Deactive</span>
                           <?php } ?>
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-4">Account created</div>
                        <div class="col-md-8">

                           <span
                              class="badge badge-lg text-bg-dark"><?php echo $fm->formatDate($result['create_date']); ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6 d-desktop">
                     <div class="user-thumb">
                        <img id="profile-photo" align='middle' src="app/uploads/userAvatar/User.png" alt="your image"
                           title='' />
                     </div>

                  </div>



               </div>

            </div>
         </div>
      </div>
      <!-- Create New User-->


      <?php
        }}else{
          echo "<script>window.location='user-dash.php';</script>";
        }
      ?>

   </div>
   <!--  main-content -->
   </div>

</section>

<!--====== End Main Wrapper Section======-->

<?php include 'app/inc/footer.php'; ?>