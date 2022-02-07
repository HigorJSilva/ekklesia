## Laravel boilerplate Auth Sanctum e estrutura MVCS

Este é um modelo de projeto que inclui modelo de autenticação por papeis utilizando Laravel Sanctum e comandos personalizados para criação de classes MVCS de acordo modelos Pai da estrutura

## Objetivo

O objetivo é poupar tempo na criação da estrutura para poder focar na implementação das regras de neǵocio do projeto. O comando ```  php artisan make:controller MeuController --model=Meu --api --requests  ``` é o comando principal e criará toda a estrutura básica para o modelo escolhido.


## Instalação

Clone o respositório

    git clone git@github.com:HigorJSilva/laravel-multiAuth-boilerplate.git

Acesse a página do repositório

    cd laravel-multiAuth-boilerplate

Instale as dependências usando o composer

    composer install

Duplique o arquivo example.env e faça as alterações no arquivo .env

    cp .env.example .env

Creie a chave da aplicação

    php artisan key:generate

Execute a criação do banco de dados (**Configure a conexão no arquivo .env antes de executar o comando**)

    php artisan migrate

Inicie o servidor Local

    php artisan serve

O acesso será em http://localhost:8000/api

## Popule o banco de dados

Execute o comando para popular o banco de dados

    php artisan db:seed

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
