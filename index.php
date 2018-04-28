<?php
$xuser = $_POST['user'];
$xpass = $_POST['pass'];

error_reporting (E_ALL | E_STRICT);

function grab($url, $par=null){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'okhttp/3.6.0');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if(isset($par)){
		curl_setopt($ch, CURLOPT_POSTFIELDS, $par);
	}
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/x-www-form-urlencoded'
	));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$html = curl_exec($ch);
	return $html;
	curl_close($ch);
}



include(dirname(__FILE__)."/cipher/phpCrypt.php");
use PHP_Crypt\PHP_Crypt as PHP_Crypt;

function encX($str){
	$key   = 'YWU0MzIzNTNhNGViNGViOQ==';
  $key   = base64_decode($key); 
  $key  .= "\0\0\0\0\0\0\0\0";
	$crypt = new PHP_Crypt($key, PHP_Crypt::CIPHER_3DES, PHP_Crypt::MODE_ECB, PHP_Crypt::PAD_ZERO);
	$out   = base64_encode($crypt->encrypt($str));
	$out   = str_replace('/', '_', $out);
	$out   = str_replace('+', '-', $out);
	return $out;
}

function encZ($str){
	$key   = 'QHh5c2Q1JEV0bSNocTdwcA==';
  $key   = base64_decode($key); 
  $key  .= "\0\0\0\0\0\0\0\0";
	$crypt = new PHP_Crypt($key, PHP_Crypt::CIPHER_3DES, PHP_Crypt::MODE_ECB, PHP_Crypt::PAD_ZERO);
	$out   = base64_encode($crypt->encrypt($str));
	$out   = str_replace('/', '_', $out);
	$out   = str_replace('+', '-', $out);
	return $out;
}

$phone  = $xuser;
$msisdn = encX($phone);

$pass   = encX($xpass);
$data = '{"secret_key":"8a1d5a0678e140ac","brand":"Samsung","model":"Galaxy S5","msisdn":"=MSISDN=","password":"=PASS=","lang":"id","channel":"android","version":"3.3.0","device_os":"android","keep_signin":"1"}';
$data = str_replace("=MSISDN=", $msisdn, $data);
$data = str_replace("=PASS=", $pass, $data);
$data = base64_encode($data);

$data = 'jsondata={"contents":"'.$data.'"}';

$url = "aHR0cDovL2FwaS5heGlzbmV0LmlkL25ld2F4aXNuZXQvc3NvL3NpZ25pbnYy";
$url = base64_decode($url); 
$get = grab($url, $data);

$error = "";
if($get == ""){
   $status = "error";
   $reason = "Kesalahan saat mengambil data ke server";
}else{
   $data = json_decode(base64_decode($get), 1);
   if($data['code'] != "200"){
      $status = "error";
      $reason = $data['message'];
   }else{
      $status = "success";
      $token = $data['data']['token'];
      $phone = encZ($phone);
   }
}


if($status == "error"){
    $datax = array("status" => "error", "reason" => $reason);
}else{
    $datax = array("status" => "success", "msisdn" => "$phone", "token" => "$token");

}

print json_encode($datax);

