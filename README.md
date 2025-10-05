# Projeto ACALI: Academy Life

O Acali é o projeto de um sistema web de gerenciamento de grupos de pesquisa, que visa centralizar e organizar a produção científica. Ele oferece um ambiente digital para que estudantes e pesquisadores possam gerir todo o ciclo de desenvolvimento de seus projetos, desde a idealização até a publicação dos resultados.

A problemática que encontramos foi que ainda não há um sistema unificado onde o estudante possa visualizar, editar e entregar seus trabalhos acadêmicos. Há muitas ferramentas com funcionalidades diferentes, mas nenhuma focada em englobar todo esse processo. Além disso, gera um grande volume de informações, que ficam dispersas em locais diversos.

---

### Tecnologias Utilizadas

* Backend: PHP 8.3
* Servidor Web: Nginx
* Banco de Dados: MySQL 8.4
* Ambiente: Docker & Docker Compose
* Gerenciador de Dependências: Composer

---

### Execução do projeto

**1. Clone o Repositório**

```bash
git clone https://github.com/deborannies/acali.git
cd acali
```

**2. Crie o arquivo .env**

O projeto utiliza um arquivo `.env` para as variáveis de ambiente. Você pode copiar o arquivo de exemplo com o comando:

```bash
cp .env.example .env
```

**3. Suba os Containers**

Este comando irá construir e iniciar os containers do Nginx, PHP e MySQL em segundo plano.

```bash
sudo ./run up -d
```

**5. Instale as Dependências**

Execute o Composer dentro do container PHP para instalar as bibliotecas necessárias.

```bash
sudo ./run composer install
```

**6. Banco de Dados**

```bash
sudo ./run db:reset
sudo ./run db:populate
```

**7. Acesse a Aplicação**

Pronto! A aplicação deve estar rodando. Abra seu navegador e acesse:

[http://localhost](http://localhost)

---

### Dados de Acesso para Teste

Após popular o banco de dados, você pode usar as seguintes credenciais para fazer login:

* **Administrador:**
    * **E-mail:** `admin@teste.com`
    * **Senha:** `123456`

* **Usuário Comum:**
    * **E-mail:** `user@teste.com`
    * **Senha:** `123456`

---
