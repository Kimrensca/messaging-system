
# 📬 Messaging System (COMP 440 Group Project)

A simple PHP-based messaging system with secure user authentication and SMS integration via Africa's Talking API. It supports user registration, login, password reset, internal messaging, and bulk SMS sending.

---

## 🚀 Features

- ✅ Secure user registration and login (with hashed passwords)
- 🔐 Forgot password and reset functionality
- 💬 Internal messaging between users
- 📲 Send SMS via [Africa's Talking](https://africastalking.com/)
- 👁️ Toggle show/hide password input fields
- 🔒 Protected routes using sessions
- 📦 Clean file structure (MVC-style)

---

## 📁 Folder Structure

```
project-root/
├── vendor/              # Composer dependencies
├── .env                 # Environment variables (ignored in Git)
├── config.php           # DB & Africa's Talking config
├── register.php         # User registration logic
├── login.php            # Login logic
├── logout.php           # Logout logic
├── forgot_password.php  # Email input form for reset
├── reset_password.php   # Password reset form using token
├── dashboard.php        # User dashboard
├── send_sms.php         # Send SMS using Africa's Talking
├── export_csv.php       # Export users to CSV
├── profile.php          # User profile page
├── test_at.php          # Test Africa's Talking integration
├── composer.json        # Composer config
├── composer.lock        # Composer lock file
└── ...
```

---

## ⚙️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Kimrensca/messaging-system.git
   cd messaging-system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Set up the `.env` file**  
   Create a `.env` file and add the following:
   ```
   DB_HOST=localhost
   DB_NAME=your_database
   DB_USER=your_username
   DB_PASS=your_password

   AT_USERNAME=sandbox
   AT_API_KEY=your_api_key
   ```

4. **Set up your database**
   - Import the `users` table structure into your MySQL database.
   - Make sure it includes `reset_token` and `reset_expires` columns.

5. **Run a local development server**
   ```bash
   php -S localhost:8000
   ```

---

## ✅ Usage

- Go to `/register.php` to create an account.
- Log in via `/login.php`.
- Visit `/dashboard.php` to manage your account and send messages/SMS.
- Use `/export_csv.php` to download registered user data.

---

## 👨‍💻 Contributors

- Kim Rensca  
- Faith  
- Steph  
- Rev John  
- Duncan  

---

## 📌 Notes

- Sandbox SMS might fail if the recipient number isn’t approved in your sandbox environment.
- For production use, switch to **live mode** on Africa’s Talking and use verified numbers and sender IDs.
- Never push your `.env` file or database credentials to a public repository.

---

## 🌍 Africa’s Talking Portal

Visit [Africa’s Talking Developer Portal](https://africastalking.com/) to:

- Sign up
- View documentation
- Get your API Key (`Settings → Generate API Key`)

**Default Dev Credentials**:
- `username`: `sandbox`
- `apiKey`: Your generated key

---

## 📜 License

© 2025 COMP 440 Group Project — All rights reserved.
