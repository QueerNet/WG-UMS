<?php include 'app/inc/user-header.php' ?>



<!--====== Start Main Wrapper Section======-->
<section class="d-flex" id="wrapper">

    <?php include 'app/inc/user-sidebar.php' ?>

    <div class="page-content-wrapper">
    
    <!--  main-content -->
    <div class="main-content">

        <div class="content-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="userdash.php"><i class="bi bi-house-door-fill"></i>Home</a></li>
              </ol>
            </nav>
        </div>
        






         <div class="card">
            <div class="card-body mt-3 mb-5">

               <div class="viewuser row">

                  <div class="col-md-6">
                     <div class="form-group row">

                     </div>
                     <div class="form-group row">
                        <div class="col-md-4">Name</div>
                        <div class="col-md-8">

                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-md-4">Theme</div>
                        <div class="col-md-8">
                        <input>dropdown here</input>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6 d-desktop">


                  </div>



               </div>

            </div>
         </div>
      </div>















        

    </div>
    </div>
    <!--  main-content -->


</section>

   <?php 

// Delete Role By Id 

if (isset($_GET['delid']) && isset($_GET['remove'])) {
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

<?php
include 'app/inc/footer.php'; ?>