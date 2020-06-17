# Light framework

### Objetivo:
Criar um framework do zero com php 7 para estudo e facilitar na criação de novos projetos de php.

### Requisitos: 
   1. PHP 7.x
   2. Composer

### Instalação:
   1. Clone este repositório.
   2. Apague a pasta .git (Para iniciar um novo projeto usando esse framework como base)
   3. Abra o terminal e execute o comando:  ```composer install```
   4. Para rodar o projeto basta apenas executar esse 'comando: ```php -S localhost:8000 -t public``` 

### Sobre: 
Este framework utiliza o padrão MVC (model-view-controller) como design-pattern e twig para manipulação da view e templates 


### Features:
    
* ##### Error/Exception handling:
    Segura os erro eee
* ##### Router:
    Com o router você pode criar suas rotas no padrão REST (GET, POST, PUT, DELETE) e direcionar para qual controller e função será direcionado os dados ou parâmetros passads na URI.
* ##### DotEnv
    As variáveis de ambiente são carregadas pela lib dotEnv, para usar você precisa copiar o arquivo .env.example e nomear a cópia como apenas .env então la você poderá usar as variaveis como desejar
* ##### Migrations:
    As migrations são feitas pela lib phinx, por padrão o phinx vem configurado com as variaveis do ambiente mas pode ser alterado no arquivo phinx.php




