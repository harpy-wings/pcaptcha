<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">
		::selection {
			background-color: #E13300;
			color: white;
		}

		::-moz-selection {
			background-color: #E13300;
			color: white;
		}

		body {
			background-color: #fff;
			margin: 40px;
			font: 13px/20px normal Helvetica, Arial, sans-serif;
			color: #4F5155;
		}

		a {
			color: #003399;
			background-color: transparent;
			font-weight: normal;
		}

		h1 {
			color: #444;
			background-color: transparent;
			border-bottom: 1px solid #D0D0D0;
			font-size: 19px;
			font-weight: normal;
			margin: 0 0 14px 0;
			padding: 14px 15px 10px 15px;
		}

		code {
			font-family: Consolas, Monaco, Courier New, Courier, monospace;
			font-size: 12px;
			background-color: #f9f9f9;
			border: 1px solid #D0D0D0;
			color: #002166;
			display: block;
			margin: 14px 0 14px 0;
			padding: 12px 10px 12px 10px;
		}

		#body {
			margin: 0 15px 0 15px;
		}

		p.footer {
			text-align: right;
			font-size: 11px;
			border-top: 1px solid #D0D0D0;
			line-height: 32px;
			padding: 0 10px 0 10px;
			margin: 20px 0 0 0;
		}

		#container {
			margin: 10px;
			border: 1px solid #D0D0D0;
			box-shadow: 0 0 8px #D0D0D0;
		}

		.submit-btn {
			color: white;
			background-color: lightslategray;
			border-radius: 25px;
			padding-right: 30px;
			padding-left: 30px;
			padding-top: 8px;
			padding-bottom: 8px;
		}

		form {
			text-align: center;
			display: contents;
		}

		input {
			width: 350px;
			padding: 11px;
			margin: 10px;
			border: 2px solid #778899;
			border-radius: 14px;
		}

		textarea {
			width: 350px;
			padding: 11px;
			margin: 10px;
			border: 2px solid #778899;
			border-radius: 14px;
		}

	</style>
	<link rel="stylesheet" href="http://manlogin.com/public/css/pcap.style.min.css">
</head>

<body>

	<div id="container">
		<h1>Welcome to CodeIgniter!</h1>

		<div id="body">

			<!-- شروع فرم -->
			<form id="myFormID" action="index.php/welcome/post_data" method="post">
				<div>
					<input class="input1" type="text" name="name" placeholder="Name">
				</div>

				<div>
					<input class="input1" type="text" name="email" placeholder="Email">
				</div>

				<div>
					<textarea rows="5" class="input1" name="message" placeholder="Message"></textarea>
				</div>
				<div>
					<!-- تگ مورد استفاده در پی کپچا -->
					<div style="width: 300px;height:180px;overflow:hidden;" id="PCaptcha" class="PCaptcha"></div>
					<!---------------------------------->
				</div>

				<div>
					<a class="submit-btn" href="#" onclick="Submit()">
						Send Email
					</a>
				</div>
			</form>
			<!-- اتمام فرم -->

		</div>

		<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds.
			<?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?>
		</p>

	</div>


	<script>
		// چک کردن وضعیت کپچا سمت کلاینت

		function Submit(event) {
			if (IsValidPCaptcha()) {
				document.getElementById("myFormID").submit()
				//FormSubmit
				/*

				// pure js & html
				document.getElementById("myFormID").submit()

				// eg. using axios
				axios.post(...)

				*/
			} else {
				//DoCaptcha
				alert("قسمت من ربات نیستم کامل به درستی انجام نشده است!!")
			}
		}

	</script>
	<script src="http://manlogin.com/captcha/0x4e2e/92713e778184f751c12aa780844d35ac52ce7e305eee15ce9d1e345a76756bc3">
	</script>

</body>

</html>
