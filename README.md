# Mestria — Sistema de Cadastro e Matrícula em Aulas

Aplicação web simples para cadastro de usuários, criação de aulas e matrícula em aulas. Front-end em HTML/CSS/JavaScript puro e back-end em PHP com banco de dados MySQL.

## Sumário

1. [Pré-requisitos](#1-pré-requisitos)
2. [Estrutura do projeto](#2-estrutura-do-projeto)
3. [Passo a passo de instalação](#3-passo-a-passo-de-instalação)
4. [Como executar o projeto](#4-como-executar-o-projeto)
5. [Como usar a aplicação](#5-como-usar-a-aplicação)

---

## 1. Pré-requisitos

Antes de começar, instale em sua máquina:

| Ferramenta | Versão mínima | Para que serve |
|---|---|---|
| [PHP](https://www.php.net/downloads) | 8.0+ | Executa o back-end (arquivos `.php`) |
| [MySQL Server](https://dev.mysql.com/downloads/mysql/) | 8.0+ | Armazena os dados (usuários, aulas, matrículas) |
| Extensão `mysqli` do PHP | — | Permite ao PHP conversar com o MySQL (geralmente já vem habilitada por padrão) |
| Um navegador | — | Chrome, Firefox, Edge, etc. |

**Verificando se o PHP e a extensão `mysqli` estão instalados**, abra um terminal e rode:

```bash
php -v
php -m
```

O segundo comando lista os módulos instalados — procure por `mysqli` na lista. Se o PHP não estiver instalado, baixe em [php.net/downloads](https://www.php.net/downloads) (Windows: baixe o pacote "Thread Safe" em formato zip e adicione a pasta ao PATH do sistema).


---

## 2. Estrutura do projeto

```
mestria_retro/
├── banco.sql              → Script que cria o banco de dados e as tabelas
├── css/
│   └── styles.css
├── html/
│   ├── main.html          → Tela de login (página inicial do site)
│   ├── cadastro.html      → Tela de criação de conta
│   ├── aulas.html         → Listagem de aulas
│   ├── cadastro_aula.html → Criar nova aula
│   ├── editar_aula.html   → Editar aula existente
│   ├── matriculas.html    → Matrículas do usuário
│   └── perfil.html        → Perfil do usuário
├── img/                   → Imagens usadas na interface
├── js/                    → Scripts que conectam o front-end ao PHP
└── php/
    ├── conexao.php              → Configuração da conexão com o MySQL
    ├── operacoes_user.php       → Login, cadastro, logout, sessão
    ├── operacoes_aulas.php      → CRUD de aulas
    └── operacoes_matriculas.php → Matrícula/cancelamento em aulas
```

---

## 3. Passo a passo de instalação

### 3.1. Extrair o projeto

Extraia o arquivo `.zip` em uma pasta de sua preferência. Ao final, você deve ter uma pasta chamada `mestria_retro` com a estrutura acima.

### 3.2. Criar o banco de dados e as tabelas

1. Abra um terminal **dentro da pasta `mestria_retro`** (a pasta raiz do projeto).
2. Execute o script SQL fornecido, que cria o banco `mestria` e as tabelas `usuarios`, `aulas` e `matriculas`:

   ```bash
   mysql -u root -p < banco.sql
   ```

3. Quando solicitado, digite a senha do usuário `root` do MySQL (a senha que você definiu ao instalar o MySQL).

> Se o comando `mysql` não for reconhecido, é porque a pasta `bin` do MySQL não está no PATH do seu sistema. No Windows, você pode usar o caminho completo, por exemplo: `"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p < banco.sql`.

### 3.3. Criar um usuário próprio para a aplicação

Por segurança, a aplicação não deve se conectar ao banco usando o usuário `root`. Vamos criar um usuário dedicado, apenas com as permissões necessárias.

1. Entre no MySQL como administrador:

   ```bash
   mysql -u root -p
   ```

2. Dentro do prompt do MySQL (`mysql>`), rode os comandos abaixo, **substituindo `DEFINA_UMA_SENHA` por uma senha de sua escolha**:

   ```sql
   CREATE USER 'mestria_app'@'localhost' IDENTIFIED BY 'DEFINA_UMA_SENHA';
   GRANT SELECT, INSERT, UPDATE, DELETE ON mestria.* TO 'mestria_app'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

   > **Guarde essa senha** — você vai usá-la no próximo passo.

   Se preferir testar rapidamente sem senha (não recomendado fora de ambiente de estudo), use:
   ```sql
   CREATE USER 'mestria_app'@'localhost' IDENTIFIED BY '';
   GRANT SELECT, INSERT, UPDATE, DELETE ON mestria.* TO 'mestria_app'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

   Se o usuário `mestria_app` já existir de uma tentativa anterior, não rode `CREATE USER` de novo — em vez disso, redefina a senha com:
   ```sql
   ALTER USER 'mestria_app'@'localhost' IDENTIFIED BY 'DEFINA_UMA_SENHA';
   FLUSH PRIVILEGES;
   ```

### 3.4. Configurar a conexão PHP com o banco

Abra o arquivo `php/conexao.php` em um editor de texto e confira/ajuste estas linhas:

```php
$servername = "localhost";
$username = "mestria_app";
$password = "DEFINA_UMA_SENHA";  // ← coloque aqui a MESMA senha do passo 3.3
$database = "mestria";
```

 **O valor de `$password` precisa ser exatamente igual à senha definida no `CREATE USER`** do passo anterior. Não use a senha do usuário `root` aqui.

---

## 4. Como executar o projeto

1. Abra um terminal **na pasta raiz do projeto** (`mestria_retro`).
2. Inicie o servidor embutido do PHP:

   ```bash
   php -S localhost:8000
   ```

   Esse comando deve ficar rodando (não feche o terminal). Você verá uma mensagem indicando que o servidor está ouvindo em `localhost:8000`.

3. Abra o navegador e acesse:

   ```
   http://localhost:8000/html/main.html
   ```

   Essa é a tela de login/página inicial do site.

> Para parar o servidor quando terminar, volte ao terminal e pressione `Ctrl + C`.

---

## 5. Como usar a aplicação

1. **Criar uma conta:** na tela inicial (`main.html`), clique em "Cadastre-se" e preencha o formulário (`cadastro.html`). A senha é armazenada de forma segura no banco (usando `password_hash`), nunca em texto puro.
2. **Fazer login:** volte para a tela inicial e entre com o e-mail e a senha cadastrados.
3. **Navegar pelo sistema:** após o login, você terá acesso a:
   - `aulas.html` — lista de aulas disponíveis, com opção de cadastrar (`cadastro_aula.html`) e editar (`editar_aula.html`)
   - `matriculas.html` — suas matrículas, com opção de matricular-se ou cancelar
   - `perfil.html` — dados do seu perfil

---


**Resumo rápido dos comandos (depois de configurar tudo uma vez):**

```bash
# 1. Entrar na pasta do projeto
cd mestria_retro

# 2. Subir o servidor PHP
php -S localhost:8000

# 3. Acessar no navegador
http://localhost:8000/html/main.html
```