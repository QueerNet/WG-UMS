<?php include 'app/inc/header.php'; ?>
<?php Session::requireAdmin($access); ?>


<?php 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $inserUser = $usr->createNewUserData($_POST, $_FILES);

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
               <li class="breadcrumb-item"><a href="users.php">Users</a></li>
               <li class="breadcrumb-item active" aria-current="page">Add new User</li>
            </ol>
         </nav>
         <div class="create-item">

            <a href="adduser.php" class="btn btn-primary"><i class="bi bi-plus-square"></i>Create
               user</a>



         </div>
      </div>
      <!--  Header BreadCrumb -->
      <?php 

          if (isset($inserUser)) {
              echo e($inserUser);
              
          }

         ?>
      <!-- Create New User -->
      <div class="main-content">

         <div class="card">
            <div class="card-body mt-5 mb-5">

               <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">

                  <div class="form-group row">
                     <div class="col-md-2">Name</div>
                     <div class="col-md-8">
                        <input id="name" type="text" placeholder="Please enter your name" class="form-control"
                           name="name" value="" autofocus="">

                     </div>
                  </div>



                  <div class="form-group row">
                     <div class="col-md-2">E-Mail Address</div>
                     <div class="col-md-4">
                        <input id="email" type="email" placeholder="Enter your Email please" class="form-control"
                           name="email" value="" autofocus="">

                     </div>
                  </div>


                  <div class="form-group row">
                     <div class="col-md-2">Password</div>
                     <div class="col-md-8">
                        <input id="password" type="password" placeholder="Enter your Password" class="form-control"
                           name="password" value="" autofocus="">

                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-md-2">Confirm Password</div>
                     <div class="col-md-8">
                        <input id="confirm_password" type="text" placeholder="Please confirm your password"
                           class="form-control" name="confirm_password" value="" autofocus="">

                     </div>
                  </div>
                  <?php if (  $access  ) { ?>
                  <div class="form-group row">
                     <div class="col-md-2">Role</div>
                     <div class="col-md-4">
                        <select class="form-control" id="rolename" name="rolename">
                           <option>Select user Role</option>
                           <?php 

                                    $rolelist = $rol->selectAllRole();
                                    if ($rolelist) {
                                        
                                        while ($result = $rolelist->fetch_assoc()) {
                                            
                                          

                                 ?>

                           <option value="<?php echo $result['rolename']; ?>"><?php echo $result['rolename']; ?>
                           </option>
                           <?php }}else{  ?>

                           <option>No user Role created</option>
                           <?php } ?>

                        </select>
                     </div>
                  </div>
                  <?php }else{ ?>

                  <input type="hidden" value="Only user" name="rolename">


                  <?php } ?>
                  <div class="form-group row">
                     <div class="col-md-2">Status</div>
                     <div class="col-md-4">
                        <select class="form-control" id="status" name="status">
                           <option>Select user Status</option>
                           <option value="0">Active</option>
                           <option value="1">Deactive</option>

                        </select>
                     </div>
                  </div>



                  <div class="form-group row">
                     <div class="col-md-2">Account create Date</div>
                     <div class="col-md-4">

                        <div class="input-group date" id="id_0">
                           <input type="text" name="create_date" value="" class="form-control" />
                           <span class="input-group-text">
                              <i class="icofont-ui-calendar"></i>
                           </span>
                        </div>

                     </div>
                  </div>

                  <div class="form-group pt-2 row">
                     <div class="col-md-2"></div>
                     <div class="col-md-4">
                        <button class="btn btn-success" type="submit" name="submit">Create
                           User</button>
                        <button class="btn btn-warning text-white" type="reset">Reset</button>
                     </div>
                  </div>

               </form>

            </div>
         </div>
      </div>
      <!-- Create New User-->


      <div class="row mt-3">

         <div class="col-md-12">
            <div class="card ">
               <div class="card-body footer-p">


               </div>
            </div>
         </div>


      </div>


   </div>
   <!--  main-content -->
   </div>

</section>

<!--====== End Main Wrapper Section======-->


<?php include 'app/inc/footer.php'; ?>