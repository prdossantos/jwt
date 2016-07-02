# Easy JWT
Json Web Token sem complicações

## Exemplos
> Com uma chave secreta para cada token
```php
  use \src\Jwt\Jwt;
  $token = Jwt::token([
    'iss' => 'domain.com',
    'jti' => 1234
  ], 'my secret key'); 
```
> Com uma chave secreta global
```php
  use \src\Jwt\Jwt;
  Jwt::$key = 'my global secret key';
  $token = Jwt::token([
    'iss' => 'domain.com',
    'jti' => 1234
  ]); 
```
