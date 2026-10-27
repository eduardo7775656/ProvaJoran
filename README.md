# Sistema de Assistência Técnica — PHP + XAMPP

Projeto de avaliação prática: autenticação, clientes, equipamentos e ordens de serviço.

## Requisitos
- XAMPP com Apache, PHP 8+ e MySQL/MariaDB.
- Extensões PHP `pdo_mysql` e `openssl` habilitadas.
- MySQL/MariaDB configurado para escutar na porta **3307**.

## Instalação
1. Copie a pasta `sistema` para `C:\xampp\htdocs\assistencia_tecnica\`.
2. Abra o XAMPP Control Panel e inicie **Apache** e **MySQL**.
3. Se o MySQL ainda estiver na porta 3306, configure-o para 3307 em `C:\xampp\mysql\bin\my.ini`, nas seções `[client]` e `[mysqld]`:
   ```ini
   port=3307
   ```
   Salve e reinicie o MySQL. Verifique se não existe outro serviço ocupando a porta.
4. Abra `http://localhost/phpmyadmin`. Se o phpMyAdmin ainda apontar para 3306, configure a conexão em `C:\xampp\phpMyAdmin\config.inc.php` adicionando/ajustando:
   ```php
   $cfg['Servers'][$i]['host'] = '127.0.0.1';
   $cfg['Servers'][$i]['port'] = '3307';
   ```
   O local exato depende da versão do XAMPP.
5. No phpMyAdmin, importe `saep_db.sql` (arquivo na raiz deste pacote).
6. Confira `sistema/config.php`: usuário `root`, senha vazia e porta 3307 são padrões comuns de XAMPP, mas ajuste se sua instalação for diferente.
7. Abra no VS Code a pasta `C:\xampp\htdocs\assistencia_tecnica\sistema`.
8. Acesse `http://localhost/assistencia_tecnica/`.

## Login inicial
- E-mail: `admin@assistencia.local`
- Senha: `password`

O script também cria `mariana@assistencia.local` e `rafael@assistencia.local`, ambos com a mesma senha de demonstração.

## Sobre as portas
- **3307 é a porta do MySQL/MariaDB**, usada pelo PHP na conexão PDO.
- A aplicação PHP é servida pelo Apache, normalmente em `http://localhost/` na porta 80. Se o Apache estiver configurado em outra porta, use-a na URL.
- Não é necessário configurar o servidor PHP para escutar na porta 3307; essa porta não deve ser compartilhada entre Apache e MySQL.

## Segurança e limitações
- Login com `password_verify`, consultas preparadas PDO, token CSRF, escape HTML e expiração de sessão após 15 minutos de inatividade.
- O CPF digitado no cadastro é criptografado com AES-256-GCM e não aparece na listagem.
- A chave de criptografia deve ser mantida fora do diretório público, em variável de ambiente `APP_ENCRYPTION_KEY`, com 32 bytes codificados em Base64. O valor fallback no `config.php` é apenas demonstrativo e deve ser substituído antes de dados reais.
- Os CPFs dos registros de exemplo não são CPFs reais; são marcadores de demonstração. Os cadastros novos usam criptografia.
- O projeto é para ambiente local/educacional. Antes de publicar na internet, configure HTTPS, chaves e credenciais fortes, backups, permissões, logs e política de privacidade.

## Entregas
- `documentacao.pdf`: documentação funcional/técnica resumida.
- `diagrama_er.png`: diagrama entidade-relacionamento.
- `saep_db.sql`: criação e população de todas as tabelas (mínimo de três registros por tabela).
- `sistema/`: código-fonte PHP, CSS e JavaScript.
