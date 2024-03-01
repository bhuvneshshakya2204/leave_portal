<?php
function send_email($to, $from, $bcc="", $cc="", $subject, $message)
	{
		if($to != "" && $from != ""){
			$headers = 'MIME-Version: 1.0'."\r\n";
			$headers.= 'Content-type: text/html; charset=UTF-8' . "\r\n";//charset=iso-8859-1
			$headers.= "From: $from \r\n" .  
			"Reply-To: $from \r\n" .  
			"X-Mailer: PHP/" . phpversion() . "\r\n";
				$headers.= "bcc:  $bcc" . "\r\n";
				$headers.= "cc:  $cc" . "\r\n";
			if(mail($to,$subject,$message,$headers))
			{
				return(1);
			}else{
				return(0);
			}
		}
	}
	?>