````md
# KFC-Food-Ordering-and-Management-System

## System Description
KFC currently relies on paper menus, verbal ordering, and handwritten bills. This causes frequent order mistakes, long waits, and poor visibility of order status.

This web-based system replaces the manual flow with a digital experience:
- Customers browse a categorized menu, add to cart, place orders, pay online, and track status.
- Admins manage menus & categories, monitor live orders, and update statuses.
- Payments are handled via **Stripe Checkout (card-only: Visa, Mastercard, UnionPay)**.

---

## Project Modules
- **User Module**
- **Menu & Review Management**
- **Order Module**
- **Payment Module**
- **Admin Dashboard Module**

---

## Quick Start

### 1) Requirements
- PHP 8.2+
- XAMPP (Apache & MySQL)
- Composer

### 2) Install & Configure
```bash
# Install dependencies
composer install

# Copy env file, then set DB + Stripe keys (STRIPE_KEY / STRIPE_SECRET)
copy .env.example .env
php artisan key:generate

# Run migrations (seed if you have seeders)
php artisan migrate
# php artisan migrate --seed   # optional
php artisan db:seed
php artisan storage:link
````

### 3) Useful Artisan Commands

```bash
# Clear everything when things feel "stale"
php artisan optimize:clear

# If you change config/routes/views frequently
php artisan config:cache
php artisan route:clear
php artisan view:clear

# Rebuild DB from scratch (DANGEROUS: drops all tables)
# php artisan migrate:fresh --seed
```

### 4) Run the app

```bash
php artisan serve
```

---

## Login
**Admin:**

Email:admin@gmail.com

Password: 12345678

**Customer:**

Email:customer@gmail.com

Password: 12345678

---

---

## Payments (Stripe Checkout)

* Card rails enabled: **Visa, Mastercard, UnionPay**.
* We store only tokenized details (brand, last4, transaction ref), **not** PAN/CVV.
* Order amount is validated server-side from the `orders` table.
* Idempotency keys are used to prevent duplicate charges.

---

## Test Cards (Stripe Sandbox)

Use **any future expiry date** and **any 3-digit CVC** (Amex uses 4 digits).
Cards below come from Stripe’s docs:

* Test card list: [https://docs.stripe.com/testing?testing-method=card-numbers#cards](https://docs.stripe.com/testing?testing-method=card-numbers#cards)
* Stripe-hosted checkout guide (test cards): [https://docs.stripe.com/payments/accept-a-payment?platform=web\&ui=stripe-hosted#test-cards](https://docs.stripe.com/payments/accept-a-payment?platform=web&ui=stripe-hosted#test-cards)

### Visa

| Purpose                       | Number                | Notes                          |
| ----------------------------- | --------------------- | ------------------------------ |
| Success                       | `4242 4242 4242 4242` | Standard success               |
| 3D Secure required            | `4000 0000 0000 3220` | Triggers a 3DS challenge       |
| Declined (insufficient funds) | `4000 0000 0000 9995` | Simulates `insufficient_funds` |

### Mastercard

| Purpose            | Number                | Notes                    |
| ------------------ | --------------------- | ------------------------ |
| Success            | `5555 5555 5555 4444` | Standard success         |
| Success (2-series) | `2223 0031 2200 3222` | New Mastercard BIN range |
| Debit (success)    | `5200 8282 8282 8210` | Mastercard debit test    |

### UnionPay

| Purpose                 | Number                    | Notes                    |
| ----------------------- | ------------------------- | ------------------------ |
| Success (credit)        | `6200 0000 0000 0005`     | UnionPay credit          |
| Success (debit)         | `6200 0000 0000 0047`     | UnionPay debit           |
| Success (19-digit card) | `6205 5000 0000 0000 004` | 19-digit UnionPay number |

> **Note:** Always run in **test mode** with your Stripe **test** keys during development.
> Do **not** use real card numbers while testing.

