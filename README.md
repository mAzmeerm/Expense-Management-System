# Expense Management System — Local Setup Guide

A PHP + MySQL web application for managing employee expense claims, department
budgets, expense categories, and employee accounts, with separate **Admin**
and **Employee (Staff)** dashboards.

This guide walks you through running the project on your own computer
(`localhost`) using **XAMPP** (recommended for Windows/Mac/Linux beginners).

---

## 1. What You Need

| Requirement | Notes |
|---|---|
| **XAMPP** (or WAMP/MAMP/LAMP) | Provides Apache, PHP, and MySQL/MariaDB together. Download: https://www.apachefriends.org |
| **PHP 8.0+** | Bundled with recent XAMPP versions. The login system uses `password_hash()`/`password_verify()` (bcrypt), available since PHP 5.5. |
| **MySQL / MariaDB** | Bundled with XAMPP. |
| A web browser | Chrome, Edge, Firefox, etc. |

You do **not** need Node.js or Composer — this project has no external
dependencies; it's plain PHP, HTML, CSS, and JavaScript.

---

## 2. Project Files

Your project folder is **`GroupProjectCSC264E`**, containing:

- PHP pages for Admin (`Admin*.php`) and Employee (`Employee*.php`)
- `dbconn.php` — database connection settings
- `function.php` — shared helper functions (login, alerts, password hashing, pagination)
- `login.php` / `loginprocess.php` / `logout.php` — authentication
- `style.css`, `script.js`, `logo.png`, and `Icon*.svg` — front-end assets
- `database.sql` — **database schema** (created for this guide — see Step 5;
  your original upload did not include a `.sql` file, so this was
  reconstructed from the tables/columns referenced in your PHP code)

---

## 3. Install & Start XAMPP

1. Download and install XAMPP from https://www.apachefriends.org
2. Open the **XAMPP Control Panel**.
3. Click **Start** next to both:
   - **Apache**
   - **MySQL**

   Both rows should turn green.

---

## 4. Copy the Project into htdocs

1. Locate your XAMPP installation folder:
   - Windows: `C:\xampp\htdocs`
   - macOS: `/Applications/XAMPP/htdocs`
   - Linux: `/opt/lampp/htdocs`
2. Copy the entire **`GroupProjectCSC264E`** folder into `htdocs`, so the path looks like:
   ```
   C:\xampp\htdocs\GroupProjectCSC264E\login.php
   ```

---

## 5. Create the Database

Your code expects a database called **`expensemanagementdb`** (set in `dbconn.php`).
No `.sql` file was included in your upload, so use the one generated for you,
**`database.sql`**, which creates all 5 tables your code uses
(`employee`, `department`, `expensecategory`, `budget`, `expenseclaim`)
plus a working Admin login.

**Using phpMyAdmin (easiest):**

1. Go to http://localhost/phpmyadmin
2. Click **New** (left sidebar) → name it `expensemanagementdb` → **Create**.
3. Select the new database, click the **Import** tab.
4. Click **Choose File**, select `database.sql`, then click **Go**.

**Or using the MySQL command line:**

```bash
mysql -u root -p < database.sql
```
(Press Enter with no password if you haven't set a MySQL root password.)

This creates the database, all tables, and a default Admin login:

| Field | Value |
|---|---|
| Email | `admin@company.com` |
| Password | `admin123` |
| Role | Admin |

⚠️ **Change this password immediately after your first login** (via the Admin
Profile page) since it's published in this guide.

---

## 6. Check the Database Connection Settings

Open `GroupProjectCSC264E/dbconn.php` and confirm it matches your local MySQL setup:

```php
$user = "root";
$pass = "";
$host = "localhost";
$dbname = "expensemanagementdb";
```

This is the **default for a fresh XAMPP install** (root user, no password), so
in most cases you won't need to change anything. If your MySQL has a
different username/password, update `$user` / `$pass` here.

---

## 7. Run the App

1. Make sure Apache and MySQL are still running in XAMPP.
2. Open your browser and go to:
   ```
   http://localhost/GroupProjectCSC264E/login.php
   ```
3. Log in as Admin using the credentials from Step 5, or:
   - Go to **Admin → Employee Management** to add real Staff/Admin accounts.
   - Go to **Admin → Department Management** to add departments.
   - Go to **Admin → Category Management** to add expense categories.
   - Go to **Admin → Budget Management** to allocate department budgets.
4. Log out and log back in as a **Staff** employee to test claim submission
   (**Employee → Submit Claim** and **My Claims**).

---

## 8. How Login & Roles Work

- The `employee` table has a `Role` column: `Admin` or `Staff`.
- On login, you select your role (radio button) — it must match the role
  stored in the database for that account, or login will fail.
- Passwords are stored as **bcrypt hashes**. If an old/plaintext password is
  ever found in the database, the system automatically upgrades it to a
  bcrypt hash the next time that user logs in successfully.
- An employee with `Status = 'Inactive'` cannot log in until an Admin
  reactivates the account (Admin → Employee Management).

---

## 9. Troubleshooting

| Problem | Likely Cause / Fix |
|---|---|
| Blank page or "Error: ..." from mysqli | MySQL isn't running, or `dbname` in `dbconn.php` doesn't match the database you created. |
| "Invalid email, password, or role" | Double-check the Role radio button matches the account's role in the `employee` table. |
| Page shows raw PHP code instead of rendering | You opened the file directly (e.g. `file:///...`) instead of through `http://localhost/...`. Always access pages via the Apache URL. |
| `Access denied for user 'root'@'localhost'` | Your MySQL root password isn't blank. Update `$pass` in `dbconn.php` to match it. |
| Icons/images broken | Make sure the whole `GroupProjectCSC264E` folder (including all `.svg`/`.png` files) was copied, not just the `.php` files. |
| 404 Not Found | Confirm the folder name in `htdocs` is exactly `GroupProjectCSC264E` and the URL matches it exactly (case-sensitive on Linux/macOS). |

---

## 10. Notes on This Guide

- `database.sql` was **reconstructed by inspecting the SQL queries inside your
  PHP files** (table names, column names, and status values), since the
  uploaded project did not contain a database export. Double-check it against
  your own database if you already have one set up, and adjust column types
  if your actual schema differs.
- This project does not use Composer, npm, or any framework — it's a classic
  procedural PHP app, so no `composer install` / `npm install` step is needed.
