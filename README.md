# Tele Guard

Tele Guard (project name `TeleGroupBot`) is a Laravel application that automates and moderates Telegram groups, for community managers, agencies, and developers who run groups or want to offer group automation as a service.

## Overview

The bot handles the routine work of running a Telegram community: filtering spam, censoring keywords, managing service messages, restricting new members, scheduling announcements, and reporting on member activity.

Alongside moderation it includes the pieces needed to run the bot as a product: a web dashboard, multilingual content, subscription plans, QR-code generation, an update system, and integrations with a range of payment gateways. Real-time events go through Pusher, queues through Redis/Predis, and storage can be backed by AWS S3.

## Features

- Group management automation for routine admin tasks
- Message filtering
- Keyword monitoring and censoring
- Service-message control (user joined, user left, and similar)
- Restrictions for new members
- Scheduled messages for announcements and reminders
- Member activity reporting
- Ban and mute controls
- Multilingual support with built-in translation management
- Subscriptions and payments across multiple gateways
- Webhook-driven processing of Telegram updates

## Requirements

- PHP 7.3 or later (8.x recommended) with Composer
- MySQL 5.7 or later
- Node.js and npm for frontend assets
- A Telegram bot token from [@BotFather](https://t.me/BotFather)

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/morpheusadam/TeleGuard.git
cd laravel-management-telegram-bot

# 2. Install dependencies
composer install
npm install

# 3. Create and configure your environment
cp .env.example .env
php artisan key:generate

# 4. Run migrations (and optionally seed)
php artisan migrate
php artisan db:seed   # optional

# 5. Build assets
npm run dev
```

Start the server and open `http://localhost:8000`:

```bash
php artisan serve
```

Set the Telegram bot token, database, Pusher, and payment gateway credentials in `.env`, then point the bot's webhook at the application's webhook endpoint.

## Tech stack

| Layer | Technologies |
| --- | --- |
| Backend | Laravel 8, PHP 7.3 / 8.x, Laravel Sanctum, Tinker |
| Messaging | Telegram Bot API (webhook), Pusher |
| Queue and cache | Redis / Predis |
| Storage | AWS SDK, Flysystem S3 |
| Frontend | Blade, Tailwind CSS, Alpine.js, Laravel Mix, SweetAlert2 |
| Payments | PayPal, Stripe, Razorpay, Mollie, Flutterwave, Paystack, Mercado Pago, Xendit, Instamojo, Myfatoorah, Senangpay, Toyyibpay, Paymaya, Yoomoney |
| Utilities | Simple QrCode, LaravelCollective HTML, Joe Dixon Translation |

## Project structure

```text
laravel-management-telegram-bot/
├── app/
│   ├── Http/Controllers/   # Bot, Webhook, Dashboard, Subscription...
│   ├── Providers/Payment/  # PayPal, Stripe, Razorpay, Mollie, ...
│   ├── Models/             # Telegram_bot, Usage_log, User
│   ├── Events/             # ChatEventPusherTelegram / Whatsapp
│   └── Jobs/               # SendEmailJob
├── .github/workflows/      # CI (php.yml)
├── resources/              # Blade views & Tailwind assets
└── routes/                 # web, api & webhook routes
```

## Contributing

Fork the repository and submit a pull request, or open an [issue](https://github.com/morpheusadam/TeleGuard/issues) with ideas and bug reports.

## License

MIT. See the `LICENSE` file for details, or add one if it is missing.

## Author

Morpheus Adam — [GitHub](https://github.com/morpheusadam) · [sam.zeonic.me](https://sam.zeonic.me) · morpheusadam95@gmail.com
