<style>
#profile_img_container:hover img.camera_pic{
	display: inline!important;
}
</style>
<?php
$eid=$_SESSION['eid'];
$sql = "SELECT FirstName,LastName,EmpId,ImageName from  tblemployees where id=:eid";
$query = $dbh -> prepare($sql);
$query->bindParam(':eid',$eid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
foreach($results as $result)?>
	<aside id="slide-out" class="side-nav white fixed" style="width: 240px;">
                <div class="side-nav-wrapper">
                    <div class="sidebar-profile">
					<form id="profile_pic_form" name="profile_pic_form">
                        <div class="sidebar-profile-image" id="profile_img_container">
                            <img src="<?php echo $path_decided; ?>assets/images/<?php echo $result->ImageName== NULL?'profile-image.png':$result->ImageName; ?>" class="circle" alt="" title="Update profile picture" id="change_pic">&nbsp;&nbsp;&nbsp;<img src="<?php echo $path_decided; ?>assets/images/camera.jpg" class="camera_pic" style="width: 20px; height: 20px; position: relative; top: -10px; display: none;" />
							<input type="file" name="upload_profile_pic" id="upload_profile_pic" style="display: none;" onchange="updateProfilePic()" />
						</div>
						<input type="submit" value="" id="submit_pic" style="display: none;" />
						</form>
                        <div class="sidebar-profile-info">
                    
                                <p><?php echo htmlentities($result->FirstName." ".$result->LastName);?></p>
                                <span><?php echo htmlentities($result->EmpId)?></span>
                        </div>
                    </div>

                <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
                    
                    <li class="no-padding"><a class="waves-effect waves-grey" href="<?php echo $path_decided; ?>emp-dashboard.php"><i class="material-icons">settings_input_svideo</i>Dashboard</a></li>
                    <li class="no-padding"><a class="waves-effect waves-grey" href="<?php echo $path_decided; ?>myprofile.php"><i class="material-icons">account_box</i>My Profile</a></li>
					<li class="no-padding">
                        <a class="waves-effect waves-grey" href="<?php echo $path_decided; ?>policies/Leave Policy ver 1.6.pdf" target="_blank"><i class="material-icons">dock</i>Leave Policies</a>
                    </li>
					<li class="no-padding">
						<?php $calYear = date('Y'); ?>
                        <!-- <a class="waves-effect waves-grey" onclick="openCalendar('<?php //echo $calYear ?>')"><i class="material-icons">today</i>Holiday Calendar</a> -->
                        <a class="waves-effect waves-grey"  href="https://www.netsutra.com/calendar" target="_blank"><i class="material-icons">today</i>Holiday Calendar</a>
                    </li>
					 <li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">apps</i>My Leaves <i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="<?php echo $path_decided; ?>apply-leave.php">Apply Leave</a></li>
                                <li><a href="<?php echo $path_decided; ?>leavehistory.php">Leave History</a></li>
                                <!-- <li><a href="<?php echo $path_decided; ?>admin/leave_report.php">Leave Report</a></li> -->
                            </ul>
                        </div>
                    </li>
					<li class="no-padding">
                        <a class="waves-effect waves-grey" href="<?php echo $path_decided; ?>admin/manageemployee.php"><i class="material-icons">contacts</i>Employees Directory</a>
                    </li>
  <li class="no-padding"><a class="waves-effect waves-grey" href="<?php echo $path_decided; ?>emp-changepassword.php"><i class="material-icons">settings_input_svideo</i>Change Password</a></li>
                   
					
					<li class="no-padding">
                        <a class="waves-effect waves-grey" href="<?php echo $path_decided; ?>admin/faq-page.php"><i class="material-icons">dock</i>FAQs</a>
                    </li>
					<li class="no-padding">
                        <a class="waves-effect waves-grey" href="<?php echo $path_decided; ?>feedback.php"><i class="material-icons">comment</i>Feedback</a>
                    </li>
					
                  <li class="no-padding">
                                <a class="waves-effect waves-grey" href="<?php echo $path_decided; ?>logout.php"><i class="material-icons">exit_to_app</i>Sign Out</a>
                            </li>


                </ul>
                <div class="footer">
                    <p class="copyright"><a href="http://www.netsutra.com">Netsutra </a>©</p>
                </div>
                </div>
            </aside>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
<script>
function openCalendar(yr){
	if(yr > 2021) {
		window.open('https://www.netsutra.com/calendar_'+yr+'.php', '_blank', 'location=yes');
	}else{
		window.open('https://www.netsutra.com/calendar_'+yr+'_emp.php', '_blank', 'location=yes');
	}
}
function updateProfilePic(){
	document.getElementById("submit_pic").click();
}
	$(document).ready(function(){
		$('#profile_pic_form').on("submit", function(e){  
		e.preventDefault();
		//alert($("#upload_profile_pic").val());
		if($("#upload_profile_pic").val()== ""){
			return false;
		}
		$.ajax({  
			 url: "<?php echo $path_decided; ?>update_profile_pic.php",  
			 method: "POST",  
			 data: new FormData(this),  
			 contentType: false,          // The content type used when sending data to the server.  
			 cache: false,                // To unable request pages to be cached  
			 processData: false,          // To send DOMDocument or non processed data file it is set to false  
			 success: function(data){
				 if(data== "Invalid"){
					 alert("You are uploading Invalid file");
				 }else{
					 $("#change_pic").attr("src", '<?php echo $path_decided; ?>'+data);
				 }
			 }
		});
	});
		$("#profile_img_container").click(function(){
			document.getElementById("upload_profile_pic").click();
		});
	});
</script>
