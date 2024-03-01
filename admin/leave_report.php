<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../includes/custom_functions.php');
$empId = 0;
if(strlen($_SESSION['mlogin'])!=0){   
    $empId = $_SESSION['eid'];
}else if(strlen($_SESSION['hrlogin'])!=0){
    $empId = $_SESSION['eid'];
}else if(strlen($_SESSION['emplogin'])!=0){
    $empId = $_SESSION['eid'];
}else{
    header('location: ../index.php');
}

if(isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
    $empId=$_REQUEST['id'];
}
$sql = "SELECT * from  tblemployees where id='$empId'";    
//print_r($_SESSION);

$query = $dbh -> prepare($sql);
$query->execute();
$empDetail= $query->fetch(PDO::FETCH_OBJ);
if(!$empDetail){
    echo "No Record found to populate."; die;
}

$totILForYear = $empDetail->informed_leave;
$totCLForYear = $empDetail->casual_leave;
$remainingLeave = $totILForYear + $totCLForYear;
$leavePerMonth = $empDetail->leave_per_month;
$year = date("Y");

//-----------------------------------------------------------------------------
function getLeaveType($leave){
    $fromDate = date('d-m-Y',strtotime($leave->FromDate));
    $toDate = date('d-m-Y',strtotime($leave->ToDate));
    $postingDate = $leave->PostingDate;
    $reqGap = $fromDate == $toDate ? 2 : 7;
    $leaveDuration = dateDuration($postingDate, $fromDate);
    $totalOff = getOffDaysForPostingDate($leave->FromDate, $leave->PostingDate);
    $leaveDuration = $leaveDuration - $totalOff;
    //echo "<br/>GAP=".$leaveDuration." FOR ".$leave->FromDate." (".($leaveDuration >= $reqGap ? 'IL' :'CL').") where reqGAP is ".$reqGap;
    return $leaveDuration >= $reqGap ? 'IL' :'CL';
}


/*
@Param $fromDate = 'dd-mm-yyyy'
@Param $toDate = 'dd-mm-yyyy'
*/
function dateDuration($fromDate, $toDate){
    $datetime1 = date_create($fromDate); 
    $datetime2 = date_create($toDate); 
    $interval = date_diff($datetime1, $datetime2); 
    return intval($interval->format('%R%a')); 
}


/*
@Param $fromDate = 'dd-mm-yyyy'
@Param $toDate = 'yyyy-mm-dd'
*/
function getOffDaysForPostingDate($fromDate, $postDate){
    $totalOff = 0;
    $fromDate = date('Y-m-d',strtotime($fromDate));
    $gap =  dateDuration($postDate, date('d-m-Y',strtotime($fromDate)));
    for($i=1;$i<=$gap;$i++){
        $nextDate = date('d-m-Y', strtotime($postDate. ' + '.$i.' days'));
        $isOff = check_official_off($nextDate);
        $totalOff = $isOff ? $totalOff + 1 : $totalOff ;
    }
    return $totalOff;
}

function getPreviousLeave($fromDate,$empId){
    global $dbh;
    $from_date = date('Y-m-d',strtotime($fromDate));
    $sql = "SELECT * FROM tblleaves where str_to_date(FromDate, '%d-%m-%Y') < '".$from_date."' AND empid=$empId AND LeaveType = 'Leave' and Status= 1 order by str_to_date(FromDate, '%d-%m-%Y') desc LIMIT 1";
    $query = $dbh -> prepare($sql); 
    $query->execute();
    $leaveDetail=$query->fetch(PDO::FETCH_OBJ);
    if(sizeof($leaveDetail)>0){
        return $leaveDetail;
    }else{
        return null;
    }
}

function getNextLeave($fromDate,$empId){
    global $dbh;
    $from_date = date('Y-m-d',strtotime($fromDate));
    $sql = "SELECT * FROM tblleaves where str_to_date(FromDate, '%d-%m-%Y') > '".$from_date."' AND empid=$empId AND LeaveType = 'Leave' and Status= 1 order by str_to_date(FromDate, '%d-%m-%Y') asc LIMIT 1";
    $query = $dbh -> prepare($sql); 
    $query->execute();
    $leaveDetail=$query->fetch(PDO::FETCH_OBJ);
    if(sizeof($leaveDetail)>0){
        return $leaveDetail;
    }else{
        return null;
    }
}

function getSandwitchLeave($toDateOfPreLeave, $fDateOfCurrentLeave){
    $gap =  dateDuration($toDateOfPreLeave, $fDateOfCurrentLeave) - 1;
    for($i=1;$i<=$gap;$i++){
        $nextDate = date('d-m-Y', strtotime($toDateOfPreLeave. ' + '.$i.' days'));
        $isOff = check_official_off($nextDate);
        if(!$isOff){
            return 0;
        }
    }
    return $gap;
}

function prepareNewLeave($preLeave,$leave,$nextLeave){
    $preGap = ($preLeave != null) ? dateDuration($preLeave->ToDate,$leave->FromDate) : 0;
    $nextGap = ($nextLeave != null) ? dateDuration($leave->ToDate, $nextLeave->FromDate) : 0;
    $newLeave = null;
    if($preGap == 1 || $nextGap == 1 ){
        $newLeave = array("FromDate"=>"","ToDate"=>"","PostingDate"=>$leave->PostingDate);
        $newLeave["FromDate"] = $preGap == 1 ? $preLeave->FromDate : $leave->FromDate;
        $newLeave["ToDate"] = $nextGap == 1 ? $nextLeave->ToDate : $leave->ToDate;
    }
    return $newLeave;
}
//die;
//---------------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Leave Report</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet"> 
        <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="../assets/css/jquery-ui.css">
		<style>
		.red_border{
			border: 2px solid red;
		}
        .colShortLeave{
            background-color: #d6f1ef !important;
        }
        .colLeave{
            background-color: #d3f9ce !important;
        }
		</style>
    </head>
    <body>
  <?php include('includes/header.php');?>
            
    <?php include('includes/sidebar.php');?>
            
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <h3>Leave Report</h3>
                        <?php if(strlen($_SESSION['mlogin'])!=0 || strlen($_SESSION['hrlogin'])!=0 || $eid == 50){?>
                            <div class="input-field col m2 s12">
                                <span class="label_up">Employee Name</span>
                                <input id="nameFilter" name="nameFilter" value="<?php echo $empDetail->FirstName.' '.$empDetail->LastName ?>">
                            </div>
                        <?php }else{?>
                        <small>( <?php echo $empDetail->FirstName.' '.$empDetail->LastName ?> )</small>
                        <?php }?>
                        
                        <?php
                        $finalData = array(
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0),
                            array("IL"=>0,"CL"=>0,"SW"=>0)
                        );

                        for($month=1;$month<=12;$month++){
                            $sql = "select id,FromDate,ToDate,PostingDate,DAY(LAST_DAY(str_to_date(FromDate, '%d-%m-%Y'))) as lastDayOfMonth from tblleaves where (MONTH(str_to_date(FromDate, '%d-%m-%Y')) = '".$month."' OR MONTH(str_to_date(ToDate, '%d-%m-%Y')) = '".$month."') AND YEAR(str_to_date(FromDate, '%d-%m-%Y')) = '".$year."' AND LeaveType = 'Leave' and Status= 1  and empid= ".$empId." order by str_to_date(FromDate, '%d-%m-%Y') asc";
                            $query = $dbh -> prepare($sql);
                            $query->execute();
                            $results=$query->fetchAll(PDO::FETCH_OBJ);
                            $IL = 0; $CL = 0; $sandwitchLeave = 0;
                            foreach ($results as $key => $row) {

                                $preLeave = $key > 0 ? $results[$key-1] : null;
                                if($preLeave == null){
                                    $preLeave = getPreviousLeave($row->FromDate, $empId);
                                }

                                $leaveType = getLeaveType($row);
                                
                                $sandwitchLeave = getSandwitchLeave($preLeave->ToDate, $row->FromDate);

                                $monthOfFromDate = date_format(date_create($row->FromDate),"m");
                                $monthOfToDate = date_format(date_create($row->ToDate),"m");
                                if($monthOfFromDate != $monthOfToDate){
                                    if($monthOfFromDate == $month){
                                        $row->ToDate = $row->lastDayOfMonth."-".str_pad($month,2,"0",STR_PAD_LEFT)."-".$year;
                                    }else{
                                        $row->FromDate = "01-".str_pad($month,2,"0",STR_PAD_LEFT)."-".$year;
                                    }
                                }
                                
                                $leaveCount = dateDuration($row->FromDate, $row->ToDate) + 1;
                               
                                // echo "<br/>Pre Leave = ". ($preLeave != null ? $preLeave->ToDate : "");
                                // echo "  Leave = ". ($row != null ? $row->FromDate : "");

                                
                                if($leavePerMonth == 1 || $leaveType == 'IL'){
                                    $nextLeave = isset($results[$key+1]) ? $results[$key+1] : getNextLeave($row->FromDate, $empId);
                                    if($preLeave != null || $nextLeave != null){
                                        $newLeave = prepareNewLeave($preLeave,$row,$nextLeave);
                                        $newLeaveType = $newLeave != null ? getLeaveType($newLeave) : 'IL';
                                        if($newLeaveType == 'IL'){
                                            $IL = $IL + intval($leaveCount);
                                            $CL = $CL + $sandwitchLeave;
                                        }else{
                                            $CL = $CL + intval($leaveCount) + $sandwitchLeave;
                                        }
                                    }else{
                                        $IL = $IL + intval($leaveCount);
                                    }
                                    
                                }else{
                                    $CL = $CL + intval($leaveCount) + $sandwitchLeave;
                                }
                            }
                            $finalData[$month] = array("IL"=>$IL,"CL"=>$CL,"SW"=>$sandwitchLeave);
                        }
                        // echo "<pre>";
                        // print_r($finalData);
                        // echo "</pre>";
                        ?>
                        <div style="text-align: center;color:#0b0e0b;margin-bottom:10px;">
                            <p>Year <?php echo $year; ?></p>
                            <p>
                                Carried From Previous Year 
                                ( IL: <?php echo $totILForYear ?>, CL: <?php echo $totCLForYear ?>) = <?php echo $remainingLeave ?>
                            </p>
                            <?php 
                                $totILForYear = $totILForYear + 1;
                                $totCLForYear = ($leavePerMonth == 2) ? $totCLForYear + 1 : 0;
                                $remainingLeave = $remainingLeave + $leavePerMonth;
                            ?>
                            <p>
                                Total Leave Balance in January : 
                                ( IL: <?php echo $totILForYear ?>, CL: <?php echo $totCLForYear ?>)  = <?php echo $remainingLeave ?>
                            </p>
                        </div>

                        <div class="table-responsive">
                            <table class="display responsive-table  dataTable ">
                                <thead>
                                  <tr>
                                    <th>Month</th>
                                    <th colspan="2" class="colShortLeave">LA/LE</th>
                                    <th colspan="6" class="colLeave">Leave</th>
                                  </tr>
                                </thead>
                                <thead>
                                  <tr>
                                    <th></th>
                                    <th class="colShortLeave">Used</th>
                                    <th class="colShortLeave">Remaining</th>
                                    <th class="colLeave">For Current Month(IL/CL)</th>
                                    <th class="colLeave">Leaves(IL/CL)</th>
                                    <th class="colLeave">Adjusted(IL/CL)</th>
                                    <th class="colLeave">Remaining(IL/CL) <br/><small>(Till End of The Month)</small></th>
                                    <th class="colLeave">For Next Month(IL/CL)</th>
                                  </tr>
                                </thead>
                                <tbody>
                                <?php 
                                for($month=1;$month<=12;$month++){
                                    $totalShortLeave = total_shortleave_taken_current_month($dbh, $empId, $year, $month);

                                    $ILTaken = $finalData[$month]["IL"];
                                    $CLTaken = $finalData[$month]["CL"];
                                    $SWLeave = $finalData[$month]["SW"];
                                    $ILAdjusted = $totILForYear >= $ILTaken ? $ILTaken : $totILForYear;
                                    $ILNotAdjusted = $ILTaken > $ILAdjusted ? $ILTaken - $ILAdjusted : 0;
                                    $CLAdjusted = $totCLForYear >= ($CLTaken+$ILNotAdjusted) ? ($CLTaken+$ILNotAdjusted) : $totCLForYear; 
                                    $totalAdjusted = $ILAdjusted + $CLAdjusted;

                                    $cMonthLeave = $remainingLeave ;
                                    $cMonthILForYear = $totILForYear;
                                    $cMonthCLForYear = $totCLForYear;

                                    $totILForYear = $totILForYear >= $ILTaken ? $totILForYear - $ILTaken : 0;
                                    $totCLForYear = $totCLForYear >= ($CLTaken+$ILNotAdjusted) ? $totCLForYear - ($CLTaken+$ILNotAdjusted) : 0; 
                                    $totalLeaveTaken = $ILTaken + $CLTaken;
                                    
                                    

                                    $remainingLeave = $remainingLeave - $totalAdjusted;

                                ?>
                                  <tr>
                                    <td><?php echo date("F", mktime(0, 0, 0, $month, 10)) ; ?>, <?php echo $year?></td>
                                    <td class="colShortLeave"><?php echo $totalShortLeave; ?></td>
                                    <td class="colShortLeave"><?php echo (4 - $totalShortLeave); ?></td>
                                    <td class="colLeave">
                                        <?php echo $cMonthLeave  ?> = <?php echo $cMonthILForYear  ?> IL + <?php echo $cMonthCLForYear  ?> CL
                                        <br/><small>( For <?php echo date("F", mktime(0, 0, 0, $month, 10)) ; ?>)</small>
                                    </td>
                                    <td class="colLeave">
                                        <?php echo $totalLeaveTaken?> = <?php echo $ILTaken?> IL + <?php echo $CLTaken?> CL
                                        <small style="color:red;"><?php if($SWLeave > 0) {echo "<br/>Sandwitch: ".$SWLeave;} ?></small>
                                    </td>
                                    <td class="colLeave"><?php echo $totalAdjusted?> = <?php echo $ILAdjusted?> IL + <?php echo $CLAdjusted?> CL</td>
                                    <td class="colLeave">
                                        <?php echo $remainingLeave?> = <?php echo $totILForYear?> IL + <?php echo $totCLForYear?> CL<br/>
                                    </td>
                                    <td class="colLeave">
                                        <?php
                                            $totILForYear = $totILForYear + 1;
                                            $totCLForYear = ($leavePerMonth == 2) ? $totCLForYear + 1 : 0;
                                            $remainingLeave = $remainingLeave + $leavePerMonth;
                                        ?>
                                        <?php echo $remainingLeave  ?> = <?php echo $totILForYear  ?> IL + <?php echo $totCLForYear  ?> CL
                                        <br/><small>( For <?php echo date("F", mktime(0, 0, 0, ($month<12 ? $month + 1 : 1), 10)) ; ?>)</small>
                                    </td>
                                  </tr>
                                <? }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="left-sidebar-hover"></div>
        
    <!-- Javascripts -->
    <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
    <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
    <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
    <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
    <script src="../assets/js/alpha.min.js"></script>
    <script src="../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../assets/plugins/jquery/jquery-ui.js"></script>
	<script>
	$('#nameFilter').autocomplete({
        type: "POST",
        minLength: 3,
        source : function (request, response) 
        {                         
            var source_url = "leaves-data.php?action=name";

            $.ajax({
                url: source_url,
                dataType: "json",
                data: request,
                success: function (data) { response(data); },
                error : function (a,b,c) { HandleLookUpError(a); }
                });
        },                
        select: function (event, ui) { $('#nameFilter').val(ui.item.value);filterData(ui.item.id); }               
    });

    function filterData(id){
        document.location.href = "<?php echo $_SERVER['REQUEST_URL'] ?>?id="+id;
    }
	</script>
        
    </body>
</html>