<?php
global $config;

//SMTP detail
// $config['HOST']	= 'mail.u-turn.in'; 
// $config['USERNAME'] = 'verification@u-turn.in';
// $config['PASSWORD'] = 'nahiptahai1512';
// $config['PORT'] = '25';
// $config['FROMEMAIL']= 'verification@u-turn.in';
// $config['ADMIN']='verification@u-turn.in';




$config['EMAIL_HEADER']='<html xmlns="http://www.w3.org/1999/xhtml"><head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>U-turn</title>
                </head>
				<body style="color:#69625B;background-color:#f9f9f9;border:#333333; margin-left:50px; padding:0; margin:0; padding:0; font-family: Source Sans Pro,Helvetica,Arial,sans-serif;">
                <div style=" margin:auto; text-align:center; background-color:#D0D0D0; padding:0 10px; font-size:25px;">
				<a style="color: #fff;  text-decoration:none; " href="" style="text-decoration:none;" title="u-Turn" target="_blank">
                ICE GROUP OF INSTITUTIONS
                </a>
                </div><br>';
				
$config['EMAIL_FOOTER']=' 
				<div>
				<p>Ragards<br>
				ICE GROUP OF INSTITUTIONS<br>
				<br>
				</p>
			
				</div>
				<div style="margin:auto;width:none; text-align:center; background-color:#D0D0D0; padding:0 10px; font-size:25px;">
				<a style="color: #fff; text-decoration:none; " href="" style="text-decoration:none;" title="U-turn"  target="_blank">ICE GROUP OF INSTITUTIONS
                </a>
                </div>
				</body>
                </html>';
				
				
				
function sendSms($number,$text)
{
global $config;	
 // $user="pyin-paceiit"; //your username
 // $password="98765"; //your password   
 //$senderid="PACESR"; //Your senderid 
 // $senderid="PSUPER"; //Your senderid 
 // $url="http://103.16.101.52/sendsms/bulksms?";   
 // $message = urlencode($text);
 // $ch = curl_init();
 // if (!$ch){die("Couldn't initialize a cURL handle");}
 // $ret = curl_setopt($ch, CURLOPT_URL,$url);
 // curl_setopt($ch, CURLOPT_POST, 1);
 // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
 // curl_setopt($ch, CURLOPT_POSTFIELDS,"username=$user&password=$password&type=0&dlr=1&destination=$number&source=$senderid&message=$message");
 // $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;   
 // $result = curl_exec($ch);
 // curl_close($ch);
// $curlresponse=$result;

$authKey = "236907AW41PBvu1yvW5b977528";
$mobileNumber =$number;
$senderId = "UTurnU";
$message = urlencode($text);
$route = "4";
$postData = array(
    'authkey' => $authKey,
    'mobiles' => $mobileNumber,
    'message' => $message,
    'sender' => $senderId,
    'route' => $route
);
$url="http://api.msg91.com/api/sendhttp.php";
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData
    //,CURLOPT_FOLLOWLOCATION => true
));

//Ignore SSL certificate verification
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

//get response
$output = curl_exec($ch);
	
   
 
}
?>