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
- `database.sql` — your actual database export (5 tables, with real sample
  data already in it: 11 employees, 6 departments, 4 categories, 6 budgets,
  and 60 expense claims)

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
Use the **`database.sql`** file you exported from your own phpMyAdmin — it
already contains all 5 tables (`employee`, `department`, `expensecategory`,
`budget`, `expenseclaim`) plus your real sample data.

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

Once imported, you can log in with any existing account from the `employee`
table, for example:

| Email | Role | Status |
|---|---|---|
| `azmeer@sportzone.com` | Admin | Active |
| `noorasyraf@sportzone.com` | Admin | Active |

 **Six accounts still have plaintext passwords** in this export (`aiman123`,
`faris123`, `hakimi123`, `daniel123`, `amirul123`, `iqbal123` — for
`aimanhakim@sportzone.com`, `faris@sportzone.com`, `hakimi@sportzone.com`,
`daniel@sportzone.com`, `amirul@sportzone.com`, and `iqbal@sportzone.com`
respectively). Your code's `is_hashed()` check handles this automatically —
the first successful login for each of these accounts will silently upgrade
their password to a bcrypt hash. You don't need to do anything, but don't
share this README with those exact passwords still in it once your project
goes anywhere public (e.g. GitHub).

 **`faris@sportzone.com` has `Status = 'Inactive'`** and won't be able to
log in until an Admin reactivates the account (Admin → Employee Management).

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

- `database.sql` is your **actual phpMyAdmin export** — not reconstructed —
  so it matches your real database exactly, including all current sample data.
-  Every table in this export uses `CHARSET=eucjpms` (a Japanese-specific
  text encoding), which is unusual for a project with no Japanese text and
  was likely just phpMyAdmin's default charset setting at export time rather
  than something chosen on purpose. It works fine for plain English/Malay
  text, but if you ever see garbled characters, change each table's charset
  to `utf8mb4` instead (phpMyAdmin: select table → **Operations** → Table
  options → Collation).
- This project does not use Composer, npm, or any framework — it's a classic
  procedural PHP app, so no `composer install` / `npm install` step is needed.
