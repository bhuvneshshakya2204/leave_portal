<?php
$officialOff = array("01-01-2019", "26-01-2019", "04-03-2019", "21-03-2019", "05-06-2019", "15-08-2019", "24-08-2019", "02-10-2019", "08-10-2019", "28-10-2019", "25-12-2019"); // office leave according to calendar

function total_leave_taken_current_month($dbh, $emp_id, $this_year_print, $this_month)
{

    $sql ="SELECT ";
    $sql .= "SUM(IF(MONTH(str_to_date(FromDate, '%d-%m-%Y')) != MONTH(str_to_date(ToDate, '%d-%m-%Y')), ";
    $sql .="If(MONTH(str_to_date(FromDate, '%d-%m-%Y')) = '".$this_month."', DAY(LAST_DAY(str_to_date(FromDate, '%d-%m-%Y'))) - DAY(str_to_date(FromDate, '%d-%m-%Y')) + 1, DAY( ";
    $sql .="str_to_date(ToDate, '%d-%m-%Y'))), DATEDIFF(str_to_date(ToDate, '%d-%m-%Y'), str_to_date(FromDate, '%d-%m-%Y')) + 1)) AS LeaveCount FROM `tblleaves` ";
    $sql .="WHERE ((YEAR(str_to_date(FromDate, '%d-%m-%Y')) = '".$this_year_print."' and MONTH(str_to_date(FromDate, '%d-%m-%Y')) = '".$this_month."') OR (YEAR(str_to_date(ToDate, '%d-%m-%Y')) = '";
    $sql .=$this_year_print."'  and MONTH(str_to_date(ToDate, '%d-%m-%Y')) = '".$this_month."')) and LeaveType = 'Leave' and Status= 1  and empid= '".$emp_id."'" ;
    
    $query = $dbh -> prepare($sql);
    $query->execute();
    $rs_taken= $query->fetchAll(PDO::FETCH_OBJ);        
    $total_leave= 0;
    foreach($rs_taken as $leave_taken){}
    $total_leave+= $leave_taken->LeaveCount;
    return $total_leave;
}

function total_shortleave_taken_current_month($dbh, $emp_id, $this_year_print, $this_month)
{
    $sql= "SELECT SUM(DATEDIFF(str_to_date(ToDate, '%d-%m-%Y'), str_to_date(FromDate, '%d-%m-%Y')) + 1) AS LeaveCount FROM `tblleaves` WHERE MONTH(str_to_date(ToDate, '%d-%m-%Y')) = '".$this_month."' and (LeaveType = 'Late Arrival (LA)' or LeaveType = 'Leaving Early (LE)') and Status= 1 and YEAR(str_to_date(ToDate, '%d-%m-%Y')) = '".$this_year_print."' and empid= '".$emp_id."'";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $rs_taken= $query->fetchAll(PDO::FETCH_OBJ);
    $total_short_leave= 0;
    foreach($rs_taken as $leave_taken){}
    $total_short_leave+= $leave_taken->LeaveCount;
    return $total_short_leave;
}

function check_official_off($fromDate)
{   $off = false;
    $fullNameDay= date('l', strtotime($fromDate)); // get full name
    $monthYear= date("F Y", strtotime($fromDate));  
    $secondSat= date('d-m-Y', strtotime($monthYear.' second saturday')); // get second saturday from date month   
    global $officialOff; //= array("03-09-2018", "02-10-2018", "19-10-2018", "07-11-2018", "25-12-2018"); // office leave according to calendar
    
    if($fullNameDay=='Sunday' || $fromDate==$secondSat || in_array($fromDate, $officialOff))
    {
        $off = true;
    }
 return $off;
}

?>