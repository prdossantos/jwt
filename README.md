# Easy JWT
Json Web Jwt

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
Instanciando a classe
```php
  use Jwt\Jwt;
```
Gerando uma chave secreta global, a mesma será usada em todos os tokens
```php
  Jwt::$key = 'my global secret key';
```
Gerar um token com uma chave diferente para cada.
```php
  $token = Jwt::encode([
    'iss' => 'domain.com',
    'jti' => 1234
  ], 'my secret key'); 
```
Pegando um token de uma requisição.
```php
  /**
   * Retorna uma instancia da classe, caso o token seja valido
   * @param    string $headerAuth  caso você queira passar o token manualmente,
   *                               por padrão o token é captado pelo header "Authorizarion"
   *                               que será passado na requisição.
   * @param    string $type  tipo de autorização, DEFAULT (Bearer)
   * @return   instance|boolean        
   */
  $token = Jwt::getToken();
```
Validando a assinatura
```php
  $token = Jwt::getToken();
  $token->validSignature('my secret key');
  #output
  boolean
```
Validando um campo do payload
```php
  $token = Jwt::getToken();
  /**
   * Valida os parametros do payload
   * @param    string $field campo a ser validado
   * @param    string $value valor passado
   * @param    boolean $return retorna o valor comparado
   * @return   boolean        
   */
  print $token->validPayload('iss','domain.com');
  #output
  true
```
Retora o valor de um campo payload
```php
  print Jwt::getToken()->getPayload('iss');
  #output
  domain.com
```
Setar um algoritimo de criptografia
```php
  Jwt::setAlg('sha512');
  #default
  sha256
```
