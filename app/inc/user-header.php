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
$COLOR_MODE = 'light';

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
      <div class="header">


         <div class="navigation">
            <nav class="navbar navbar-expand-lg navbar-bg">

               <div class="brand-logo">
                  <a class="navbar-brand" href="userdash.php" id="menu-action">
                     <?php 

              $header_contents = $fr->selectfrontendpart();
              if ($header_contents) {
                while ($result = $header_contents->fetch_assoc()) {
                

             ?>
                     <!--====== App Name ======-->
                     <span>
                        <?php if (isset($result['logo'])) { ?>
                        <img width="40" align='middle' src="assets/images/icons/favicon.png" alt="your image"
                           title='' />
                        <?php }else{ ?>
                        <img width="40" align='middle' src="assets/images/icons/favicon.png" alt="your image" title=''>
                        <?php } ?>

                        <?php  if (isset($result['app_name'])) {
              echo $result['app_name'];
            } else{?>
                     </span>
                     <span>Admin Panel</span>
                     <?php }} }?>

                  </a>
                  <div id="nav-toggle">
                     <div class="cta">
                        <div class="toggle-btn type1"></div>
                     </div>
                  </div>
               </div>


               <div class="for-mobile d-mobile">
                  <a href="#mobile-authentication" id="mobile-toggle"><span></span></a>

                  <div id="mobile-authentication">
                     <ul>
                        <li>
                           <span>
                              <img width="70" align='middle' src="app/uploads/userAvatar/User.png" alt="your image"
                                 title='' />
                           </span>
                        </li>
                        <li><span><strong>Welcome!</strong><?php echo $userName = Session::get('userName'); ?></span>
                        </li>
                        <li><a href="user-account.php?myid=<?php echo Session::get("userid")?>">
                              <i class="bi bi-person-circle"></i>
                              Account Settings</a></li>
                        <li><a href="?action=logout&&sunset=id">
                           <i class="bi bi-box-arrow-left"></i>
                        Logout</a>
                        </li>
                     </ul>
                  </div>
               </div>

               <!--<div class="collapse navbar-collapse pr-3" id="#">-->
                  <ul class="navbar-nav user-info d-desktop ml-auto mt-2 mt-lg-0">
                     <li class="nav-item dropdown show">
                        <a href="#" class="navbar-nav-link dropdown-toggle text-light account" data-toggle="dropdown"
                           aria-expanded="true">
                           <div class="user-photo">
                              <img width="70" align='middle' src="app/uploads/userAvatar/User.png" alt="your image"
                                 title='' />
                           </div>
                           <strong>Welcome ! </strong><?php echo $userName = Session::get('userName'); ?>

                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                           <a href="user-account.php?myid=<?php echo Session::get("userid")?>" class="dropdown-item">
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
            </nav>

         </div>
      </div>
   </header>
   <!--====== End Header Section======-->