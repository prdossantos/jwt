<?php
namespace src\Jwt;

class Jwt {

	static public $header;
	static private $payload;
	static private $signature;
	static public $key;
	static private $alg;

	static public function setAlg($alg='')
	{
		if( $alg )
			self::$alg = $alg;
		else
			self::$alg = 'sha256';

		return self::$alg;
	}

	static public function getAlg()
	{
		return (self::$alg) ? self::$alg : 'sha256';
	}

	static public function setHeader($args)
	{
		if(is_array($args)) {
			self::$header = $args;
		}

		return "Invalid argument";
	}

	/**
	 * Gera um novo token
	 * @param    array  $payload 	 
	 * @param 	 string $local_key  Chave que será utilizada para gerar a signature
	 * @return   string         	token
	 */
	static public function token($payload,$local_key='')
	{

	 	if(!self::$header) self::$header = ['typ'=>'JWT','alg'=>'HS256'];
 		
 		self::$payload = $payload;

 		$header = base64_encode(json_encode(self::$header));
 		$payload = base64_encode(json_encode(self::$payload));

 		$token = "$header.$payload"; 

 		$key = ($local_key) ? $local_key : self::$key;

 		if(!$key)
 			die('Informe uma chave global ou local');


 		$signature = base64_encode(hash_hmac(self::getAlg(), $token, $key, true));

 		$token_w_signature = "$token.$signature"; 

 		return $token_w_signature;

	}

	static public function decode($token)
	{
		$parts = explode('.',$token);

		$header = json_decode(base64_decode($parts[0]),true);
		$payload = json_decode(base64_decode($parts[1]),true);
		$signature = isset($parts[2]) ? $parts[2] : '';

		return ['header'=>$header,'payload'=>$payload,'signature'=>$signature];
	}

	static public function checkSignature($key,$token)
	{
		if($key && $token) {
			$parts = self::decode($token);

			$header = base64_encode(json_encode($parts['header']));
			$payload = base64_encode(json_encode($parts['payload']));
			$signature = base64_encode(hash_hmac(self::getAlg(), "$header.$payload", $key, true));

			if($signature == $parts['signature'])
				return true;			
		}

		return false;
	}

	/**
	 * Valida os parametros do payload
	 * @param    string $token token com campos originais
	 * @param    string $field campo a ser validado
	 * @param    string $value valor passado
	 * @return   boolean        
	 */
	static public function checkPayload($token, $field, $value)
	{
		$parts = self::decode($token);
		$payload = $parts['payload'];

		if( isset($payload[$field]) && $payload[$field] == $value )
			return true;

		return false;
	}
}