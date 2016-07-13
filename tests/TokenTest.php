<?php 
use PHPUnit_Framework_TestCase as TestCase;
use Jwt\Token;

class TokenTest extends TestCase {

	public function testHeader()
	{
		$token = Token::generate(['iss'=>'domain.com'],'asdf');

		$this->assertEquals('HS256', Token::$header['alg'], 'header default declarado');
	}

	public function testSetAlg()
	{
		$token = Token::generate(['iss'=>'domain.com'],'asdf');

		Token::setAlg('sha256');

		$this->assertEquals('sha256', Token::getAlg(), 'Algoritimo alterado');
	}

	public function testGetAlg()
	{
		$token = Token::generate(['iss'=>'domain.com'],'asdf');

		$this->assertEquals('sha256', Token::getAlg());

	}

	public function testKey()
	{
		Token::$key = 'asdf';

		$this->assertEquals('asdf',Token::$key);		
	}

	public function testToken()
	{

		Token::$key = 'asdf';

		$token = Token::generate([
			'iss' => 'domain.com',
			'jti' => '58987-9'
		]);

		$this->assertNotNull($token);
	}

	public function testTokenLocalKey()
	{

		$token = Token::generate([],'asdf');

		$this->assertNotNull($token);
	}	

	public function testDecode()
	{
		Token::$key = 'asdf';

		$token = Token::generate([
			'iss' => 'domain.com',
			'jti' => '58987-9'
		]);	

		$decode = Token::decode($token);

		$this->assertEquals(Token::$header,$decode['header'],' header is invalid');

	}

	public function testCheckSignature()
	{
		Token::$key = 'asdf';

		$token = Token::generate([
			'iss' => 'domain.com',
			'jti' => '58987-9'
		]);	

		$this->assertTrue(Token::checkSignature('asdf',$token), 'key: asdf invalid');
	}

	public function testCheckPayload()
	{
		$token = Token::generate([
			'iss' => 'domain.com',
			'jti' => '58987-9'
		],'1232');		

		$this->assertTrue(Token::checkPayload($token,'iss','domain.com'),'invalid iss');
		$this->assertTrue(Token::checkPayload($token,'jti','58987-9'),'invalid jti');
	}
}
