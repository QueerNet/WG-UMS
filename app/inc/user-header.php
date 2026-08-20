<?php
ob_start();
$filepath = realpath(dirname(__FILE__));
include ($filepath.'/../lib/Session.php');
Session::init();
Session::checkUserSession();
include ($filepath.'/../lib/Database.php');
include ($filepath.'/../helpers/Format.php');

spl_autoload_register(function($class){
   $class = str_replace('\\', '/', $class);
  include_once 'app/classes/'.$class.".php";
});

$db = new Database();
$fm = new Format();
$usr = new Users();
$rol = new Roles();
$prm = new Permissions();
$app = new App();
$apa = new AppAutho();
$fr = new Frontend();
$chn = new Changepassword();

// Set dark vs light mode
$COLOR_MODE = 'dark';

// Get user's preferred theme
$userid = $_SESSION['userid'];
$query = "SELECT theme FROM USERS WHERE userid = '$userid';";
$result = $db->select($query);
$field = $result->fetch_assoc();
$THEME = $field['theme'];


header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); 
header("Pragma: no-cache"); 
header("Expires: Mon, 6 Dec 1977 00:00:00 GMT"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
?>

<?php 

$userEmail = Session::get('userEmail');
$userid = Session::get('userid');
$rolename =  Session::get("rolename");
 ?>
<?php 
   
   $ROLE = $rol->selectPermissionItem($userEmail)->fetch_assoc();

      if (isset($ROLE)) {

         switch ($ROLE['ROLENAME']) {
            case 'Only user':
               $access = FALSE;
               $create = FALSE;
               $show = FALSE;
               $edit = FALSE;
               $delete = FALSE;
               $banactive = FALSE;
               $useronly = TRUE;
               $sysadmin = FALSE;
               break;
            case 'sysadmin':
               $access = TRUE;
               $create = TRUE;
               $show = TRUE;
               $edit = TRUE;
               $delete = TRUE;
               $banactive = TRUE;
               $useronly = FALSE;
               $sysadmin = TRUE;
               break;
            default:
               $access = FALSE;
               $create = FALSE;
               $show = FALSE;
               $edit = FALSE;
               $delete = FALSE;
               $banactive = FALSE;
               $useronly = TRUE;
               $sysadmin = FALSE;
         }
      }

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
   <link rel="shortcut icon" href="assets/images/icons/favicon.png" type="image/png">
   <?php }} 
   

   include ('loadassets.php');
   ?>
   




</head>

<body>

   <!-- Preloader -->
   <div class="spinner_body">
      <div class="spinner"></div>
   </div>
   <!-- Preloader -->

   
   <?php 
    if (isset($_GET['action']) && $_GET['action'] == "logout" && $_GET['sunset'] == "id") {
        $userid = Session::get('userid');
        $update_off = $usr->userActive_OFF($userid);
        $logOut = $usr->userLogOut();
        
    }
 ?>



<!--====== Start Header Section======-->
<header>
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <!-- Branding -->
      <div id="branding" style="width: 250px;" class="flex-shrink-0">
         <a class="navbar-brand" href="userdash.php" id="menu-action">
            <?php 
            $header_contents = $fr->selectfrontendpart();
            if ($header_contents) {
               while ($result = $header_contents->fetch_assoc()) {
            ?>
            <!--====== App Name ======-->
            <span>
               <?php if (isset($result['logo'])) { ?>
               <img align='middle' src="assets/images/icons/favicon.png" alt="your image"
                  title='' />
               <?php }else{ ?>
               <img align='middle' src="assets/images/icons/favicon.png" alt="your image" title=''>
               <?php } ?>

               <?php  if (isset($result['app_name'])) {
                  echo $result['app_name'];
               } else{?>
            </span>
            <span>Portal</span>
            <?php }}}?>
         </a>
      </div>
      <!-- Branding -->

      <!-- Sidebar hamburger -->
       <button class="flex-shrink-0 rounded-0 navbar-toggler" type="button" data-bs-toggle="offcanvas"
            style="width: 50px;"
            data-bs-target="#wrapper-sidebar" aria-controls="wrapper-sidebar"
            aria-label="Toggle sidebar">
         <span class="navbar-toggler-icon"></span>
      </button>
      <!-- Sidebar hamburger -->

      <!-- Middle space -->
      <div class="flex-grow-1"></div>
      <!-- Middle space -->

      <!-- Account toggle -->
      <!-- <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
      </button> -->
      <!-- Account toggle -->
         
      <!-- Account dropdown -->
      <div class="ms-auto pr-3">
         <ul class="user-info ms-auto mt-2 mt-lg-0">
            <li class="nav-item dropdown" style="padding-right:1vw;">
               <a href="#" class="navbar-nav-link dropdown-toggle text-light account" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <div class="user-photo">
                     <img width="70" align='middle' src="app/uploads/userAvatar/User.png" alt="your image"
                        title='' />
                  </div>
                  <strong id="greeting"><?php echo $userName = Session::get('userName'); ?> </strong>
               </a>
               <div class="dropdown-menu dropdown-menu-right">
                  <a href="account.php?myid=<?php echo Session::get("userid")?>" class="dropdown-item">
                     <i class="bi bi-person-circle"></i>
                     Account Settings</a>
                  <div class="menu-dropdown-divider"></div>
                  <a class="dropdown-item" href="?action=logout&&sunset=id">
                     <i class="bi bi-box-arrow-left"></i>
                        Logout</a>
               </div>
            </li>
         </ul>
      </div>
      <!-- Account dropdown -->
   </nav>
</header>
<!--====== End Header Section======-->