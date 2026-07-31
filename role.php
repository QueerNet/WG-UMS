<?php include 'app/inc/header.php'; ?>
<?php Session::requireAdmin($access); ?>

<?php 
  
  // Delete Role By Id 
$delrole = isset($_GET['delrole']) ? $_GET['delrole'] : '';
if (isset($delrole) && is_numeric($delrole) && isset($_GET['remove']) == 'removeid') {
        $delrole = preg_replace('/[^a-zA-Z0-9-]/', '', $delrole);
        $delRole = $rol->deleteRoleById($delrole);
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

               <li class="breadcrumb-item active" aria-current="page">Roles</li>
            </ol>
         </nav>
         <div class="create-item">
            <a href="createrole.php" class="theme-primary-btn btn btn-primary"><i class="bi bi-plus-circle-fill"></i> &nbsp Add New
               Role</a>
         </div>
      </div>
      <!--  Header BreadCrumb -->
      <?php 

            if (isset($delRole)) {
                echo $delRole;
                
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
                        <table id="roleTable" class="table table-striped table-borderless" style="width:100%">
                           <thead>
                              <tr>
                                 <th>SL</th>
                                 <th>Display Name</th>
                                 <th>Role Name</th>
                                 <th>Status</th>
                                 <th class="text-center">Action</th>
                              </tr>
                           </thead>
                           <tbody>

                              <?php 

                                    $rolelist = $rol->selectAllRole();
                                    if ($rolelist) {
                                        $i = 0;
                                        while ($result = $rolelist->fetch_assoc()) {
                                            $i++;
                                          

                                 ?>
                              <tr>
                                 <td class="pt-3"><?php echo $i; ?></td>
                                 <td class="pt-3"><?php echo $result['roledname']; ?></td>
                                 <td class="pt-3"><span
                                       class="badge badge-lg badge-secondary text-white"><?php echo $result['rolename']; ?></span>
                                 </td>
                                 <td class="pt-3">
                                    <?php if ($result['status'] == '0') {?>
                                    <span class="badge badge-lg badge-success text-white">Active</span>
                                    <?php }elseif($result['status'] == '1'){  ?>
                                    <span class="badge badge-lg badge-warning text-white">Deactive</span>
                                    <?php } ?>

                                 </td>


                                 <td class="text-center pt-3">
                                    <a class="btn btn-info"
                                       href="editrole.php?roleid=<?php echo $result['roleid']; ?>">Edit Role</a>
                                    <?php if ($result['rolename'] == "sysadmin") { ?>
                                    <?php if (  $access  ) { ?>
                                    <a class="btn btn-danger" onclick="return confirm('You can not Remove account !')"
                                       href="#">&nbspNo Action&nbsp</a>
                                    <?php } ?>
                                    <?php }else{ ?>

                                    <?php if (  $edit ) { ?>
                                    <a class="btn btn-danger" onclick="return confirm('Are you sure to Delete ?')"
                                       href="?remove=removeid&&delrole=<?php echo $result['roleid']; ?>">Delete Role</a>
                                    <?php } ?>

                                    <?php } ?>


                                 </td>
                              </tr>

                              <?php }}else{  ?>

                              <tr>
                                 <td colspan="5" class="text-center">No role created yet !</td>
                              </tr>
                              <?php } ?>





                        </table>
                     </div>
                  </div>
               </div>
            </div>

         </div>

         <!-- Users DataTable-->



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