[![Gitpod ready-to-code](https://img.shields.io/badge/Gitpod-ready--to--code-blue?logo=gitpod)](https://gitpod.io/#https://github.com/renandmc/e-vent)

## E-Vent

Sistema para gerenciamento de eventos da Fatec Ourinhos.

### Como executar projeto? 
*Necessário Docker
#### 1 - Clonar repositório
```
git clone https://github.com/renandmc/e-vent.git
```
#### 2 - Acessar pasta do projeto
```
cd e-vent
```
#### 3 - Instalar dependências com composer (via Docker)
```
docker run --rm -v $(pwd):/opt -w /opt laravelsail/php80-composer:latest composer install
```
#### 4 - Copiar arquivo de variáveis de ambiente, alterar configurações
```
cp .env.example .env
```
#### 5 - Criar atalho para não digitar './vendor/bin/sail' toda vez
```
alias sail='bash vendor/bin/sail'
```
#### 6 - Executar o comando do Laravel Sail (pode demorar na primeira execução) 
Primeiro comando sobe as máquinas no Docker.
Segundo comando cancela a execução do projeto.
```
sail up -d
sail down
```
#### 7 - Executar comandos para inicializar projeto (gerar key, migrations banco e instalar Breeze)
```
sail artisan key:generate
sail artisan migrate
sail npm install
sail npm run dev
```
#### 8 - Comandos para atualizar dependências (sempre que houver alguma atualização de depedências ou banco de dados do projeto)
```
docker run --rm -v $(pwd):/opt -w /opt laravelsail/php80-composer:latest composer update
sail up -d
sail artisan migrate
```
#### 9 - Após executar o comando o site pode ser acessado em <http://localhost:8080>

### Projeto desenvolvido com Laravel
<a href="https://laravel.com" target="_blank">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="150">
</a>
