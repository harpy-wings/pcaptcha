<?php
	$message = array();
	if(isset($_POST)){

		$pcaptcha 	= $_POST["pcaptcha"]; // مقداری است که از طریق فرم ارسال شده است
		$uid 		= "0x4e2e"; // کد یکتای کپچای شما در سایت من لاکین
		$secretKey 	= "e8414ae66a37806bae1166a6297ce302a393e70b8e444b37abe20fe4e277779c"; // کلید خصوصی
		$url        = "http://manlogin.com/captcha/cheack/v1/$uid/$secretKey/$pcaptcha";
		$verifyResponse = file_get_contents($url);
		$responseData = json_decode($verifyResponse);
		if(isset($responseData->success) && $responseData->success){
			// کپچای شما تایید شده است و می توانید ادامه کار رو انجام دهید
			$message["class"] 	= "alert-success";
			$message["text"] 	= "با موفقیت انجام شد";
		}else{
			$message["class"] 	= "alert-danger";
			$message["text"] 	= "قسمت من ربات نیستم به درستی انجام نشده است!!";
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Contact V1</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">

<!--===============================================================================================-->

<link rel="stylesheet" href="http://manlogin.com/public/css/pcap.style.min.css">
</head>
<body>
	<div class="contact1">
		<div class="container-contact1">
			<div class="contact1-pic js-tilt" data-tilt>
				<img src="images/img-01.png" alt="IMG">
			</div>

			<form id="myFormID" class="contact1-form validate-form" action="index.php" method="post">
				<?php 
					if($message != array()){
						echo '<div class="alert '.$message["class"].'">'.$message["text"].'</div>';
						
					}
				?>
				<span class="contact1-form-title">
					Get in touch
				</span>
				<div class="wrap-input1 validate-input" data-validate = "Name is required">
					<input class="input1" type="text" name="name" placeholder="Name">
					<span class="shadow-input1"></span>
				</div>

				<div class="wrap-input1 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
					<input class="input1" type="text" name="email" placeholder="Email">
					<span class="shadow-input1"></span>
				</div>

				<div class="wrap-input1 validate-input" data-validate = "Subject is required">
					<input class="input1" type="text" name="subject" placeholder="Subject">
					<span class="shadow-input1"></span>
				</div>

				<div class="wrap-input1 validate-input" data-validate = "Message is required">
					<textarea class="input1" name="message" placeholder="Message"></textarea>
					<span class="shadow-input1"></span>
				</div>
				<div class="wrap-input1 validate-input" data-validate = "Subject is required" >
					<div style="width: 300px;height:180px;overflow:hidden;"id="PCaptcha"class="PCaptcha"></div>
					<span class="shadow-input1"></span>
				</div>

				<div class="container-contact1-form-btn">
					<button class="contact1-form-btn" type="submit">
						<span>
							Send Email
							<i class="fa fa-long-arrow-right" aria-hidden="true"></i>
						</span>
					</button>
				</div>
			</form>
		</div>
	</div>




<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>
<script src="http://manlogin.com/captcha/0x4e2e/92713e778184f751c12aa780844d35ac52ce7e305eee15ce9d1e345a76756bc3"></script>
</body>
</html>
