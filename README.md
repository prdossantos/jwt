# Easy JWT
Json Web Token sem complicações

## Instalação
> Testes
```sh
  composer install --dev
```

## Testes
```sh
  composer tests
```

## Exemplos
> Instanciando a classe
```php
  use Jwt\Token;
```
> Com uma chave secreta para cada token
```php
  $token = Token::generate([
    'iss' => 'domain.com',
    'jti' => 1234
  ], 'my secret key'); 
```
> Com uma chave secreta global
```php
  Jwt::$key = 'my global secret key';
  $token = Token::generate([
    'iss' => 'domain.com',
    'jti' => 1234
  ]); 
```
> Decodificar um token
```php
  $decode = Token::decode($token);
  #output
  Array(
    [header] => Array
        (
            [typ] => JWT
            [alg] => HS256
        )
    [payload] => Array
        (
            [iss] => domain.com
            [jti] => 6546
        )
    [signature] => fCan1TqMPuFnxKr3/t2GRFA68sWLUInpXMgzj2asgs8=
  )
```
> Verifica a assinatura
```php
  Token::checkSignature('my secret key',$token);
  #output
  boolean
```
> Verifica um campo do payload
```php
  Token::checkPayload($token,'iss','domain.com');
  #output
  boolean
```
> Seta algoritimo de criptografia
```php
  Token::setAlg('sha512');
  #default
  sha256
```
