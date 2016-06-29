<?php


require_once('../../vendor/autoload.php');

use \src\Jwt\Jwt;

if(isset(getallheaders()['Authorization'])) {
	$token = trim(str_replace('Bearer','',getallheaders()['Authorization']));

	$cod = isset($_GET['cod']) ? $_GET['cod'] : 0;

	if(!$cod || !$token)
		die(json_encode(['status'=>0,'msg'=>'Informações necessárias para requisição incompletas.']));


	$parts = Jwt::decode($token);

	if( $parts['payload']['jti'] != $cod )
		die(json_encode(['status'=>0,'msg'=>'Código inválido para esta requisição']));

	$user_key = 'teste';

	if(!Jwt::checkSignature($user_key,$token))
		die(json_encode(['status'=>0,'msg'=>'Token inválido']));


	echo json_encode(['status'=>1,'msg'=>'Acesso realizado com sucesso','data'=>$token]);		
	exit;
}


if($_SERVER['REQUEST_METHOD'] == 'POST') {

	Jwt::$key = 'teste';

	$token = Jwt::token([
		'iss' => 'domain.com',
		'jti' => isset($_POST['id']) ? $_POST['id'] : mt_rand(10,10)
	]);

	if( $token ) {
		echo json_encode(['status'=>1,'data'=>$token]);
		exit;
	}
}

echo json_encode(['status'=>0,'msg'=>'Método '.$_SERVER['REQUEST_METHOD'].' inválido']); exit;

