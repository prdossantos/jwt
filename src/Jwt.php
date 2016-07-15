<?php
namespace Jwt;

class Jwt {

	public static 	$header;
	private static 	$instance = null;
	private static 	$payload;
	private static 	$signature;
	public static 	$key;
	private static 	$alg;
	private static 	$tokenDecoded;
	private static 	$token;

	public static function getInstance() {
		if(!self::$instance) self::$instance = new Jwt; 

		return self::$instance;
	}

	public static function setAlg($alg='')
	{
		if( $alg )
			self::$alg = $alg;
		else
			self::$alg = 'sha256';

		return self::$alg;
	}

	public static function getAlg()
	{
		return (self::$alg) ? self::$alg : 'sha256';
	}

	public static function setHeader($arg1,$arg2)
	{
		if(is_array($arg1)) {
			self::$header = $arg1;
		} else if($arg1 && $arg2) {
			self::$header[$arg1] = $arg2;
		} else {
			return "Invalid argument";
		}

		return self::$header;
	}

	/**
	 * Gera um novo token
	 * @param    array  $payload 	 
	 * @param 	 string $local_key  Chave que será utilizada para gerar a signature
	 * @return   string         	token
	 */
	public static function generate($payload,$local_key='')
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

	public static function decode($token)
	{
		$parts = explode('.',$token);

		$header = json_decode(base64_decode($parts[0]),true);
		$payload = json_decode(base64_decode($parts[1]),true);
		$signature = isset($parts[2]) ? $parts[2] : '';

		self::$tokenDecoded = ['header'=>$header,'payload'=>$payload,'signature'=>$signature];
		if(!self::$token) self::$token = $token;

		return self::getInstance();
	}

	public function validSignature($key)
	{
		if($key && self::$tokenDecoded) {
			$parts = self::$tokenDecoded;

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
	 * @param    string $field campo a ser validado
	 * @param    string $value valor passado
	 * @param    boolean $return retorna o valor comparado
	 * @return   boolean        
	 */
	public function validPayload($field=null, $value=null, $return=false)
	{

		$parts = self::$tokenDecoded;
		$payload = $parts['payload'];

		if(!$value) {
			switch ($field) {
				case 'iss':
					$value = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
				break;
			}
		}

		if( isset($payload[$field]) && $payload[$field] == $value )
			return ($return) ? $payload[$field] : true;

		return false;
	}

	/**
	 * Retorna os parametros do header
	 * @param    string $field campo a ser retornado
	 * @return   string|array       
	 */
	public function getHeader($field=null)
	{
		$parts = self::$tokenDecoded;
		$header = $parts['header'];

		if( isset($header[$field]) )
			return $header[$field];

		return $field ? null : $header;
	}

	/**
	 * Retorna os parametros do payload
	 * @param    string $field campo a ser retornado
	 * @return   string|array        
	 */
	public function getPayload($field=null)
	{
		$parts = self::$tokenDecoded;
		$payload = $parts['payload'];

		if( isset($payload[$field]) )
			return $payload[$field];

		return $field ? null : $payload;
	}



	/**
	 * Retorna uma instancia da classe, caso o token seja valido
	 * @param    string $headerAuth  caso você queira passar o token manualmente,
	 *                               por padrão o token é captado pelo header "Authorizarion"
	 *                               que será passado na requisição.
	 * @param 	 string	$type  tipo de autorização, DEFAULT (Bearer)
	 * @return   instance|boolean        
	 */
	public static function getToken($headerAuth=null,$type='Bearer')
	{
		
		if(!$headerAuth) $headerAuth = (isset(getallheaders()['Authorization'])) ? rtrim(getallheaders()['Authorization']) : null;

		if(substr($headerAuth,0,strlen($type))){
			self::$token = trim(substr($headerAuth,strlen($type)));
			self::decode(self::$token);

			return self::getInstance();
		}

		return false;
	}

}