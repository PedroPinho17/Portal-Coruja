# Portal Corujinha

Website dedicado à **Corujinha — Centro de Apoio Escolar**, onde simpatizantes e utilizadores podem navegar e aceder a informações sobre a organização, serviços, equipa, notícias e contactos.

Repositório: [https://github.com/PedroPinho17/Portal-Coruja](https://github.com/PedroPinho17/Portal-Coruja)

## Sobre o projeto

O Portal Corujinha é uma aplicação web desenvolvida em **Laravel 12** com **PHP 8.2+**, pensada para apresentar o centro de apoio escolar ao público e permitir a gestão de conteúdos através de um backoffice administrativo.

## Funcionalidades

### Site público

- **Página inicial** — apresentação, serviços, formações, equipa, notícias, protocolos escolares e contacto
- **Sobre nós** — informação sobre a Corujinha
- **Galeria** — fotografias dos centros e atividades
- **Equipa** — membros da equipa
- **Notícias** — publicações e novidades
- **Formulário de contacto** — envio de mensagens por email

### Backoffice (`/admin`)

- Dashboard administrativo
- Gestão de **equipa**, **notícias**, **entidades**, **formações** e **protocolos escolares**
- Gestão de **utilizadores** e perfil
- Autenticação com sessão, rate limiting e alteração obrigatória de password no primeiro login
- Suporte a **WebAuthn** (autenticação com chave de segurança)

## Tecnologias

- Laravel 12
- PHP 8.2+
- Tailwind CSS 4 + Vite
- SweetAlert2, Bootstrap Icons, Bootstrap FileInput
- DeepL (integração para tradução)
- SQLite (configuração padrão em `.env.example`; também suporta MySQL/MariaDB)

## Instalação local

```powershell
git clone https://github.com/PedroPinho17/Portal-Coruja.git
cd Portal-Coruja
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

Alternativa rápida (instala dependências, `.env`, chave, migrations e build):

```powershell
composer setup
```

## Notas importantes

### Autenticação no GitHub

Na primeira vez que fizeres `push`, o GitHub pode pedir login. Podes usar um [Personal Access Token](https://github.com/settings/tokens) em vez da password.

### Ficheiros que não vão para o GitHub

Estes ficheiros/pastas já estão no `.gitignore` e **não são enviados** para o repositório:

- `.env` — credenciais e configuração local (correto não enviar)
- `vendor/` — dependências PHP (quem clonar corre `composer install`)
- `node_modules/` — dependências JavaScript (quem clonar corre `npm install`)

### Quem clonar o projeto precisará de

```powershell
composer install
copy .env.example .env
php artisan key:generate
```

Depois, configurar a base de dados no `.env` e executar `php artisan migrate`.

## Licença

Projeto open-source sob licença [MIT](https://opensource.org/licenses/MIT).
