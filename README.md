# 🧑‍💻 Freelancer Tracker

Freelancer Tracker API is a Laravel-based application designed to help freelancers manage clients, projects, time logs, and daily work summaries. This API-first application supports user authentication using Laravel Sanctum and includes time-based reporting features.

## 🚀 Features

- User registration & login 
- Client & Project management
- Time logging with tags (billable/non-billable), billable if more than 8 hours.
- Grouped log views (daily/weekly)
- PDF export of time logs
- Email notifications when 8+ hours are logged per day
- API Resource responses
- User-based authorization
- Validation and error messages for all forms

---

## 🛠️ Tech Stack

- PHP 8.x
- Laravel 10.x
- MySQL
- Sanctum for API Auth
- Mailtrap for email testing
- Laravel Eloquent API Resources

---

## ⚙️ Setup Instructions

1. Clone the Repository

git clone git@github.com:shantana1234/freelancer-tracker-api.git <br>
cd freelancer-tracker

2. Install Dependencies
composer install

3. Configure Environment
Copy the .env.example file and set up your environment variables
cp .env.example .env

4. Then generate your application key:
php artisan key:generate

5. Update your .env with:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=freelancer_tracker
DB_USERNAME=root
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@freelancetracker.com"
MAIL_FROM_NAME="Freelancer Tracker"

6. Set Up the Database
Create the MySQL database named freelancer_tracker and run migrations:

php artisan migrate
seed test data:
php artisan db:seed

7. Serve the Application
php artisan serve

By default, the app runs on: http://127.0.0.1:8000

📬 Testing Emails

Sign up at https://mailtrap.io and use your credentials in .env. Emails (notifications after 8+ hours logged) will appear in your Mailtrap inbox.

## ⚙️ API Routes

POST /api/register

POST /api/login

POST /api/logout (auth required)

GET /api/profile (auth required)

apiResource /clients (auth required)

apiResource /projects (auth required)

apiResource /time-logs (auth required)

GET /api/time-logs/grouped?from=...&to=...&group_by=day/week

GET /api/export/pdf (auth required)


## ⚙️ API Endpoint and Details [Postman collection] 

    https://shantana-4364985.postman.co/workspace/Shantana's-Workspace~7d487738-7a1e-4e58-82ac-feec9d7c0bcc/collection/45205524-e77a0135-e296-49ee-bcc9-f7761a84998b?action=share&creator=45205524 

postman collection JSON file is also included in the file. 


## 📁 Folder Highlights

app/Http/Controllers – Controllers for Users, Clients, Projects, TimeLogs

app/Http/Resources – API resource transformers

app/Models – Eloquent Models

app/Notifications – Email notifications

resources/views/pdf – Blade view used for PDF generation

##  ✅ Tips

Use Postman to test API endpoints.

Protect routes using auth:sanctum middleware.

Keep .env secure – never commit it to Git.

