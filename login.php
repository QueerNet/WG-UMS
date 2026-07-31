<?php

$filepath = realpath($_SERVER['HOME']);
include ($filepath.'/app/lib/Session.php');
Session::init();
Session::checkUserLogin();
include (__DIR__.'/app/lib/Database.php');
include (__DIR__.'/app/config/config.php');

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


   <?php 









if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
   $chkUserLogin = $usr->userLoginAuthentication($_POST);

}


 ?>


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
            <h3 class="text-center">Login</h3>
            <!--====== Favicon Icon ======-->



            <?php }} else{?>

            <h3 class="text-center">
               <span class="benzi">QLS</span> - Login
            </h3>
            <?php } ?>




         </div>
         <div class="box">
            <!--SIGN IN FORM-->
            <div class="panel mb-none">
               <div class="panel-content bg-scale-0">
                  <form action="" method="post" id="login-user">
                     <div class="form-group mt-md">
                        <?php if (isset( $chkUserLogin)) {
                               echo  $chkUserLogin;
                           } ?>

                     </div>
                     <div class="form-group mt-md">
                        <span class="input-with-icon">
                           <?php if(isset($_COOKIE["email"])) {  ?>
                           <input type="email" class="form-control" name="email" id="email"
                              value="<?php echo $_COOKIE["email"]; ?>">
                           <?php }else{ ?>
                           <input type="email" class="form-control" name="email" id="email"
                              placeholder="Enter your E-Mail ...">
                           <?php } ?>
                           <i class="bi bi-envelope-at-fill"></i>
                        </span>
                     </div>
                     <div class="form-group">
                        <span class="input-with-icon">
                           <?php if(isset($_COOKIE["password"])) {  ?>
                           <input type="password" class="form-control" name="password" id="password"
                              value="<?php echo $_COOKIE["password"]; ?>">
                           <?php }else{ ?>
                           <input type="password" class="form-control" name="password" id="password"
                              placeholder="Enter your Password ...">
                           <?php } ?>



                           <i class="bi bi-lock-fill"></i>
                        </span>
                     </div>
                     <div class="form-group">
                        <div class="checkbox-custom checkbox-primary">
                           <input type="checkbox" name="remember" id="remember" <?php if(isset($_COOKIE["email"])) { ?>
                              checked <?php } ?> />
                           <label class="check" for="remember">Remember me</label>
                        </div>
                     </div>
                     <div class="form-group">
                        <button type="submit" name="login" id="login"
                           class="btn text-white theme-primary-btn btn-primary btn-block">Sign In</button>
                     </div>
                     <div class="form-group text-center">
                        <a href="reset-password.php">Forgot password?</a>
                        <hr />
                        <span>Don't have an account?</span>
                        <a href="register.php" class="  mt-sm">Register</a>
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
   <script src="assets/js/plugins.js"></script>
   <script src="assets/js/script.js"></script>

</body>

</html>