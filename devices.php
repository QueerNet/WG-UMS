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
        


            <?php
            // List all devices owned by UID
            $devices = $vpn->wg_list_devices($_SESSION['userid']);
            
            ?>

    <!-- Add device -->
    <div class="row wo animated fadeInUp mt-3">
        <div class="col-md-12">
            <div class="card " style="padding: 20px;">
                <h4>Add Device</h4>
                <div class="row g-3 align-items-center">
                    <form class="form-inline">
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="devid" class="sr-only">Device name</label>
                        <input type="text" class="form-control" id="devid" placeholder="Device Name">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">+ Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End add device -->


    <!-- Devices DataTable-->
    <div class="row wo animated fadeInUp mt-3">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-body mt-3">
                <div class="table-responsive">
                <table id="usersTable" class="table table-striped table-borderless" style="width:100%">
                    <thead>
                        <tr>
                            <th>Device</th>
                            <th>Name</th>
                            <th>Connect</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($devices) {
                            $i = 0;
                            foreach ($devices as $device) {
                                $NAME = $device['devid'];
                                $i++;
                            ?>
                            <tr>
                                <td class="pt-4"><?php echo $i ?></td>
                                <td class="pt-4"><?php echo $NAME ?></td>
                                </td>
                                <td class="pt-4">
                                <button type="button" style="padding: .2rem .5rem !important;" class="btn btn-success">
                                    <img height="50px" src="./assets/images/icons/OpenQR.svg">
                                </button>
                                <button type="button" class="btn btn-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-fill" viewBox="0 0 16 16">
                                    <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"></path>
                                    </svg>&nbspSend
                                </button>
                                </td>
                                <td class="text-center pt-3">
                                <a class="btn btn-info" href="">&nbspEdit&nbsp</a>
                                <!--<a class="btn btn-info" href="">&nbspEdit&nbsp</a>-->
                                <a class="btn btn-warning text-white"
                                    onclick="return confirm('Are you sure you want to deactivate ___?')"
                                    href="">&nbspDeactive&nbsp</a>
                                <a class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete ___?')"
                                    href="">&nbspDelete&nbsp</a>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="7" class="text-center">No devices added yet</td>
                        </tr>
                        <?php 
                        }
                        ?>


                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- End devices DataTable-->

    </div>
    </div>
    <!-- end main-content -->

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