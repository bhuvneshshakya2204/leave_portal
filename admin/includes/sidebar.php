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
$results_admin=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
foreach($results_admin as $val_admin)?>
	 <aside id="slide-out" class="side-nav white fixed" style="width: 240px;">
                <div class="side-nav-wrapper">
                    <!--<div class="sidebar-profile">
                        <div class="sidebar-profile-image">
                            <img src="../assets/images/profile-image.png" class="circle" alt="">
                        </div>
                        <div class="sidebar-profile-info">
								<?php
								if(strlen($_SESSION['hrlogin'])!=0){ ?>
									<p>Manager- HR</p>
								<?php }else{ ?>
									 <p>Manager</p>
								<?php } ?>
                         
                        </div>
                    </div>-->
					<div class="sidebar-profile">
					<form id="profile_pic_form" name="profile_pic_form">
                        <div class="sidebar-profile-image" id="profile_img_container">
                            <img src="../assets/images/<?php echo $val_admin->ImageName== NULL?'profile-image.png':$val_admin->ImageName; ?>" class="circle" alt="" title="Update profile picture" id="change_pic">&nbsp;&nbsp;&nbsp;<img src="../assets/images/camera.jpg" class="camera_pic" style="width: 20px; height: 20px; position: relative; top: -10px; display: none;" />
							<input type="file" name="upload_profile_pic" id="upload_profile_pic" style="display: none;" onchange="updateProfilePic()" />
						</div>
						<input type="submit" value="" id="submit_pic" style="display: none;" />
						</form>
                        <div class="sidebar-profile-info">
                    
                                <p><?php echo htmlentities($val_admin->FirstName." ".$val_admin->LastName);?></p>
                                <span><?php echo htmlentities($val_admin->EmpId)?></span>
                        </div>
                    </div>
            
                <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
				<?php
					if(strlen($_SESSION['hrlogin'])!=0){ ?>
                    <li class="no-padding"><a class="waves-effect waves-grey" href="dashboard.php"><i class="material-icons">settings_input_svideo</i>Dashboard</a></li>
                    <!--<li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">code</i>Leave Type<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="addleavetype.php">Add Leave Type</a></li>
                                <li><a href="manageleavetype.php">Manage Leave Type</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">account_box</i>Employees<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="addemployee.php">Add Employee</a></li>
                                <li><a href="manageemployee.php">Manage Employee</a></li>
       
                            </ul>
                        </div>
                    </li>
					<li class="no-padding">
                        <a class="waves-effect waves-grey" href="add_leave.php"><i class="material-icons">playlist_add</i>Add Leave/LA/LE</a>
                    </li>-->
					<?php } else if(strlen($_SESSION['mlogin'])!=0){ ?>
                    <li class="no-padding"><a class="waves-effect waves-grey" href="emp-dashboard.php"><i class="material-icons">settings_input_svideo</i>Dashboard</a></li>
                    <?php } ?>
					<li class="no-padding"><a class="waves-effect waves-grey" href="myprofile.php"><i class="material-icons">account_box</i>My Profile</a></li>
					<li class="no-padding">
                        <a class="waves-effect waves-grey" href="../policies/Leave Policy ver 1.6.pdf" target="_blank"><i class="material-icons">dock</i>Leave Policies</a>
                    </li>
					<li class="no-padding">
                        <?php $calYear = date('Y'); ?>
                        <!-- <a class="waves-effect waves-grey" onclick="openCalendar('<?php //echo $calYear ?>')"><i class="material-icons">today</i>Holiday Calendar</a> -->
                        <a class="waves-effect waves-grey" href="https://www.netsutra.com/calendar" target="_blank"><i class="material-icons">today</i>Holiday Calendar</a>
                    </li>
					<?php
					if(isset($_SESSION['mlogin'])){ ?>
					<li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">desktop_windows</i>Leave Management<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="leaves.php">All Leaves </a></li>
                                <li><a href="pending-leavehistory.php">Pending Leaves </a></li>
                                <!--<li><a href="approvedleave-history.php">Approved Leaves</a></li>
                                  <li><a href="notapproved-leaves.php">Not Approved Leaves</a></li>-->
       
                            </ul>
                        </div>
                    </li>
					<?php } ?>
					<li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">apps</i>My Leaves<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="apply-leave.php">Apply Leave</a></li>
                                <li><a href="leavehistory.php">Leave History</a></li>
                            </ul>
                        </div>
                    </li>
					 <?php
                        if(isset($_SESSION['mlogin'])){ ?>
                        <li class="no-padding">
                            <a class="waves-effect waves-grey" href="manageemployee.php"><i class="material-icons">contacts</i>Employees Directory</a>
                        </li>
                        <?php } ?>
						<li class="no-padding"><a class="waves-effect waves-grey" href="changepassword.php"><i class="material-icons">settings_input_svideo</i>Change Password</a></li>
						<li class="no-padding">
                        <a class="waves-effect waves-grey" href="faq-page.php"><i class="material-icons">dock</i>FAQs</a>
                    </li>
						<li class="no-padding">
							<a class="waves-effect waves-grey" href="feedback.php"><i class="material-icons">comment</i>Feedback</a>
						</li>
						
					 <li class="no-padding">
                           <a class="waves-effect waves-grey" href="logout.php"><i class="material-icons">exit_to_app</i>Sign Out</a>
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
			 url: "../update_profile_pic.php",  
			 method: "POST",  
			 data: new FormData(this),  
			 contentType: false,          // The content type used when sending data to the server.  
			 cache: false,                // To unable request pages to be cached  
			 processData: false,          // To send DOMDocument or non processed data file it is set to false  
			 success: function(data){
				 if(data== "Invalid"){
					 alert("You are uploading Invalid file");
				 }else{
					 $("#change_pic").attr("src", "../"+data);
				 }
			 }
		});
	});
		$("#profile_img_container").click(function(){
			document.getElementById("upload_profile_pic").click();
		});
	});
</script>
