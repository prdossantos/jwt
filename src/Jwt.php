<?php
namespace Jwt;

class Jwt {

	public static 	$header = array('typ'=>'JWT','alg'=>'HS256');
	private static 	$instance = null;
	private static 	$payload;
	private static 	$signature;
	public static 	$key;
	public static 	$alg = 'HS256';
	private static 	$supported_algs = array(
        'HS256' => array('hash_hmac', 'SHA256'),
        'HS512' => array('hash_hmac', 'SHA512'),
        'HS384' => array('hash_hmac', 'SHA384'),
        // 'RS256' => array('openssl', 'SHA256'),
    );
	private static 	$tokenDecoded;
	private static 	$token;

	public static function getInstance() {
		if(!self::$instance) self::$instance = new Jwt; 

		return self::$instance;
	}

	public static function setAlg($alg='')
	{

		if(!$alg)
			throw new \ErrorException("Algorithm invalid", 1);
			
		if( isset(self::$supported_algs[$alg]) ) {
			self::$alg = $alg;
			self::setHeader('alg',$alg);
		}
		else {
			throw new \ErrorException("Algorithm invalid", 1);
		}

		return self::$alg;
	}

	public static function setHeader($arg1,$arg2)
	{
		if(is_array($arg1))
			self::$header = $arg1;
		else if($arg1 && $arg2)
			self::$header[$arg1] = $arg2;
		else
			throw new \ErrorException("Invalid argument", 1);

		return self::$header;
	}

	private static function doSignature($token,$key)
	{
		$crypt = self::$supported_algs[self::$alg];
		
		list($function,$algorithm) = ($crypt) ? $crypt : array(null=>'');

		$signature = null;

		switch ($function) {
			case 'hash_hmac':
			default:
				$signature = base64_encode(hash_hmac($algorithm, $token, $key, true));
			break;
		}	

		return $signature;
	}

	/**
	 * Gera um novo token
	 * @param    array  $payload 	 
	 * @param 	 string $local_key  Chave que será utilizada para gerar a signature
	 * @return   string         	token
	 */
	public static function encode($payload,$local_key='')
	{
 		
 		self::$payload = $payload;

 		$header = base64_encode(json_encode(self::$header));
 		$payload = base64_encode(json_encode(self::$payload));

 		$token = "$header.$payload"; 

 		$key = ($local_key) ? $local_key : self::$key;

 		if(!$key)
			throw new \ErrorException("Invalid key", 1);


 		$signature = self::doSignature($token,$key);

 		if(!$signature)
 			throw new \ErrorException("Signature invalid", 1);
 			

 		$token_w_signature = "$token.$signature"; 

 		return $token_w_signature;

	}

	public static function decode($token)
	{
		$parts = explode('.',$token);

		if( count($parts) != 3 )
			throw new \ErrorException("Invalid token", 1);
			
		$header = json_decode(base64_decode($parts[0]),true);
		$payload = json_decode(base64_decode($parts[1]),true);
		$signature = $parts[2];

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
			$signature = self::doSignature("$header.$payload", $key);

			if($signature === $parts['signature'])
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
		if(!self::$tokenDecoded)
			return false;

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
		if(!self::$tokenDecoded)
			return null;

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
		if(!self::$tokenDecoded)
			return null;

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

		if(substr($headerAuth,0,strlen($type)) == $type){
			self::$token = trim(substr($headerAuth,strlen($type)));
			self::decode(self::$token);
		}
 		
 		return self::getInstance();
	}

}