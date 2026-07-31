<?php include 'app/inc/user-header.php' ?>


<?php

$vpn = new VPN();

?>


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