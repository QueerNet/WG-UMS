<?php

$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../lib/Database.php');
include_once ($filepath.'/../helpers/Format.php');



require ($filepath.'/../classes/SMTP.php');
require ($filepath.'/../classes/PHPMailer.php');
require ($filepath.'/../classes/Exception.php');

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;





/**
 * ClientMsg Class
 */
class ClientMsg{
	
	// Construct auto Load
	public function __construct(){
		$this->db = new Database();
		$this->fm = new Format();
		$this->usr = new Users();
	}




	// clientProposalMethod 
	public function clientMessageMethod($data){
		$name_ 				= $this->fm->validation($data['name']);
		$email_ 				= $this->fm->validation($data['email']);
		$budget_ 			= $this->fm->validation($data['budget']);
		$frameworks_ 	= $this->fm->validation($data['frameworks']);



		$name 				= mysqli_real_escape_string($this->db->link, $name_);
		$email 				= mysqli_real_escape_string($this->db->link, $email_);
		$budget 			= mysqli_real_escape_string($this->db->link, $budget_);
		$frameworks 	= mysqli_real_escape_string($this->db->link, $frameworks_);
		
		

		$pregExp = "/^[a-z0-9_-]+(\.[a-z0-9_-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/";
		if ($name == "" || $email == "" || $budget == "" || $frameworks == "") {
	     
	        $msg =   '<div class="alert alert-danger " id="flash-msg">
	    <strong>Error !</strong> Input fields must not be Empty!</div>';
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
				//Client Proposal Message
				
				date_default_timezone_set("Asia/Dhaka");
				$Date 		= new DateTime();
				$Date 		= date_format($Date, 'Y-m-d H:i:s');
				$form 	 = $email;
				$to 	 = "nababurdev@gmail.com";
				$subject = 'New Job proposal from Benzi Admin Dashboard !';
				$message  = "Client name : " . strip_tags($name) . "\r\n";
				$message .= "Client E-mail : " . strip_tags($email) . "\r\n";
				$message .= "Client Budget : " . strip_tags($budget) . "\r\n"; 
				$message .= "Client framework choice : " . strip_tags($frameworks) . "\r\n";
				$message .= "Proposal Email Date : " . strip_tags($Date) . "\r\n";
				$message .= "This Email come from your Benzi Admin Dashboard Client proposal Pannel.";
		        $sendmail = $this->usr::sendEmail($name, $email, $subject, $message);


		        if ($sendmail) {
				         $msg = ' <div class="alert alert-success " id="flash-msg">
    <strong>Success! </strong> Your Proposal has been send Successfully, We will reply as soon as possible. Thanks !</div>';
		        echo $msg;
		        }else{
					$msg =   '<div class="alert alert-danger " id="flash-msg">
    <strong>Error !</strong> Something went wrong!</div>';
		        echo $msg;
		        }

					
		    }
	}


	





}