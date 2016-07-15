<?php


require_once('../../vendor/autoload.php');

use Jwt\Jwt;

$gt = Jwt::getToken();

if($gt) {

	print_r($_SERVER);

	$cod = isset($_REQUEST['cod']) ? $_REQUEST['cod'] : 0;

	if(!$cod )
		die(json_encode(['status'=>0,'msg'=>'Informações necessárias para requisição incompletas.']));

	// print_r($parts);
	if( $gt->getPayload('jti') != $cod )
		die(json_encode(['status'=>0,'msg'=>'Código inválido para esta requisição']));

	$user_key = 'teste';

	if(!$gt->validSignature($user_key))
		die(json_encode(['status'=>0,'msg'=>'Token inválido']));


	// echo json_encode(['status'=>1,'msg'=>'Acesso realizado com sucesso','data'=>$token]);		
	exit;
}
else {

	Jwt::$key = 'teste';

	$token = Jwt::generate([
		'iss' => 'domain.com',
		'jti' => isset($_REQUEST['id']) ? $_REQUEST['id'] : mt_rand(10,10)
	]);

	if( $token ) {
		echo json_encode(['status'=>1,'data'=>$token]);
		exit;
	}
}

// echo json_encode(['status'=>0,'msg'=>'Método '.$_SERVER['REQUEST_METHOD'].' inválido']); exit;

