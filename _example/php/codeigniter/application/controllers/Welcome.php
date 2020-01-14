<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}
	public function post_data()
	{
		$name 		= $this->input->post("name",TRUE);
		$email 		= $this->input->post("email",TRUE);
		$message 	= $this->input->post("message",TRUE);
		$pcaptcha 	= $this->input->post("pcaptcha",TRUE);
		$uid 		= "0x4e2e"; // کد یکتای کپچای شما در سایت من لاکین
		$secretKey 	= "e8414ae66a37806bae1166a6297ce302a393e70b8e444b37abe20fe4e277779c"; // کلید خصوصی

		$url = "http://manlogin.com/captcha/cheack/v1/$uid/$secretKey/$pcaptcha";
		$verifyResponse = file_get_contents($url);
		$responseData = json_decode($verifyResponse);
		if(isset($responseData->success) && $responseData->success){
			// کپچای شما تایید شده است و می توانید ادامه کار رو انجام دهید
			echo "با موفقیت انجام شد";
		}else{
			echo "قسمت من ربات نیستم کامل به درستی انجام نشده است!!";
		}
		
	}
}
