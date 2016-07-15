<?php 
use PHPUnit_Framework_TestCase as TestCase;
use Jwt\Jwt;

class JwtTest extends TestCase {

	public function testSetAlg()
	{
		$token = Jwt::generate(['iss'=>'domain.com'],'asdf');

		Jwt::setAlg('sha256');

		$this->assertEquals('sha256', Jwt::getAlg(), 'Algoritimo alterado');
	}

	public function testGetAlg()
	{
		$token = Jwt::generate(['iss'=>'domain.com'],'asdf');

		$this->assertEquals('sha256', Jwt::getAlg());

	}

	public function testKey()
	{
		Jwt::$key = 'asdf';

		$this->assertEquals('asdf',Jwt::$key);		
	}

	public function testGenerate()
	{
 		$token = Jwt::generate([
			'iss' => 'domain.com',
			'jti' => '58987-9'
		],'asdf');

		$this->assertNotNull($token);
		
		return $token;
	}

	public function testTokenLocalKey()
	{

		$token = Jwt::generate([],'asdf');

		$this->assertNotNull($token);
	}	

	/**
	 * @depends testGenerate
	 */
	public function testDecode($token)
	{	

		$decode = Jwt::decode($token);
		$this->assertEquals(Jwt::$header,$decode->getHeader(),' header is invalid');

	}

	/**
	 * @depends testGenerate
	 */
	public function testFailDecode($token)
	{	

		$decode = Jwt::decode('Bearer ');
		$this->assertEquals(Jwt::$header,$decode->getHeader(),' header is invalid');

	}

	/**
	 * @depends testGenerate
	 */
	public function testGetToken($token)
	{
		$tokenInstance = Jwt::getToken($token);

		$this->assertNotNull($token, 'invalid token');

		return $tokenInstance;
	}

	/**
	 * @depends testGetToken
	 */
	public function testGetPayload($token)
	{
		$this->assertEquals($token->getPayload('iss'),'domain.com',' invalid iss payload');
	}
	
	/**
	 * @depends testGetToken
	 */
	public function testCheckSignature($token)
	{
		$this->assertNotNull($token->validSignature('asdf'), 'key: asdf invalid');
	}

	/**
	 * @depends testGetToken
	 */
	public function testValidPayload($token)
	{
		$this->assertTrue($token->validPayload('iss','domain.com'),'invalid iss');
	}

	
}
