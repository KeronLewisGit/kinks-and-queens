# Kinks & Queens

Website and pre-order back office for **Kinks & Queens**, a Trinidad & Tobago textured hair brand. They sell Brazilian Kinky Curly, Mongolian Afro Coily and Brazilian Textured Straight bundles, drawstring ponytails and custom clip-ins, all on a pre-order basis.

Built with Laravel 13 (PHP 8.4), Blade, Tailwind CSS v4 and Alpine.js.

## What's inside

**Public site**
- Home page with a texture switcher, "how pre-order works", a wear diary, a Queen Cam mosaic and an FAQ teaser.
- A page for each texture, with an interactive length guide and the full TT$ price list. Every set links to a pre-filled pre-order.
- Pre-order builder with texture, format, set or length, and contact details. Prices are always worked out on the server.
- Custom clip-in service request: customers send their own bundles.
- Texture Match quiz, Queen Cam gallery (filter and lightbox), care guide, FAQ, our story and privacy pages.
- Order tracking: the customer enters their reference plus the last 4 digits of their phone number.

**Admin (`/admin`)**
- **Today:** requests waiting to be confirmed, orders arriving soon and orders ready to hand over.
- **Requests:** filter and search requests, move each one through confirmed → paid → ordered → ready → completed with a note the customer can see, and open a WhatsApp or Instagram DM in one click.
- **Price lists:** edit every set price, add sets, and hide a texture or format.
- **Photos:** upload and caption Queen Cam photos, then choose which ones show.
- **Settings and team (owner only):** Instagram and WhatsApp contacts, the announcement strip, pausing pre-orders, and staff accounts.

## Local development

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate --seed
npm run build   # or: npm run dev
php artisan serve --port=8134
```

Running the local seed creates two demo accounts (`owner@kinksandqueens.test` and `staff@kinksandqueens.test`, both with the password `password123`) and some sample requests. Production installs only get the catalogue.

```bash
php artisan test                   # feature tests
node scripts/screens.mjs dist/s    # phone and desktop screenshots plus an overflow check (needs the dev server)
```

## Deploying to Hostinger

See [INSTALL-HOSTINGER.md](INSTALL-HOSTINGER.md). In short, run `scripts/package-hostinger.sh`, upload `dist/kinksandqueens-hostinger.zip` to `public_html/`, extract it, fill in `kq-app/.env`, then open `/setup.php?token=…` once.
