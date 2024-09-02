php artisan queue:work

dan set up mail di env atau set up mailtrap https://mailtrap.io/

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your username
MAIL_PASSWORD=your password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="your email"
MAIL_FROM_NAME="${APP_NAME}"
