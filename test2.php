<?php
date_default_timezone_set('Asia/Kolkata');
$today= "15-7-2018";
$expire= "23-07-2018";
$today_dt = new DateTime("d-m-Y", strtotime($today));
$expire_dt = new DateTime("d-m-Y", strtotime($expire));

if ($expire_dt < $today_dt)
	echo "less";
else
	echo "no";
	die;

//$fromdate= '17-07-2018';
//echo $fromdate = date('d-m-Y',strtotime($fromdate));die;
$start_date = new DateTime('23-07-2018');
$since_start = $start_date->diff(new DateTime());
echo $since_start->days.' days total<br>';
// echo $since_start->y.' years<br>';
// echo $since_start->m.' months<br>';
// echo $since_start->d.' days<br>';
// echo $since_start->h.' hours<br>';
// echo $since_start->i.' minutes<br>';
// echo $since_start->s.' seconds<br><br>';
// $minutes = $since_start->days * 24 * 60;
// $minutes += $since_start->h * 60;
// $minutes += $since_start->i;
// echo $minutes.' minutes';
?>


<?PHP
	 // $response_date= "2017-11-20 16:44:14";
	 // $date = new DateTime($response_date);
     // ECHO $responsedate = $date->format('d-m-Y H:i:s');
	 // $date1= new DateTime("09/07/2018");
	 // $date2= new DateTime("19/07/2018");
	 // if(strtotime($date1) < strtotime($date2))
		 // echo "less";
	 // else
		 // echo "greater";
	 
	// $response_date= "2018/06/29";
	// //$response_date= "29/06/2018";
	// $date = new DateTime($response_date);
    // $responsedate = $date->format('F j, Y');
	
	// $response_date1= "2018/06/30";
	// //$response_date= "29/06/2018";
	// $date1 = new DateTime($response_date1);
    // $responsedate1 = $date->format('F j, Y');
	// if($responsedate < $responsedate1)
		// echo "ok";
	// else
		// echo "no";
	//ECHO $today_date= date("d-m-Y", strtotime("+1 day"));
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>jQuery UI Datepicker - Format date</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  
  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
	$( "#datepicker" ).datepicker("option", "dateFormat", "dd/mm/yy");
    $( "#format" ).on( "change", function() {
      $( "#datepicker" ).datepicker( "option", "dateFormat", $( this ).val() );
    });
  } );
  </script>
</head>
<body>
 
<p>Date: <input type="text" id="datepicker" size="30" readonly="readonly"></p>
 
<p>Format options:<br>
  <select id="format">
    <option value="mm/dd/yy">Default - mm/dd/yy</option>
    <option value="yy-mm-dd">ISO 8601 - yy-mm-dd</option>
    <option value="d M, y">Short - d M, y</option>
    <option value="d MM, y">Medium - d MM, y</option>
    <option value="DD, d MM, yy">Full - DD, d MM, yy</option>
    <option value="&apos;day&apos; d &apos;of&apos; MM &apos;in the year&apos; yy">With text - 'day' d 'of' MM 'in the year' yy</option>
  </select>
</p>
 <script>
 // var date1 = new Date();alert(date1);
// var date2 = new Date("7/1/2018");
// var timeDiff = (date2.getTime() - date1.getTime());
// var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
// if(diffDays <= 2)
	// alert("ok");
// else
	// alert("no");
 // var date = new Date()

// // Add a day
// date.setDate(date.getDate() + 1);
// //alert(date);
	// var from_date= '13/07/2018'; //$(".from_date").val();
   // var from_date_array= from_date.split("/");
   // var y_from= from_date_array[2];
   // var m_from= from_date_array[1];
   // var d_from= from_date_array[0];
   // var d = new Date();
   // if (new Date(m_from +"/" + d_from +"/" + y_from) < d) {
     // //alert('false');
   // } 
 </script>
 
</body>
</html>