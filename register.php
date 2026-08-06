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

   <?php 

        $header_contents = $fr->selectfrontendpart();
        if ($header_contents) {
          while ($result = $header_contents->fetch_assoc()) {
          

       ?>
   <!--====== Title ======-->
   <title><?php  if (isset($result['title'])) {
        echo $result['title'];
      } ?></title>
   <!--====== Favicon Icon ======-->
   <link rel="shortcut icon" href="<?php  if (isset($result['favicon'])) {
        echo $result['favicon'];
      } else{ echo "assets/images/icons/favicon.png";}?>" type="image/png">
   <?php }} 
   
   include ($filepath.'/../inc/loadassets.php');
   
   ?>





</head>

<body>

   <!-- Preloader -->
   <div class="spinner_body">
      <div class="spinner"></div>
   </div>

   <!-- Preloader -->





   <!--====== Start Main Wrapper Section======-->
   <div class="wrap">
      <!-- page BODY -->
      <!-- ========================================================= -->
      <div class="page-body animated slideInDown">
         <!-- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= -->
         <!--LOGO-->
         <div class="login-text">
            <?php 

        $header_contents = $fr->selectfrontendpart();
        if ($header_contents) {
          while ($result = $header_contents->fetch_assoc()) {
          

       ?>
            <!--====== Title ======-->
            <h3 class="text-center">Register</h3>
            <!--====== Favicon Icon ======-->



            <?php }} else{?>

            <h3 class="text-center">
               <span class="benzi">BENZI</span> - Login/User Management
            </h3>
            <?php } ?>
         </div>
         <div class="box">
            <!--SIGN IN FORM-->
            <div class="panel mb-none">
               <div class="panel-content bg-scale-0">
                  <form method="POST" id="register_user" action="" role="form">
                     <div class="form-group mt-md">
                        <div id="msg"></div>
                        <?php if (isset($register)) {
                               echo $register;
                           }
                           
                           
                           
                           ?>
                     </div>
                     <div class="form-group mt-md">
                        <span class="input-with-icon">
                           <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                           <i class="bi bi-person-fill"></i>
                        </span>
                     </div>
                     <div class="form-group mt-md">
                        <span class="input-with-icon">
                           <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                           <i class="bi bi-envelope-at-fill"></i>
                        </span>
                     </div>
                     <div class="form-group">
                        <span class="input-with-icon">
                           <input type="password" name="password" class="form-control" id="password"
                              placeholder="Password">
                           <i class="bi bi-shield-lock-fill"></i>
                        </span>
                     </div>
                     <div class="form-group">
                        <span class="input-with-icon">
                           <input type="text" class="form-control" name="confirm_password" id="confirm_password"
                              placeholder="Confirm Password">
                           <i class="bi bi-shield-lock"></i>
                        </span>
                     </div>
                     <div class="form-group">
                        <button class="btn btn-primary w-100" type="submit"
                           name="register">Register</button>
                     </div>

                     <!--Submit -->
                     <div class="form-group text-center">Have an account? <a href="login.php">Sign In</a>
                     
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