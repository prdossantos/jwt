<?php 
use PHPUnit_Framework_TestCase as TestCase;

class JwtTest extends TestCase {

	public function testHeader()
	{
		$token = \src\Jwt\Jwt::token(['iss'=>'domain.com'],'asdf');

		$this->assertEquals('HS256', \src\Jwt\Jwt::$header['alg'], 'header default declarado');
	}

	public function testSetAlg()
	{
		$token = \src\Jwt\Jwt::token(['iss'=>'domain.com'],'asdf');

		\src\Jwt\Jwt::setAlg('HS256');

		$this->assertEquals('HS256', \src\Jwt\Jwt::$header['alg'], 'header alterado após o construtor');
	}

	public function testGetAlg()
	{
		$token = \src\Jwt\Jwt::token(['iss'=>'domain.com'],'asdf');

		$this->assertEquals('sha256', \src\Jwt\Jwt::getAlg());

	}

	public function testKey()
	{
		\src\Jwt\Jwt::$key = 'asdf';

		$this->assertEquals('asdf',\src\Jwt\Jwt::$key);		
	}

	public function testToken()
	{

		\src\Jwt\Jwt::$key = 'asdf';

		$token = \src\Jwt\Jwt::token([
			'iss' => 'domain.com',
			'jti' => '58987-9'
		]);

		$this->assertNotNull($token);
	}

	public function testTokenLocalKey()
	{

		$token = \src\Jwt\Jwt::token([],'asdf');

		$this->assertNotNull($token);
	}	

	public function testDecode()
	{
		\src\Jwt\Jwt::$key = 'asdf';

		$token = \src\Jwt\Jwt::token([
			'iss' => 'domain.com',
			'jti' => '58987-9'
		]);	

		$decode = \src\Jwt\Jwt::decode($token);

		$this->assertEquals(\src\Jwt\Jwt::$header,$decode['header'],' header is invalid');

	}

	public function testCheckSignature()
	{
		\src\Jwt\Jwt::$key = 'asdf';

		$token = \src\Jwt\Jwt::token([
			'iss' => 'domain.com',
			'jti' => '58987-9'
		]);	

		$this->assertTrue(\src\Jwt\Jwt::checkSignature('asdf',$token), 'key: asdf invalid');
	}
}
