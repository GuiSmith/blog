# Blog Slim PHP – Ambiente com Docker

Este projeto é um **exemplo de aplicação PHP utilizando o framework Slim**, com **MariaDB rodando em container Docker** e gerenciamento de dependências via **Composer**.

A ideia dessa estrutura é permitir que qualquer pessoa consiga **clonar o repositório e rodar o projeto rapidamente**, sem precisar instalar PHP, Composer ou MariaDB manualmente.

---

# Estrutura do Projeto

```
blog/
│
├── docker-compose.yml
├── .env
├── .env.template
│
├── db/
│   └── ddl.sql
│
└── php/
    ├── Dockerfile
    ├── composer.json
    ├── public/
    ├── app/
    └── resources/
```

Cada parte tem uma função específica:

| Pasta / Arquivo      | Função                                      |
| -------------------- | ------------------------------------------- |
| `docker-compose.yml` | Orquestra os containers da aplicação        |
| `.env`               | Configurações reais do ambiente             |
| `.env.template`      | Modelo do `.env` para quem clonar o projeto |
| `db/`                | Scripts SQL para inicialização do banco     |
| `php/`               | Código da aplicação PHP                     |
| `php/Dockerfile`     | Define a imagem da aplicação                |

---

# Arquivo `.env.template`

O `.env.template` é apenas **um modelo do arquivo `.env`**.

Ele permite que outras pessoas saibam **quais variáveis precisam existir** no `.env`.

O `.env` contém **variáveis de ambiente usadas pela aplicação e pelo banco de dados**.

Você deve escolher as próprias credenciais, a aplicação não está usando o usuário root.

Exemplo:

```
DB_DRIVER=mysql
DB_HOST=db
DB_PORT=3306

MYSQL_ROOT_PASSWORD=your_root_password
MYSQL_DATABASE=blog
MYSQL_USER=blog
MYSQL_PASSWORD=blog
```

Quando alguém clona o projeto deve executar:

```
cp .env.template .env
```

E então ajustar os valores conforme necessário.

---

# Pasta `db/`

Essa pasta contém **scripts SQL usados para inicializar o banco de dados**.

Exemplo:

```
db/
 └── ddl.sql
```

O arquivo `ddl.sql` pode conter:

* criação de tabelas
* inserts iniciais
* estrutura do banco

O Docker executa automaticamente esses scripts na primeira inicialização do banco.

---

# `php/Dockerfile`

Esse arquivo define **como a imagem da aplicação PHP será construída**.

Principais responsabilidades:

* instalar extensões necessárias do PHP
* instalar o Composer
* instalar dependências do projeto
* iniciar o servidor PHP

Exemplo simplificado do que ele faz:

1. usa a imagem oficial `php`
2. instala extensões (`pdo`, `pdo_mysql`, etc.)
3. instala o Composer
4. instala dependências do `composer.json`
5. inicia o servidor embutido do PHP

Isso permite rodar a aplicação **sem precisar instalar PHP no sistema local**.

---

# `docker-compose.yml`

Esse arquivo define **todos os containers necessários para rodar o projeto**.

Neste caso temos dois serviços:

## Banco de dados

```
db:
  image: mariadb
```

Esse container:

* cria o banco automaticamente
* executa scripts da pasta `db`
* expõe a porta `3307`

---

## Aplicação PHP

```
app:
  build:
    context: ./php
```

Esse container:

* constrói a imagem usando `php/Dockerfile`
* monta o código da aplicação
* expõe o servidor PHP na porta `8000`

---

# Como Rodar o Projeto

### 1. Clonar o repositório

```
git clone <repo>
cd blog
```

---

### 2. Criar o `.env`

```
cp .env.template .env
```

---

### 3. Subir os containers

```
docker compose up --build
```

---

### 4. Acessar a aplicação

```
http://localhost:8000
```

---

# Banco de Dados

O banco será inicializado automaticamente usando os scripts da pasta:

```
db/
```

Caso seja necessário recriar o banco:

```
docker compose down -v
docker compose up --build
```

O parâmetro `-v` remove os volumes do banco.

---

# Observações

Esse projeto foi estruturado para:

* facilitar desenvolvimento local
* evitar dependências instaladas no sistema
* permitir que qualquer pessoa execute o projeto com Docker

Toda a infraestrutura necessária (PHP, Composer e MariaDB) é executada dentro dos containers.

---

# Requisitos

Para rodar o projeto é necessário ter instalado:

* Docker
* Docker Compose
