<?php

$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../lib/Database.php');
include_once ($filepath.'/../helpers/Format.php');

/**
 * AppAutho Class
 */
class AppAutho{
	
	private $table = "APP_AUTH";
	private $db;
	private $fm;

	// Construct auto Load
	public function __construct(){
		$this->db = new Database();
		$this->fm = new Format();
	}





	// Select only User ID
	public function selectOnlyAppId(){
		$query = "SELECT * FROM $this->table ";
		$result = $this->db->select($query);
		return $result;
	}




	// Add email switch values 
	public function addEmailValuse($allow_email, $id_autho){
		$id_autho 		= $this->fm->validation($id_autho);
		$allow_email 	= $this->fm->validation($allow_email);
		$allow_email 	= mysqli_real_escape_string($this->db->link ,$allow_email);

    	$query = "UPDATE $this->table
    			SET  
    			allow_email 	= '$allow_email'
    			WHERE id_autho = '$id_autho'
    	";
    	$updated_row = $this->db->update($query);
		if ($updated_row) {
			echo $msg = ' <div class="alert alert-success alert-dismissible" id="flash-msg">
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
<strong>Success! </strong>Allow Registration Changed Save Successfully !</div>';
			
			exit();
		}else{
			echo $msg = ' <div class="alert alert-danger alert-dismissible" id="flash-msg">
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
<strong>Error! </strong>Something went wrong !</div>';
			exit();
		}


	}


}