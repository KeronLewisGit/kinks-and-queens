# Installing on Hostinger (shared or Business hosting)

The package unzips straight into `public_html/`:

```
public_html/
├── index.php        front controller (points at kq-app/)
├── .htaccess        HTTPS, clean URLs, security headers
├── build/ assets/   compiled CSS and JS, brand photos
├── uploads/         photos uploaded in the admin (scripts blocked)
├── setup.php        one-time installer, deletes itself
└── kq-app/          the Laravel application (web access denied)
```

## 1. Build the package

```bash
scripts/package-hostinger.sh     # → dist/kinksandqueens-hostinger.zip
```

## 2. hPanel

1. **Websites → Manage → Advanced → PHP Configuration:** set PHP to **8.4**.
2. **Databases → MySQL:** create a database and a user. Note the full `u123456789_…` names.
3. **Files → File Manager → public_html:** remove the default `default.php` or `index.html`, upload the zip, then right-click it and choose **Extract** into `public_html`.
4. Copy `kq-app/.env.example` to `kq-app/.env` and edit it:
   - `APP_URL`: the site's full https address
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `SETUP_TOKEN`: any random string of 32 or more characters
   - Optionally, the `MAIL_…` settings, if a mailbox exists for new-request emails

## 3. Run the installer

Open `https://YOUR-DOMAIN/setup.php?token=YOUR_SETUP_TOKEN`. It will:
- generate the app key,
- create the tables,
- load the three textures, all price lists and the photos,
- ask for the owner's name, email and password.

When it finishes, it deletes itself. Then remove the `SETUP_TOKEN` line from `.env`.

Sign in at `/admin/login`, then go to **Settings** and add the WhatsApp number. Until a number is set, WhatsApp buttons stay hidden and customers are sent to Instagram DM.

## Updating

1. Build a new zip.
2. Upload and extract it over the old files. Your `.env`, `uploads/` and the database are untouched.
3. With SSH, run `php artisan migrate --force && php artisan optimize` inside `kq-app/`. Without SSH, add a one-off cron job in hPanel that runs `cd ~/domains/DOMAIN/public_html/kq-app && php artisan migrate --force && php artisan optimize`.

## With SSH instead

```bash
cd ~/domains/DOMAIN/public_html/kq-app
php artisan key:generate --force && php artisan migrate --seed --force
php artisan app:create-admin --name="Owner" --email=owner@example.com --password='a-long-password'
php artisan optimize
rm ../setup.php
```

## Git deployment instead of the zip (for ongoing updates)

The repository is public, so Hostinger can clone it without a deploy key. Built CSS and JS (`public/build`) are committed because Hostinger does not run npm. Composer runs on the server after each pull.

Layout in `public_html/`:

```
public_html/
├── .htaccess        ← copy of deploy/git-root.htaccess (passes every request to kq-src/public)
├── setup.php        ← copy of deploy/setup.php, until it deletes itself
└── kq-src/          ← the Git checkout; .env lives here and is never tracked
```

1. hPanel → Advanced → **GIT** → Create a new repository. Repository: `https://github.com/KeronLewisGit/kinks-and-queens.git`, branch `main`, directory `kq-src`. Click **Deploy**.
2. In File Manager, upload `deploy/git-root.htaccess` to `public_html/.htaccess`, and `deploy/setup.php` to `public_html/setup.php`.
3. Copy `deploy/env.production.example` to `public_html/kq-src/.env` and fill it in with the same values as the zip install.
4. Open `https://YOUR-DOMAIN/setup.php?token=YOUR_SETUP_TOKEN`, then create the owner account on that page.

Updating: run `npm run build` if CSS or JS changed, commit, push to `main`, then click **Deploy** in hPanel → GIT (or turn on Auto Deployment there and add its webhook URL to the GitHub repository). A pull never touches `.env`, `vendor/`, `storage/` or `public/uploads/`.
