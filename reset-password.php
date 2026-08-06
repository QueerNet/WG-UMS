<?php

$filepath = realpath(dirname(__FILE__));
include ($filepath.'/app/lib/Session.php');
Session::init();
Session::checkUserLogin();
include ($filepath.'/app/lib/Database.php');

spl_autoload_register(function($class){
  include_once 'app/classes/'.$class.".php";
});

$db = new Database();
$usr = new Users();
$fr = new Frontend();


$THEME = 'dark';
$COLOR_MODE = 'dark';

 ?>
<?php
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); 
header("Pragma: no-cache"); 
header("Expires: Mon, 6 Dec 1977 00:00:00 GMT"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
?>



<!DOCTYPE html>
<html lang="en" data-bs-theme="<?php echo $COLOR_MODE?>">

<head>
    <!-- Preloader styles -->
    <style>
    .spinner_body{position:fixed;inset:0;margin:auto;z-index:999999;background:#0a0a0a;height:100vh}
    .spinner{width:80px;height:80px;border:2px solid #f3f3f3;border-top:3px solid #2a2a2a;border-radius:100%;position:absolute;right:50%;top:45%;animation:spin 1s infinite linear}
    @keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
    </style>
    <!-- Preloader styles -->
   
    <!--====== Required meta tags ======-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">

      <!--====== Title ======-->
      <title>Reset Password</title>
    <?php
    include('app/inc/loadassets.php')
    ?>
  

</head>
<body>

<!-- Prealoder -->
<div class="spinner_body">
   <div class="spinner"></div>  
</div>

<!-- Prealoder -->





<!--====== Start Main Wrapper Section======-->
<div class="wrap">
    <!-- page BODY -->
    <!-- ========================================================= -->
    <div class="page-body  animated slideInDown">
        <!-- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= -->
        <!--LOGO-->
        <div class="login-text">

      <!--====== Title ======-->
      <h3 class="text-center">Reset Password</h3>
    <!--====== Favicon Icon ======-->




        </div>
        <div class="box">
            <!--FORGOT PASSWPRD FORM-->
            <div class="panel mb-none">
                <div class="panel-content bg-scale-0">
                    <form method="POST" id="reset_password" action=""  role="form" >
                        <h4 class="text-center">Forgot your password?</h4>
                        <div class="form-group mt-md">
                           <div id="msg"></div>
                          
                        </div>
                        <div class="form-group mt-3">
                            <span class="input-with-icon">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                                <i class="bi bi-envelope-at-fill"></i>
                            </span>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="reset_password" class="btn btn-primary w-100"> Reset Password</button>
                        </div>
                        <div class="form-group text-center">
                            You remembered? <a href="login.php">Sign in!</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= -->
    </div>
</div>
<!--====== End Main Wrapper Section======-->




    <!--====== JQuery from CDN ======-->
    <script src="assets/js/jquery.min.js"></script>

    <!--====== Bootstrap js ======-->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/popper.min.js"></script>

    <!--====== datepicker js ======-->
    <script src="assets/js/moment-with-locales.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>

    <!--====== select2.min.js ======-->
    <script src="assets/js/select2.min.js"></script>  

    <!--====== dataTables js ======-->
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>

    <!--====== Chart.min js ======-->
    <script src="assets/js/Chart.bundle.min.js"></script>

    <!--====== wow.min js ======-->
    <script src="assets/js/wow.min.js"></script>
    <!--====== Main js ======-->
    <script src="assets/js/script.js"></script>


</body>
</html>