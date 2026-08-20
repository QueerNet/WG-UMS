<?php

$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../lib/Database.php');
include_once ($filepath.'/../helpers/Format.php');
include_once ($filepath.'/../classes/sendEmail.php');

use sendEmail\sendEmail;




/**
 * Visitor Users Class
 */
class Users{

	private $apptable       = "APP_AUTH";
	private $table 		= "USERS";
	private $table_session 	= "ONLINE";
	private $db;
	private $fm;

	// Construct auto Load
	public function __construct(){
		$this->db = new Database();
		$this->fm = new Format();
	}
    
   /**
	 * Suppose, you are browsing in your localhost 
	 * http://localhost/myproject/index.php?id=8
	 */
	public function getBaseUrl() 
	{
		// output: /myproject/index.php
		$currentPath = $_SERVER['PHP_SELF']; 

		// output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
		$pathInfo = pathinfo($currentPath); 

		// output: localhost
		$hostName = $_SERVER['HTTP_HOST']; 

		// output: http://
		$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';

		// return: http://localhost/myproject/
		// return $protocol.'://'.$hostName.$pathInfo['dirname']."/";
		return $protocol.'://'.$hostName;
	}



	// Users Login Method
	public function userLoginAuthentication($data){
		$email_ 		= $this->fm->validation($data['email']);
		$password_ 	= $this->fm->validation($data['password']);

		$email 		= mysqli_real_escape_string($this->db->link, $email_);
		$password 	= mysqli_real_escape_string($this->db->link, $password_);
		

		if (empty($email) OR empty($password)) {
			$msg = "<div id='flash-msg' class='alert alert-danger'><strong>Error ! </strong>Email or Password field must not be Empty!</div>";
			return $msg;		       
		}elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$msg = '<div class="alert alert-danger" id="flash-msg"><strong>Error !</strong> Invalid email address !</div>';
			return $msg;
		}else{
			$query = "SELECT * FROM $this->table WHERE email = '$email';";
			$result = $this->db->select($query);
			if ($result != false) {
				$value = $result->fetch_assoc();

					if (password_verify($password, $value['password'] )) {
						$userid = $value['userid'];
						$userOn = $this->userActive_ON($userid);
						if ($value['status'] == '1') {
							$msg = '<div class="alert alert-danger" id="flash-msg"><strong>Error !</strong>  Your Account is Disabled, conact with Author !</div>';
							return $msg;
						}else{
							Session::set("userLogin", true);
							Session::set("login", true);
							Session::set("userid", $value['userid']);
							Session::set("userName", $value['name']);
							Session::set("userEmail", $value['email']);
							Session::set("rolename", $value['rolename']);
							if ($value['rolename']=="sysadmin") {
								echo "<script>location.href='dashboard.php';</script>";
								exit();
							} else {
								echo "<script>location.href='userdash.php';</script>";
								exit();
							}
						}
					}
					else {
						# Log the authentication failure in syslog (defined in php.ini) for Fail2ban to flag
						error_log("Failed login: user=".$value['userid']." ip=$_SERVER[REMOTE_ADDR]");
						$msg = '<div class="alert alert-danger" id="flash-msg"><strong>Error!</strong>  Your password did not Match!</div>';
						return $msg;
					}
			}else{
		       $msg = "<div id='flash-msg' class='alert alert-danger'><strong>Error ! </strong>No user found for associated credentials...</div>";
		       return $msg;		       
			}
		}


	}

	// User logout Method 
	public function userLogOut(){
	    session_destroy();
	    echo "<script>location.href='login.php';</script>";
	    session_unset();
	    exit();

	}




	// newUserRegistration Method
	public function newUserRegistration($data){
		$name_ 				= $this->fm->validation($data['name']);
		$email_ 				= $this->fm->validation($data['email']);
		$password_ 			= $this->fm->validation($data['password']);
		$confirm_password_               = $this->fm->validation($data['confirm_password']);
		// $create_date_                    = $this->fm->validation($data['create_date']);

		$name 				= mysqli_real_escape_string($this->db->link, $name_);
		$email 				= mysqli_real_escape_string($this->db->link, $email_);
		$password 			= mysqli_real_escape_string($this->db->link, $password_);
		$confirm_password               = mysqli_real_escape_string($this->db->link, $confirm_password_);
		// $create_date                    = mysqli_real_escape_string($this->db->link, $create_date_);
		$create_date = date('Y-m-d H:i:s');
		
		//Set default return - fail
		$result = ['DB'=> FALSE, 'EMAIL'=> FALSE];


		$pregExp = "/^[a-z0-9_-]+(\.[a-z0-9_-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/";
		if ($name == "" || $email == "" || $password == "" || $confirm_password == "") {
	     
	        $msg =   '<div class="alert alert-danger " id="flash-msg">
	    			<strong>Error !</strong> Input fields must not be Empty!</div>';
	        echo $msg;
		       exit();
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$msg = '<div class="alert alert-danger" id="flash-msg">
    				<strong>Error !</strong> Please fill up Valid Email !</div>';
			echo $msg;
		} elseif (!preg_match($pregExp, $email)) {
			$msg = '<div class="alert alert-danger " id="flash-msg">
    				<strong>Error !</strong> Please fill up Valid Email !</div>';
			echo  $msg;
		
		} elseif (strlen($password) < '6') {
				$msg = '<div class="alert alert-danger " id="flash-msg">
	    				<strong>Error !</strong> Your Password Must Contain At Least 6 Characters !</div>';
				echo $msg;
	    } elseif (!preg_match("#[0-9]+#",$password)) {
			$msg = '<div class="alert alert-danger " id="flash-msg">
    				<strong>Error !</strong> Your Password Must Contain At Least 1 Number !</div>';
			echo $msg;
	    } elseif (!preg_match("#[a-z]+#",$password)) {
			$msg = '<div class="alert alert-danger " id="flash-msg">
    				<strong>Error !</strong> Your Password Must be Contain At Least 1 Lowercase Letter !</div>';
			echo $msg;
	    } elseif ($password != $confirm_password) {
			$msg =   '<div class="alert alert-danger " id="flash-msg">
					<strong>Error !</strong> Password did not matched, please try agian and use same password two fields.</div>';
			echo $msg;
		} else {
			$checkUserEmail = "SELECT email FROM $this->table WHERE email = '$email' LIMIT 1";
			$mailCheck = $this->db->select($checkUserEmail);
			if ($mailCheck != false) {
				$msg = '<div class="alert alert-danger" id="flash-msg">
						<strong>Error !</strong> Email already used, Please use another Email. !</div>';
				echo $msg;
				exit();
			} else {
				$base_url   = $this->getBaseUrl();
				// This is query for handle use registration permission
				$onQuery = "SELECT * FROM $this->apptable";
				$allowRegistration = $this->db->select($onQuery);
				$value = $allowRegistration->fetch_assoc();
				
				if ($value['allow_email'] === '1') {
					$msg = '<div class="alert alert-danger" id="flash-msg">
					<strong>Error !</strong> New user Registration is closed by Author !</div>';
					$result = ['DB'=> FALSE, 'EMAIL'=> FALSE];
					echo $msg;
					exit();
				} else {
					// Has password 
					$has_pass 	= password_hash($password, PASSWORD_DEFAULT);
					$query = "INSERT INTO $this->table(name,  email, password, rolename, create_date) VALUES('$name', '$email', '$has_pass', 'Only user', '$create_date')";
					$inserted_rows = $this->db->insert($query);
					$to 		= $email;
					$subject 	= 'Welcome to QLS!';
					$message 	 = "Your name is : " . strip_tags($name) . "\r\n";
					$message 	.= "Your E-mail is : " . strip_tags($email) . "\r\n";
					$message 	.= "Your account was created at : " . $create_date . "\r\n";
					$message 	.= "Message : Please visit our website to login ".$base_url." ";

					$result = ['DB'=> TRUE, 'EMAIL'=> FALSE];

					// Use our sendEmail function
					$emailer = sendEmail::sendEmail($name, $email, $subject, $message);
					$result = ['DB'=> TRUE, 'EMAIL'=> $emailer];
					return $result;
				}
			}
		}
	}






	// createNewUserData Method 
	public function createNewUserData($data, $file){
		$name_ 				= $this->fm->validation($data['name']);
		$email_ 				= $this->fm->validation($data['email']);
		$password_ 			= $this->fm->validation($data['password']);
		$confirm_password_ 	= $this->fm->validation($data['confirm_password']);
		$rolename_ 			= $this->fm->validation($data['rolename']);
		$status_ 			= $this->fm->validation($data['status']);
		$create_date_ 		= $this->fm->validation($data['create_date']);

		$name 				= mysqli_real_escape_string($this->db->link, $name_);
		$email 				= mysqli_real_escape_string($this->db->link, $email_);
		$password 			= mysqli_real_escape_string($this->db->link, $password_);
		$confirm_password 	= mysqli_real_escape_string($this->db->link, $confirm_password_);
		$rolename 			= mysqli_real_escape_string($this->db->link, $rolename_);
		$status 			= mysqli_real_escape_string($this->db->link, $status_);
		$create_date 		= mysqli_real_escape_string($this->db->link, $create_date_);




		if ($name == "" ||$email == "" ||$password == ""||$confirm_password == ""||$rolename == ""||$status == ""||$create_date == "" ) {
	     
	        $msg =   '<div class="alert alert-danger alert-dismissible" id="flash-msg">
	    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	    <strong>Error !</strong> Input fields must not be Empty!</div>';
	        return $msg;
		}elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$msg = '<div class="alert alert-danger text-center alert-dismissible" id="flash-msg">
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <strong>Error !</strong> Please fill up Valid Email !</div>';
			return $msg;
		
		    }elseif (strlen($password) <= '6') {
				$msg = '<div class="alert alert-danger text-center alert-dismissible" id="flash-msg">
	    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	    <strong>Error !</strong> Your Password Must Contain At Least 6 Characters !</div>';
				return $msg;

	    }elseif($password != $confirm_password) {
	        $msg =   '<div class="alert alert-danger alert-dismissible" id="flash-msg">
	    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	    <strong>Error !</strong> Password did not matched, please try agian and use same password two fields.</div>';
	        return $msg;
		    }else{
			    	$checkUserEmail = "SELECT email FROM $this->table WHERE email = '$email' LIMIT 1";
			    	$mailCheck = $this->db->select($checkUserEmail);
			    	if ($mailCheck != false) {
						$msg = '<div class="alert alert-danger alert-dismissible" id="flash-msg">
			    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			    <strong>Error !</strong> Email already Exist, Please use another Email for create new User. !</div>';
						return $msg;
						//exit();
			    	}else{
                                    
                                    
                                    // This is query for handle use registration permission
                                    $onQuery = "SELECT * FROM $this->apptable";
                                    $allowRegistration = $this->db->select($onQuery);
                                    $value = $allowRegistration->fetch_assoc();
                                    
                                    
			    	//move_uploaded_file($file_temp, $uploaded_image);
			    	$has_pass 	= password_hash($password, PASSWORD_DEFAULT);
			    	$query = "INSERT INTO $this->table(name, email, password, rolename,  status, create_date) VALUES('$name', '$email', '$has_pass', '$rolename', '$status', '$create_date')";
		        	$inserted_rows = $this->db->insert($query);

				    if ($inserted_rows) {
						// Select Query for only author access
						$query 	= "SELECT * FROM $this->table WHERE rolename = 'sysadmin' LIMIT 1";
						$author = $this->db->select($query);
							$getAuthor 	= $author->fetch_assoc();
							$author 	= $getAuthor['email'];

						if (Session::get('userName') == TRUE && Session::get('rolename') == TRUE) {
							//User Registration thanks giving message
							$base_url   = $this->getBaseUrl();
							$subject = 'You have been registered Successfully.';
							$message  	 = "Account user information: ". "\r\n";
							$message  	.= "User name is: " . strip_tags($name) . "\r\n";
							$message 	.= "User E-mail is: " . strip_tags($email) . "\r\n";
							$message 	.= "======================". "\r\n";
							$message  	.= "Admin information: ". "\r\n";
							$message 	.= "Account creator  : " . Session::get('userName') . "\r\n";
							$message 	.= "Account creator Role : " . Session::get('rolename') . "\r\n";
							$message 	.= "Account Registration Date : " . strip_tags($create_date) . "\r\n";
							$message 	.= "Message : Please visit our website to login ".$base_url." ";
							sendEmail::sendEmail($name, $email, $subject, $message);

						}else{
							//User Registration thanks giving message
							$base_url   = $this->getBaseUrl();
							$subject = 'You have been registered Successfully.';
							$message  	 = "Account user information: ". "\r\n";
							$message  	.= "Your name is: " . strip_tags($name) . "\r\n";
							$message 	.= "Your E-mail is: " . strip_tags($email) . "\r\n";
							$message 	.= "Your Role is: " . strip_tags($rolename) . "\r\n";
							$message 	.= "Account Registration Date : " . strip_tags($create_date) . "\r\n";
							$message 	.= "Message : Please visit our website to login ".$base_url." ";
							sendEmail::sendEmail($name, $email, $subject, $message);
						}
				    }else {
				        $msg =   '<div class="alert alert-danger alert-dismissible" id="flash-msg">
		    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		    <strong>Error !</strong> New User not Created, Something went wrong!</div>';
				        return $msg;
				    }


				}
		    }
	}



	// User Inserted Method BY Id 
	public function updateUserById($data, $file, $id){
		$id = preg_replace('/[^a-zA-Z0-9-]/', '', $id);
		$name_ 				= $this->fm->validation($data['name']);
		$email_ 				= $this->fm->validation($data['email']);
		$rolename_ 			= $this->fm->validation($data['rolename']);
		$status_ 			= $this->fm->validation($data['status']);
		$create_date_ 		= $this->fm->validation($data['create_date']);



		$name 				= mysqli_real_escape_string($this->db->link, $name_);
		$email 				= mysqli_real_escape_string($this->db->link, $email_);
		$rolename 			= mysqli_real_escape_string($this->db->link, $rolename_);
		$status 			= mysqli_real_escape_string($this->db->link, $status_);
		$create_date 		= mysqli_real_escape_string($this->db->link, $create_date_);

	

		if ($name == "" ||$email == "" ||$rolename == ""||$status == ""|| $create_date == ""  ) {
	     
	        $msg =   '<div class="alert alert-danger alert-dismissible" id="flash-msg">
	    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	    <strong>Error !</strong> Input fields must not be Empty!</div>';
	        return $msg;
		}elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$msg = '<div class="alert alert-danger text-center alert-dismissible" id="flash-msg">
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <strong>Error !</strong> Please fill up Valid Email !</div>';
			return $msg;
		
		    }else{
				
				if (!empty($file_name)) {


					    	// Update query
					    	$query = "UPDATE $this->table
					    			SET  
					    			name 	= '$name',
					    			email 			= '$email',
					    			rolename 		= '$rolename',
					    			status 			= '$status',
					    			create_date 	= '$create_date'

					    			WHERE userid = '$id'
					    	";
				        	$updated_row = $this->db->update($query);
						    if ($updated_row) {

								$query 	= "SELECT * FROM $this->table WHERE userid = '$id' LIMIT 1";
								$result = $this->db->select($query);
								$value 	= $result->fetch_assoc();
								$email 	= $value['email'];
								$name 	= $value['name'];

								// Select Query for only author access
								$query 	= "SELECT * FROM $this->table WHERE rolename = 'sysadmin' LIMIT 1";
								$author = $this->db->select($query);
									$getAuthor 	= $author->fetch_assoc();
									$author 	= $getAuthor['email'];



								if (Session::get('userName') == TRUE && Session::get('rolename') == TRUE) {
									//User Registration thanks giving message
									$base_url   = $this->getBaseUrl();
									$Date 		= new DateTime();
									$Date 		= date_format($Date, 'Y-m-d H:i:s');
									$form 		= 'mj.qls@tuta.io';
									$to 		= "$email, $author";
									$subject 	= 'Profile update notification';
									$headers 	= "From: " . strip_tags($form) . "\r\n";
									$headers 	.= "Reply-To: ". strip_tags($form) . "\r\n";
									$headers 	.= "CC: mj.qls@tuta.io\r\n";
									$headers 	.= 'MIME-Version: 1.0';
									$headers 	.= 'Content-type: text/html; charset=iso-8859-1';
									$message  	 = "Account user information: ". "\r\n";
									$message  	.= "User name is: " . strip_tags($name) . "\r\n";
									$message 	.= "User E-mail is: " . strip_tags($email) . "\r\n";
									$message 	.= "=======================". "\r\n";
									$message  	.= "Admin information: ". "\r\n";
									$message 	.= "Account creator  : " . Session::get('userName') . "\r\n";
									$message 	.= "Account creator Role : " . Session::get('rolename') . "\r\n";
									$message 	.= "Account Registration Date : " . strip_tags($Date) . "\r\n";
									$message 	.= "Message : Please visit our website to login ".$base_url." ";
									
									sendEmail::sendEmail($name, $email, $subject, $message);


									//User Registration thanks giving message
									$base_url   = $this->getBaseUrl();
									$Date 		= new DateTime();
									$Date 		= date_format($Date, 'Y-m-d H:i:s');
									$form 		= 'mj.qls@tuta.io';
									$to 		= "$email";
									$subject 	= 'Profile update notification';
									$headers = "From: " . strip_tags($form) . "\r\n";
									$headers .= "Reply-To: ". strip_tags($form) . "\r\n";
									$headers .= "CC: mj.qls@tuta.io\r\n";
									$headers .= 'MIME-Version: 1.0';
									$headers .= 'Content-type: text/html; charset=iso-8859-1';
									$message  	 = "Account user information: ". "\r\n";
									$message  	.= "Your name is: " . strip_tags($name) . "\r\n";
									$message 	.= "Your E-mail is: " . strip_tags($email) . "\r\n";
									$message 	.= "Your Role is: " . strip_tags($rolename) . "\r\n";
									$message 	.= "Profile update Date : " . strip_tags($Date) . "\r\n";
									$message 	.= "Message : Hey, ". strip_tags($name) ." Recently you have update your profile.";
									$message 	.= "Message : Please visit our website to login ".$base_url." ";
									sendEmail::sendEmail($name, $email, $subject, $message);
								}

					    }}else{
					    	
					    	$query = "UPDATE $this->table
					    			SET  
					    			name 	= '$name',
					    			email 			= '$email',
					    			rolename 		= '$rolename',
					    			status 			= '$status',
					    			create_date 	= '$create_date'
					    			WHERE userid = '$id'
					    	";
				        	$updated_row = $this->db->update($query);
						    if ($updated_row) {
								$query 	= "SELECT * FROM $this->table WHERE userid = '$id' LIMIT 1";
								$result = $this->db->select($query);
								$value 	= $result->fetch_assoc();
								$email 	= $value['email'];
								$name 	= $value['name'];


								// Select Query for only author access
								$query 	= "SELECT * FROM $this->table WHERE rolename = 'sysadmin' LIMIT 1";
								$author = $this->db->select($query);
									$getAuthor 	= $author->fetch_assoc();
									$author 	= $getAuthor['email'];



								if (Session::get('userName') == TRUE && Session::get('rolename') == TRUE) {
									//User Registration thanks giving message
									$base_url   = $this->getBaseUrl();
									$Date 		= new DateTime();
									$Date 		= date_format($Date, 'Y-m-d H:i:s');
									$form 		= 'mj.qls@tuta.io';
									$to 		= "$email, $author";
									$subject 	= 'Profile update notification';
									$headers 	= "From: " . strip_tags($form) . "\r\n";
									$headers 	.= "Reply-To: ". strip_tags($form) . "\r\n";
									$headers 	.= "CC: mj.qls@tuta.io\r\n";
									$headers 	.= 'MIME-Version: 1.0';
									$headers 	.= 'Content-type: text/html; charset=iso-8859-1';
									$message  	 = "Account user information: ". "\r\n";
									$message  	.= "User name is: " . strip_tags($name) . "\r\n";
									$message 	.= "User E-mail is: " . strip_tags($email) . "\r\n";
									$message 	.= "=======================". "\r\n";
									$message  	.= "Admin information: ". "\r\n";
									$message 	.= "Profile update by  : " . Session::get('userName') . "\r\n";
									$message 	.= "Profile updater Role : " . Session::get('rolename') . "\r\n";
									$message 	.= "Profile update Date : " . strip_tags($Date) . "\r\n";
									$message 	.= "Message : Please visit our website to login ".$base_url." ";
									sendEmail::sendEmail($name, $email, $subject, $message);

								} else {
									//User Registration thanks giving message
									$base_url   = $this->getBaseUrl();
									$Date 		= new DateTime();
									$Date 		= date_format($Date, 'Y-m-d H:i:s');
									$form 		= 'mj.qls@tuta.io';
									$to 		= "$email";
									$subject 	= 'Profile update notification';
									$headers 	= "From: " . strip_tags($form) . "\r\n";
									$headers 	.= "Reply-To: ". strip_tags($form) . "\r\n";
									$headers 	.= "CC: mj.qls@tuta.io\r\n";
									$headers 	.= 'MIME-Version: 1.0';
									$headers 	.= 'Content-type: text/html; charset=iso-8859-1';
									$message  	 = "Account user information: ". "\r\n";
									$message  	.= "Your name is: " . strip_tags($name) . "\r\n";
									$message 	.= "Your E-mail is: " . strip_tags($email) . "\r\n";
									$message 	.= "Your Role is: " . strip_tags($rolename) . "\r\n";
									$message 	.= "Profile update Date : " . strip_tags($Date) . "\r\n";
									$message 	.= "Message : Hey, ". strip_tags($name) ." Recently you have update your profile.";
									$message 	.= "Message : Please visit our website to login ".$base_url." ";
									sendEmail::sendEmail($name, $email, $subject, $message);
								}
						    } else {
						        $msg =   '<div class="alert alert-danger alert-dismissible" id="flash-msg">
				    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				    <strong>Error !</strong> User Data not Updated!</div>';
						        return $msg;
						    }
					    }
		}
	}



	// Delete User By Id 
	public function deleteUserById($id){
		$id = preg_replace('/[^a-zA-Z0-9-]/', '', $id);

 		// Select Query for get Individual Id
		$query 	= "SELECT * FROM $this->table WHERE userid = '$id' LIMIT 1";
		$result = $this->db->select($query);
		$value 	= $result->fetch_assoc();
		$email 	= $value['email'];
		$name 	= $value['name'];

		// Select Query for only author access
		$query 	= "SELECT * FROM $this->table WHERE rolename = 'sysadmin' LIMIT 1";
		$author = $this->db->select($query);
		$getAuthor 	= $author->fetch_assoc();
		$author 	= $getAuthor['email'];



		$query = "DELETE FROM $this->table WHERE userid = '$id'";
		$delete_row = $this->db->delete($query);
		if ($delete_row) {

			//User Registration thanks giving message
			$base_url   = $this->getBaseUrl();
			$Date 		= new DateTime();
			$Date 		= date_format($Date, 'Y-m-d H:i:s');
			$subject 	= 'User account was deleted.';
			$message  	 = "Account user information: ". "\r\n";
			$message  	.= "User name was: " . strip_tags($name) . "\r\n";
			$message 	.= "User E-mail was: " . strip_tags($email) . "\r\n";
			$message 	.= "======================". "\r\n";
			$message  	.= "Admin information: ". "\r\n";
			$message 	.= "Account was deleted by  : " . Session::get('userName') . "\r\n";
			$message 	.= "Role was : " . Session::get('rolename') . "\r\n";
			$message 	.= "Account Deleted Time : " . strip_tags($Date) . "\r\n";
			$message 	.= "Message : Please visit our website to login ".$base_url." ";
			$message 	.= "Message : If you believe this to be in error, reach out to the system administrator.";
	        sendEmail::sendEmail($name, $email, $subject, $message);
		} else {
			$msg =   '<div class="alert alert-danger alert-dismissible" id="flash-msg">
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				<strong>Error !</strong> Something went wrong...</div>';
			return $msg;	       
		}
	}


	// Get All Userlist Method 
	public function selectAllUsers(){
		$query = "SELECT * FROM $this->table ORDER BY userid DESC";
		$result = $this->db->select($query);
		return $result;
	}



	// View User By Id Method 
	public function getUserById($viewuser){
		//$viewuser = preg_replace('/[^a-zA-Z0-9-]/', '', $viewuser);
		$query = "SELECT * FROM $this->table WHERE userid = '$viewuser'";
		$result = $this->db->select($query);
		return $result;
	}



	// Edit User By Id Method 
	public function editUserById($edituser, $rolename){
		//if ($rolename=='user') {
			//exit();
		//}
		$editpro = preg_replace('/[^a-zA-Z0-9-]/', '', $edituser);
		$query = "SELECT * FROM $this->table WHERE userid = '$edituser' LIMIT 1";
		$result = $this->db->select($query);
		return $result;
	}


	// Select only User ID
	public function selectOnlyUesrId(){
		$query = "SELECT userid FROM $this->table LIMIT 1";
		$result = $this->db->select($query);
		return $result;
	}




	// Public funciton disable method 
	public function DisableUserById($disid){
		$query="
			UPDATE $this->table 
			SET 
			status = '1'
			WHERE userid = '$disid'";
		$update_row = $this->db->update($query);
	 	if ($update_row) {

			$query 	= "SELECT * FROM $this->table WHERE userid = '$disid' LIMIT 1";
			$result = $this->db->select($query);
			$value 	= $result->fetch_assoc();
			$email 	= $value['email'];
			$name 	= $value['name'];
			// Select Query for only author access
			$query 	= "SELECT * FROM $this->table WHERE rolename = 'sysadmin' LIMIT 1";
			$author = $this->db->select($query);
				$getAuthor 	= $author->fetch_assoc();
				$author 	= $getAuthor['email'];

			//User Registration thanks giving message
			$base_url   = $this->getBaseUrl();
			$Date 		= new DateTime();
			$Date 		= date_format($Date, 'Y-m-d H:i:s');
			$form 		= 'mj.qls@tuta.io';
			$to 		= "$author";
			$subject 	= 'User account was Disable';
			$headers 	= "From: " . strip_tags($form) . "\r\n";
			$headers 	.= "Reply-To: ". strip_tags($form) . "\r\n";
			$headers 	.= "CC: mj.qls@tuta.io\r\n";
			$headers 	.= 'MIME-Version: 1.0';
			$headers 	.= 'Content-type: text/html; charset=iso-8859-1';
			$message  	 = "Account user information: ". "\r\n";
			$message  	.= "Account name is: " . strip_tags($name) . "\r\n";
			$message 	.= "Account E-mail is: " . strip_tags($email) . "\r\n";
			$message 	.= "Account Status is:  Disable". "\r\n";
			$message 	.= "==========================". "\r\n";
			$message  	.= "Admin information: ". "\r\n";
			$message 	.= "Account was disable by  : " . Session::get('userName') . "\r\n";
			$message 	.= "Account Role was : " . Session::get('rolename') . "\r\n";
			$message 	.= "Account disabled Time is: " . strip_tags($Date) . "\r\n";
			$message 	.= "Message : Please visit our website to login ".$base_url." ";
			

	        sendEmail::sendEmail($name, $email, $subject, $message);
	 	}else{
			$msg =   '<div class="alert alert-danger alert-dismissible" id="flash-msg">
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				<strong>Error !</strong> Something went wrong...</div>';
			return $msg;
	 	}
	}


	// Public funciton Enable method 
	public function EnableUserById($enid){
		$query="
			UPDATE $this->table
			SET 
			status = '0'
			WHERE userid = '$enid'";
		$update_row = $this->db->update($query);
	 	if ($update_row) {


	 		// Select Query for get Individual Id
			$query 	= "SELECT * FROM $this->table WHERE userid = '$enid' LIMIT 1";
			$result = $this->db->select($query);
			$value 	= $result->fetch_assoc();
			$email 	= $value['email'];
			$name 	= $value['name'];

			// Select Query for only author access
			$query 	= "SELECT * FROM $this->table WHERE rolename = 'sysadmin' LIMIT 1";
			$author = $this->db->select($query);
				$getAuthor 	= $author->fetch_assoc();
				$author 	= $getAuthor['email'];


			//User Registration thanks giving message
			$base_url   = $this->getBaseUrl();
			$Date 		= new DateTime();
			$Date 		= date_format($Date, 'Y-m-d H:i:s');
			$form 		= 'mj.qls@tuta.io';
			$to 		= "$author";
			$subject 	= 'User account Activated';
			$headers 	= "From: " . strip_tags($form) . "\r\n";
			$headers 	.= "Reply-To: ". strip_tags($form) . "\r\n";
			$headers 	.= "CC: mj.qls@tuta.io\r\n";
			$headers 	.= 'MIME-Version: 1.0';
			$headers 	.= 'Content-type: text/html; charset=iso-8859-1';
			$message  	 = "Account user information: ". "\r\n";
			$message  	.= "Account name is: " . strip_tags($name) . "\r\n";
			$message 	.= "Account E-mail is: " . strip_tags($email) . "\r\n";
			$message 	.= "Account Status is:  Activated". "\r\n";
			$message 	.= "========================". "\r\n";
			$message  	.= "Admin information: ". "\r\n";
			$message 	.= "Account was Activated by  : " . Session::get('userName') . "\r\n";
			$message 	.= "Account Role was : " . Session::get('rolename') . "\r\n";
			$message 	.= "Account Activated Time : " . strip_tags($Date) . "\r\n";
			$message 	.= "Message : Please visit our website to login ".$base_url." ";
			
	        sendEmail::sendEmail($name, $email, $subject, $message);
	 	}else{
			$msg =   '<div class="alert alert-danger alert-dismissible" id="flash-msg">
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				<strong>Error !</strong> Something went wrong...</div>';
			return $msg;
	 	}
	}



	// New Users method
	public function newUsers(){
		$query = "SELECT * FROM $this->table WHERE create_date > DATE_SUB(NOW(), INTERVAL 1 WEEK) AND status = '0' ORDER BY userid DESC";
		$result = $this->db->select($query);
		return $result; 
	}



	// Only for Active user select
	public function onlyActiveUsers(){
		$query = "SELECT * FROM $this->table WHERE lastactivity = '1' ORDER BY userid DESC";
		$result = $this->db->select($query);
		return $result; 
	}

	// Band Or Deactive Users Method
	public function bandUsers(){
		$query = "SELECT * FROM $this->table WHERE status = '1'";
		$result = $this->db->select($query);
		return $result; 
	}



	// Total Users Method
	public function totalUsers(){
		$query = "SELECT * FROM $this->table";
		$result = $this->db->select($query);
		return $result; 
	}


	// Select All Author Query
	public function selectAuthorFrom(){
		$query = "SELECT rolename FROM $this->table WHERE rolename ='sysadmin' ";
		$result = $this->db->select($query);
		return $result; 
	}


	// Band Users Method
	public function onlyBandUsers(){
		$query = "SELECT * FROM $this->table WHERE status = '1'  ORDER BY name DESC";
		$result = $this->db->select($query);
		return $result; 
	}





	// For Chart Js statics Query Select Monthly user registration query
	public function getMonthlyNewUser(){
		$query = "SELECT * FROM $this->table WHERE create_date > DATE_SUB(NOW(), INTERVAL 1 MONTH) ORDER BY userid DESC";
		$result = $this->db->select($query);
		return $result; 
	}

	// Generate Custom New Password
	public function randomPasswordGenerator() {
	    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 10; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}

	

	// User Reset Password 
	public function userResetPassword($data){
		$email 				= $this->fm->validation($data['email']);
		$email 				= mysqli_real_escape_string($this->db->link, $email);
		$pregExp = "/^[a-z0-9_-]+(\.[a-z0-9_-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/";

		if ($email == "" ) {
	     
	        $msg =   '<div class="alert alert-danger " id="flash-msg">
	    <strong>Error !</strong> E-mail field must not be Empty !</div>';
	        echo $msg;
		       exit();
		}elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$msg = '<div class="alert alert-danger" id="flash-msg">
    <strong>Error !</strong> Please fill up Valid Email !</div>';
			echo $msg;
		}elseif(!preg_match($pregExp, $email)) {
			$msg = '<div class="alert alert-danger " id="flash-msg">
    <strong>Error !</strong> Please fill up Valid Email !</div>';
			echo  $msg;
		
		}else{

	    	$checkUserEmail = "SELECT * FROM $this->table WHERE email = '$email' LIMIT 1";
	    	$mailCheck = $this->db->select($checkUserEmail);
			if ($mailCheck != false) {
				while ($value = $mailCheck->fetch_assoc()) {
					$userid 	= $value['userid'];
					$name 	 	= $value['name'];
				}

				// $text = substr($email,0, 3);
				// $rand = rand(10000, 99999);
				// $newpass = "$text$rand";
				$newpass = $this->randomPasswordGenerator();
				$password = password_hash($newpass, PASSWORD_DEFAULT);

				// Update Qiuery
		        $updateQuery = "UPDATE $this->table
		        		SET 
		        		password = '$password' 
		        		WHERE userid = '$userid'";
		        $update_pass = $this->db->update($updateQuery);


		        if ($update_pass) {

			        //User Request Password changed thanks giving message
                                $base_url   = $this->getBaseUrl();
                                $Date 		= new DateTime();
                                $Date 		= date_format($Date, 'Y-m-d H:i:s');
                                $subject        = 'Request to change your Password.';
                                $message  	 = "Account user information: ". "\r\n";
                                $message 	 = "Your name is : " . strip_tags($name) . "\r\n";
                                $message 	.= "Your E-mail is : " . strip_tags($email) . "\r\n";
                                $message 	.= "Your New generate password is  : " . strip_tags($newpass) . "\r\n";
                                $message 	.= "Password changed Date : " . strip_tags($Date) . "\r\n";
                                $message 	.= "Message : Please visit our website to login ".$base_url." ";
                                sendEmail::sendEmail($name, $email, $subject, $message);
		        }
			}else{
				$msg = '<div class="alert alert-danger " id="flash-msg">
	    <strong>Error !</strong> Email not Exists !</div>';
				echo  $msg;
			}

		}





	}


	// Profile Complete Notification
	public function profileCompleteNotify($userEmail, $userid){
		$query = "SELECT * FROM $this->table WHERE email = '$userEmail'  && userid = '$userid' && status = '0' ";
		$result 		= $this->db->select($query)->fetch_assoc();

		$name 			= $result['name'];
		$rolename 		= $result['rolename'];

		if (empty($rolename)) {
			 $msg 	= '<div class="alert alert-danger animated fadeInUp bg-danger text-white alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <strong>In-complete Profile !</strong> Hey ( '.$name.' ) Please before Complete your profile, then browse Dashboard. ! <a href="editprofile.php?edituser='.$userid.'"><span class="badge badge-lg text-bg-dark">Go to profile </span></a> </div>';
    		return $msg;
		}
	}



	// User Login Date activity Statics
	public function userActive_OFF($userid){
		$lastactivity	= date("Y-m-d H:i:s");
    	$query = "UPDATE $this->table
    			SET  
    			lastactivity 	= '0'
    			WHERE userid 	= '$userid'
    			 && status = '0' 
    	";
		$update_row = $this->db->update($query);
		return $update_row;
	 	
	}




	// User Login Date activity Statics
	public function userActive_ON($userid){
		$lastactivity	= date("Y-m-d H:i:s");
    	$query = "UPDATE $this->table
    			SET  
    			lastactivity 	= '1'
    			WHERE userid 	= '$userid'
    			 && status = '0' 
    	";
		$update_row = $this->db->update($query);
		return $update_row;
	 	
	}












}