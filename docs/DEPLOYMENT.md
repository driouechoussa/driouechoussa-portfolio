# Deploying driouechoussa.me

## Branches

| Branch | Purpose |
|---|---|
| `master` | Day-to-day development. Every push runs the tests and the production build in GitHub Actions. |
| `production` | What runs on the live server. Only update it from a `master` that passes CI. |

Release a new version:

```bash
git checkout production
git merge --ff-only master
git push origin production
git checkout master
```

Then, on the server: `bash deploy.sh`.

## Server requirements

- **PHP 8.4 or newer**, with the extensions `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `intl`, `mbstring`, `openssl`, `pdo_mysql` (or `pdo_sqlite`), `tokenizer`, `xml` and `zip`.
- Composer 2.
- Node 20+ to build the CSS/JS. If the host has no Node, run `npm run build` on your computer and upload `public/build/` before running the deploy script.
- MySQL 8 / MariaDB 10.6+, or SQLite.
- HTTPS certificate (Let's Encrypt). The site redirects HTTP to HTTPS and sends HSTS in production.
- **The web server's document root must be the `public/` folder**, never the project root (that would expose `.env`).

## First deploy

```bash
git clone -b production https://github.com/driouechoussa/driouechoussa-portfolio.git
cd driouechoussa-portfolio
cp .env.production.example .env
nano .env                      # fill in every CHANGE_ME value
bash deploy.sh --first
php artisan make:filament-user # use an email listed in ADMIN_EMAILS
```

`deploy.sh --first` generates the app key, links `storage/`, runs the migrations, loads the starter content and builds the caches.

Then log in at `https://driouechoussa.me/admin` and:

1. **Profile → Contact details**: enter an email that can receive mail, then click **Send test email** and check that it arrives.
2. **Profile → CV**: upload your current CV.
3. Replace the starter projects and posts with your own.

## Updates

```bash
cd driouechoussa-portfolio
bash deploy.sh
```

The site shows a maintenance page for the few seconds this takes. The script:

1. resets the code to `origin/production`;
2. installs PHP packages without dev tools and rebuilds the front end;
3. runs new migrations (`--force`);
4. rebuilds the config, route, view and Filament caches.

Content edited in `/admin` is never overwritten: the seeder refuses to run once content exists.

## After changing `.env`

The configuration is cached in production, so changes to `.env` take effect only after:

```bash
php artisan optimize
```

## Scheduled tasks and queues

None needed. Contact-form emails are sent during the request (`QUEUE_CONNECTION=sync`).

## Safety built in for production

- `APP_DEBUG=false`: visitors see the site's own error pages, never stack traces.
- `migrate:fresh`, `db:wipe` and similar commands are refused, so the live database cannot be wiped by mistake.
- HTTPS is enforced, cookies are secure and encrypted, and every page sends a Content-Security-Policy and other security headers.
- `/admin` only accepts the emails in `ADMIN_EMAILS`.
- The contact form has a hidden spam trap and a limit of 3 messages per minute and 20 per day per visitor.
- `robots.txt` allows search engines only when `APP_ENV=production`.

## Behind Cloudflare or a load balancer

Set `TRUSTED_PROXIES="*"` (or the proxy IPs) in `.env` and run `php artisan optimize`. Without it, the site cannot tell that visitors use HTTPS, and every visitor shares one rate limit.

## Troubleshooting

| Symptom | Fix |
|---|---|
| 500 error right after deploy | Read `storage/logs/laravel-YYYY-MM-DD.log`. |
| Styles missing | `public/build/` wasn't built: run `npm ci && npm run build`. |
| Uploaded images or CV return 404 | Run `php artisan storage:link`. |
| `.env` change ignored | Run `php artisan optimize`. |
| Contact emails not arriving | Check the `MAIL_*` settings, then use **Send test email** in `/admin`. Check spam and your domain's SPF/DKIM records. |
| Can't open `/admin` (403) | Your email isn't in `ADMIN_EMAILS`; fix it and run `php artisan optimize`. |
| Health check | `https://driouechoussa.me/up` returns 200 when the app is running. |
