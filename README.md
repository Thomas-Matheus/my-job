#### Sobre a aplicação
Foi criado um sistema de currículo online, baseado em micro-serviços e api rest.

#### APIs
Para ficilitar a visualização das APIs é recomendado que seja visualizado pela documentação.

    http://localhost/api-my-job/web/app_dev.php/api/doc
    
A URL acima irá exibir todas as APIs que o projeto possui exibindo também os tipos de requisição e também os campos necessários para a requisição, junto também de uma área de sandbox. 

#### Tecnologias Utilizadas

  * Composer
  * Symfony Framework 3.3.*

#### Libs Utilizadas

  * FOSRestBundle
  * APIDocBundle
  * CORSBundle
  * SerializerBundle
  * JWTAuthenticationBundle

#### Requisitos

  * Composer
  * PHP 7.0

#### Configuração

* Ao realizar o clone do projeto, navegue até a pasta `myjob` e execute o comando `composer install` em seu terminal, para realizar o download de todas as dependências do framework.
* Em seguida rode o seguinte comando `php bin/concole doctrine:schema:update --force --dump-sql`, o comando fara a criação das tabelas baseado nas entidades que foram criadas.

  
**OBS**.:
Provavelmente o Google não irá permitir o envio do email devido ao nível de segurança, sendo necessário acessar o link `https://www.google.com/settings/security/lesssecureapps` e **ATIVAR** o método 'menos seguro' nas configurações do seu email que irá realizar o `envio do email` para o destinatário.

Ao rodar o **composer** ao final do download das dependência, será solicitado os dados de usuário do email de preferência **gmail**, para que seja possível realizar o envio de email.

Caso não tenha adicionado os dados necessários durante o composer, procurar o arquivo `parameters.yml` no diretório `api/app/config/parameters.yml`.

Ficando da seguinte forma:
```yml
# Diretório -> app/config/parameters.yml

mailer_user: seuemail@gmail.com       # Email
mailer_password: suasenha@secreta     # Senha do email
```

Alterar também os dados do banco de dados:
```yml
# Diretório -> app/config/parameters.yml

database_host: 127.0.0.1
database_name: nome-do-banco
database_user: usuario
database_password: senha
```
