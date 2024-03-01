<?php
	include('includes/config.php');
	session_start();
if(!empty($_FILES["upload_profile_pic"]["name"])){
	$allowed_ext = array("jpg", "png", "jpeg");  
	$extension = end(explode(".", $_FILES["upload_profile_pic"]["name"]));
	if(in_array($extension, $allowed_ext)){
		$file_name= "Raw_Data.".$extension;
		$eid= $_SESSION['eid'];
		$img_name= time().".".$extension;
		$path= "assets/images/".$img_name;
		//$img_name= $_FILES["upload_profile_pic"]["name"];
		move_uploaded_file($_FILES['upload_profile_pic']['tmp_name'],$path);
		$sql= "update tblemployees set ImageName= '$img_name' where id= :eid";
		$query = $dbh -> prepare($sql);
		$query->bindParam(':eid',$eid,PDO::PARAM_STR);
		$query->execute();
		echo $path;
		//rename($_SERVER['DOCUMENT_ROOT']."/netsutra_leave/assets/images/".$_FILES["upload_profile_pic"]["name"],$eid.".".$extension);
	}else{
		echo "Invalid";
	}
}
?>