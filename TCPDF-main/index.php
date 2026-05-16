<?php 
    // error_reporting(0);
    include_once('tcpdf.php');

    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetAutoPageBreak(TRUE, 20);  
    $pdf->AddPage(); 
    $content = ''; 

	$content = '<html>
                    <head>
                        <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
                        <link href="/assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
                        <link href="/assets/css/style.css" rel="stylesheet">
                        <link href="/assets/css/LineIcons.css" rel="stylesheet">
                    </head>
                    <body>
                        <div class="row no-gutters justify-content-center">
                            <form action="" method="post">                                                   
                                <table> 
                                    <tbody>
                                        <tr>
                                            <td>
                                                <img src="assets/images/logo/weldarc-logo.png" style="width:210px;" alt="">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">
                                                <h2 class="text-primary"><b>Mansoori Weldarc India Pvt. Ltd.</b></h2>						
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">
                                                <h6>23/7, Mathura Road, Near JCB India, Ballabhgard, Faridabad-121004</h6>
                                                <h6><b>Mobile:</b> +91-9811100410, 9313103366, <b>Email:</b> afzal@weldarcindia.com</h6>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">
                                                <h4><span class="btn-warning">Quotation</span></h4>
                                            </td>
                                        </tr>	
                                        <tr>
                                            <td><b>Ref: </b></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:right"><b>Date: </b></td>
                                        </tr>
                                        <tr>
                                            <td><b>M/S:</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Kind Attention:</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Contact:</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Email Id:</b></td>
                                        </tr>	
                                    </tbody>
                                </table>
                                <table>
                                    <thead>
                                        <tr class="thead-default">
                                            <th colspan="2">Machine Info</th>												
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Machine Name:</td>
                                            <td style=""></td>
                                        </tr>
                                        <tr>
                                            <td>Machine Type:</td>
                                            <td style=""></td>
                                        </tr>
                                        <tr>
                                            <td>Machine Model:</td>
                                            <td style=""></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="img-wrap">
                                                    <img src="assets/images/product/" alt="">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            
                                <table>
                                    <thead>
                                        <tr class="thead-default">
                                            <th colspan="3">Machine Description</th>													
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="position-relative editable">
                                            <td>Actual Machine Size with Panel</td>
                                            <td style="" class="machineName">
                                                <input type="text" name="actual_machine" class="form-control p-0 border-0 preview-input" value=" " readonly>
                                            </td>
                                            <td>
                                                <div class="popup">
                                                    <div class="popup-wrap">
                                                        <input type="text" class="form-control" value="">
                                                        <div class="btn-wrap mt-4 d-inline-block">
                                                            <p value="update" class="btn nextPreview d-inline">update</p>
                                                        </div>
                                                        <div class="btn-wrap mt-4 d-inline-block">
                                                            <p value="update" class="btn cancle-btn nextPreview d-inline">cancle</p>
                                                        </div>
                                                    </div>														
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Effective working Size</td>
                                            <td> mm</td>
                                        </tr>
                                        <tr>
                                            <td>CNC Controller</td>
                                            <td>CypCut</td>
                                        </tr>
                                        <tr>
                                            <td>Laser Head Height Controller</td>
                                            <td>BCS 100 </td>
                                        </tr>
                                        <tr>
                                            <td>Auto Focus Laser Head </td>
                                            <td>Raytool (Switzerland)</td>
                                        </tr>
                                        <tr>
                                            <td>Transmission System</td>
                                            <td>Helical Rack and Pinion</td>
                                        </tr>
                                        <tr>
                                            <td>Drive system </td>
                                            <td>Dual Drive</td>
                                        </tr>
                                        <tr>
                                            <td>Linear Motion Guide </td>
                                            <td>HIWIN (Taiwan) 35mm for Y1&Y2, 25mm for X</td>
                                        </tr>
                                        <tr>
                                            <td>Planetary Gear Box </td>
                                            <td>Shimpo (Japan)</td>
                                        </tr>
                                        <tr>
                                            <td>Drag Chain </td>
                                            <td>IGUS “Closed Type” (Germany)</td>
                                        </tr>
                                        <tr>
                                            <td>Lubrication System </td>
                                            <td>Automatic Centralized Lubrication</td>
                                        </tr>
                                        <tr>
                                            <td>Bellow Cover </td>
                                            <td>Stainless Steel Bellow Cover</td>
                                        </tr>
                                        <tr>
                                            <td>Electronic Gas Valve </td>
                                            <td>SMC (Japan)</td>
                                        </tr>
                                        <tr>
                                            <td>Solenoid Valve </td>
                                            <td>SMC (Japan)</td>
                                        </tr>
                                        <tr>
                                            <td>X Axis Servo Motor 1 No </td>
                                            <td>1 KW Yaskawa (Japan)</td>
                                        </tr>
                                        <tr>
                                            <td>X Axis Servo Driver 1 No </td>
                                            <td>1 KW Yaskawa (Japan)</td>
                                        </tr>
                                        <tr>
                                            <td>Y Axis Servo Motor 2 No </td>
                                            <td><?= $yaxis; ?> Yaskawa (Japan)</td>
                                        </tr>
                                        <tr>
                                            <td>Y Axis Servo Driver 2 No </td>
                                            <td><?= $yaxis; ?> Yaskawa (Japan)</td>
                                        </tr>
                                        <tr>
                                            <td>Z Axis Servo Brake Motor 1 No </td>
                                            <td><?= $zaxis; ?> Yaskawa (Japan)</td>
                                        </tr>
                                        <tr>
                                            <td>Z Axis Servo Brake Driver 1 No </td>
                                            <td><?= $zaxis; ?> Yaskawa (Japan)</td>
                                        </tr>
                                        <tr>
                                            <td>Rack and pinion </td>
                                            <td>Helical YYC (Taiwan)</td>
                                        </tr>
                                        <tr>
                                            <td>Operating Software</td>
                                            <td>CypCut</td>
                                        </tr>
                                        <tr>
                                            <td>Chiller </td>
                                            <td>Hanli</td>
                                        </tr>
                                        <tr>
                                            <td>Transformer </td>
                                            <td>Included</td>
                                        </tr>
                                        <tr>
                                            <td>Laser Source</td>
                                            <td><?= $lasermodel; ?></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <strong>
                                                <h5> <?= $i; ?>)<?= $names; ?>
                                                </h5>
                                            </strong>
                                        </div>
                                        <div class="col-sm-12">
                                            <img src="images/" style="height: 300px; margin-left: 300px">
                                        </div>
                                        <div class="col-sm-12">
                                            <p>demo</p>
                                        </div>
                                    </div>

                                
                                <table>
                                    <thead>
                                        <tr class="thead-default">
                                            <th>Customer Scope of Work</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                The Purchaser shall finish all necessary preparation work before equipment installation. 
                                            </td>
                                        </tr>
                                        <tr>	
                                            <td>
                                                <i class="fa fa-check"></i> 
                                                The purchaser will accept and sign the installation report after successful Installation. 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                The supplier will provide training for software and machine operation for 3 days.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                Earthing 3 no’s 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                Gas Bank Nitrogen and Oxygen with Regulator & Pipeline up to Machine.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                Electrical Accessories like wires, cables, MCB etc. up to Machine panel.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                On-line ups 50 KVA with battery.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table>
                                    <thead>
                                        <tr class="thead-default">
                                            <th>Delivery</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                45-60 Days after date of advance payment
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>										
                                <table>
                                    <thead>
                                        <tr class="thead-default">
                                            <th>Warranty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td> 
                                                <i class="fa fa-check"></i>
                                                One Year Warranty against manufacturing faults, Accidental damage will not cover in warranty. 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> 
                                                <i class="fa fa-check"></i>
                                                After one year Engineer service charge and travel expense will be extra. 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> 
                                                <i class="fa fa-check"></i>
                                                Twenty-three Month Warranty for laser Source  <br>
                                                (Optics cable, Optical Module, QBH lens, laser head Lens, bellow covers, consumables parts etc. do not cover under warranty Terms) 
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table>
                                    <thead>
                                        <tr class="thead-default">
                                            <th>Prices</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                CNC Laser Cutting machine with Laser Power Source: 50, 00,000 INR.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table>
                                    <thead>
                                        <tr class="thead-default">
                                            <th>Terms And Condition :</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                30% Advance along with PO and 70% plus all the taxes before Dispatch.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                Forwarding will be extras as actual.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                Quotation will be valid for 15 days.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fa fa-check"></i>
                                                GST @18% extra.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="col-12 btn-wrap m-4 d-inline-block text-center">
                                    <div class="btn-wrap mt-4 d-inline-block">
                                        <button name="submit" type="submit" class="nextPreview d-inline">Next</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </body>
                </html>';
$pdf->writeHTML($content);
// $datetime=date('dmY_hms');
$file_name = "INV_1.pdf";
  ob_end_clean();
$pdf->Output($file_name, 'S');
    // $pdf->writeHTML($pdfHtml);
    // $file_name = "INV_1.pdf";
    // ob_end_clean();
    // $pdf->Output($file_name, 'I');
    error_reporting(E_ALL ^ E_DEPRECATED);	
	include_once('PHPMailer/class.phpmailer.php');	
	require ('PHPMailer/PHPMailerAutoload.php');

	$body='';
	$body .="<html>
	<head>
	<style type='text/css'> 
	body {
	font-family: Calibri;
	font-size:16px;
	color:#000;
	}
	</style>
	</head>
	<body>
	Dear Customer,
	<br>
	Please find attached invoice copy.
	<br>
	Thank you!
	</body>
	</html>";

	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->IsMAIL();
	$mail->IsSMTP();
	$mail->Subject    = "Invoice details";
	$mail->From = "roovinbansal@gmail.com";
	$mail->FromName = "Weldarc India";
	$mail->IsHTML(true);
	$mail->AddAddress('roovinbansal@gmail.com'); // To mail id
	//$mail->AddCC('info.shinerweb@gmail.com'); // Cc mail id
	//$mail->AddBCC('info.shinerweb@gmail.com'); // Bcc mail id

	$mail->AddAttachment($file_name);
	$mail->MsgHTML ($body);
	// $mail->WordWrap = 50;
	$mail->Send();	
	$mail->SmtpClose();
	if($mail->IsError()) {
	echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		echo "Message sent!";					
	};
?>