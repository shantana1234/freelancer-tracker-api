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

&nbsp;&nbsp;&nbsp;DB_CONNECTION=mysql<br>
&nbsp;&nbsp;&nbsp;DB_HOST=127.0.0.1<br>
&nbsp;&nbsp;&nbsp;DB_PORT=3306<br>
&nbsp;&nbsp;&nbsp;DB_DATABASE=freelancer_tracker<br>
&nbsp;&nbsp;&nbsp;DB_USERNAME=root<br>
&nbsp;&nbsp;&nbsp;DB_PASSWORD=your_password<br>

<br>
&nbsp;&nbsp;&nbsp;MAIL_MAILER=smtp<br>
&nbsp;&nbsp;&nbsp;MAIL_HOST=sandbox.smtp.mailtrap.io<br>
&nbsp;&nbsp;&nbsp;MAIL_PORT=2525<br>
&nbsp;&nbsp;&nbsp;MAIL_USERNAME=your_mailtrap_username<br>
&nbsp;&nbsp;&nbsp;MAIL_PASSWORD=your_mailtrap_password<br>
&nbsp;&nbsp;&nbsp;MAIL_ENCRYPTION=null<br>
&nbsp;&nbsp;&nbsp;MAIL_FROM_ADDRESS="noreply@freelancetracker.com"<br>
&nbsp;&nbsp;&nbsp;MAIL_FROM_NAME="Freelancer Tracker"<br>

6. Set Up the Database
Create the MySQL database named freelancer_tracker and run migrations:

php artisan migrate<br>

seed test data:<br>
php artisan db:seed<br>

7. Serve the Application
php artisan serve

By default, the app runs on: http://127.0.0.1:8000

📬 Testing Emails

Sign up at https://mailtrap.io and use your credentials in .env. Emails (notifications after 8+ hours logged) will appear in your Mailtrap inbox.

## ⚙️ API Routes

POST /api/register <br>
POST /api/login<br>
POST /api/logout (auth required)<br>
GET /api/profile (auth required)<br>
apiResource /clients (auth required)<br>
apiResource /projects (auth required)<br>
apiResource /time-logs (auth required)<br>
GET /api/time-logs/grouped?from=...&to=...&group_by=day/week<br>
GET /api/export/pdf (auth required)<br>


## ⚙️ API Endpoint and Details [Postman collection] 

    https://shantana-4364985.postman.co/workspace/Shantana's-Workspace~7d487738-7a1e-4e58-82ac-feec9d7c0bcc/collection/45205524-e77a0135-e296-49ee-bcc9-f7761a84998b?action=share&creator=45205524 

postman collection JSON file is also included in the file. 


## 📁 Folder Highlights

app/Http/Controllers – Controllers for Users, Clients, Projects, TimeLogs<br>

app/Http/Resources – API resource transformers<br>

app/Models – Eloquent Models<br>

app/Notifications – Email notifications<br>

resources/views/pdf – Blade view used for PDF generation<br>

##  ✅ Tips

Use Postman to test API endpoints.<br>

Protect routes using auth:sanctum middleware.<br>

Keep .env secure – never commit it to Git.<br>

