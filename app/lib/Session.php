<?php


class Session{
  
   public static function init() {
       $stat = session_status();
       if ($stat==1) {
        session_start();
       }
   }
   
   public static function set($key, $val){
    $_SESSION[$key] = $val;
   }

   public static function get($key){
    if (isset($_SESSION[$key])) {
      return $_SESSION[$key];
    } else {
      return false;
    }
   }

   public static function checkUserSession(){
    self::init();
    if (self::get("userLogin") == false) {
      self::destroy();
      echo "<script>location.href='login.php';</script>";
    }
   }

   public static function checkSession(){
    self::init();
    if (self::get("login") == false) {
      self::destroy();
      echo "<script>location.href='dashboard.php';</script>";
    }
   }

   public static function checkUserLogin(){
    self::init();
    if (self::get("userLogin") == true) {
      echo "<script>location.href='dashboard.php';</script>";
    }
   }

   public static function destroy(){
    session_destroy();
   
     echo "<script>location.href='login.php';</script>";
    session_unset();

   }

   public static function requireAdmin($access) {
    self::init();
    if ($access !== true) {
        ob_end_clean();
        header("Location: userdash.php?denied=1");
        exit();
    }
   }
}

?>
