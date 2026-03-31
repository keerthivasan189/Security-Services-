# Sai Saktheeswari Security Services — HRMS
## Full PHP + MySQL Human Resource Management System

---

## Requirements

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Apache 2.4 with mod_rewrite enabled
- Composer (for PDF/Excel libraries — optional)

---

## Quick Setup

### Step 1 — Install files
Copy the `hrms/` folder to your web server root:
```
/var/www/html/hrms/     (Linux/Apache)
C:\xampp\htdocs\hrms\   (XAMPP on Windows)
```

### Step 2 — Create Database
Open phpMyAdmin or MySQL CLI and run:
```sql
SOURCE /path/to/hrms/config/install.sql;
```
Or import `config/install.sql` via phpMyAdmin.

### Step 3 — Configure Database
Edit `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hrms_saktheeswari');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // your MySQL password
```

### Step 4 — Configure Base URL
Edit `index.php` line 4:
```php
define('BASE_URL', 'http://localhost/hrms');
```
Change `localhost` to your server's domain or IP if deploying online.

### Step 5 — Enable Apache mod_rewrite
Make sure `.htaccess` is being processed. In Apache config:
```apache
<Directory /var/www/html/hrms>
    AllowOverride All
</Directory>
```

### Step 6 — Set folder permissions (Linux)
```bash
chmod -R 755 /var/www/html/hrms/
chmod -R 777 /var/www/html/hrms/uploads/
```

### Step 7 — Login
Open browser: `http://localhost/hrms`

**Default credentials:**
- Username: `admin`
- Password: `password`

> Change the password immediately after first login via the database:
> ```sql
> UPDATE users SET password = '$2y$10$YOUR_BCRYPT_HASH' WHERE username = 'admin';
> ```
> Generate hash: `php -r "echo password_hash('newpassword', PASSWORD_DEFAULT);"`

---

## Optional: PDF & Excel Libraries (Composer)

Install Composer from https://getcomposer.org, then run in the hrms folder:
```bash
composer install
```

This installs:
- **TCPDF** — for invoice and payslip PDF generation
- **PhpSpreadsheet** — for Excel export of salary/attendance

---

## Module URLs

| Module              | URL                                    |
|---------------------|----------------------------------------|
| Dashboard           | /hrms/dashboard/index                  |
| Employee List       | /hrms/employees/index                  |
| Add Employee        | /hrms/employees/add                    |
| Client Master       | /hrms/clients/index                    |
| Attendance Bulk     | /hrms/attendance/bulk                  |
| Attendance Single   | /hrms/attendance/single                |
| View Attendance     | /hrms/attendance/view                  |
| Generate Salary     | /hrms/payments/generate                |
| Salary List         | /hrms/payments/salarylist              |
| Advances            | /hrms/payments/advances                |
| Fuel Expenses       | /hrms/payments/fuel                    |
| MISC Expenses       | /hrms/payments/misc                    |
| Other Allowances    | /hrms/payments/allowances              |
| Client Invoices     | /hrms/receipts/clientbills             |
| Create Invoice      | /hrms/receipts/addinvoice              |
| Issue Uniforms      | /hrms/receipts/uniforms                |
| Other Deductions    | /hrms/receipts/deductions              |
| Current Positions   | /hrms/positions/index                  |
| Appoint/Transfer    | /hrms/positions/appoint                |
| Transfer History    | /hrms/positions/history                |
| Accounts Statements | /hrms/accounts/statements              |
| Add Transaction     | /hrms/accounts/add                     |
| Ledger Accounts     | /hrms/accounts/ledgers                 |

---

## Project Structure

```
hrms/
├── index.php              ← Front controller / router
├── .htaccess              ← URL rewriting
├── composer.json          ← PHP dependencies
├── README.md
├── config/
│   ├── db.php             ← Database connection
│   └── install.sql        ← Full DB schema with seed data
├── controllers/
│   ├── BaseController.php
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── EmployeeController.php
│   ├── ClientController.php
│   ├── AttendanceController.php
│   ├── PaymentsController.php
│   ├── ReceiptsController.php
│   ├── AccountsController.php
│   └── PositionController.php
├── helpers/
│   ├── Session.php        ← Session management & auth
│   └── Helper.php         ← Utility functions (money, upload, redirect)
├── views/
│   ├── layout/
│   │   ├── header.php     ← Sidebar + topbar
│   │   └── footer.php     ← Scripts
│   ├── auth/login.php
│   ├── dashboard/index.php
│   ├── employees/         ← index, add, edit, view
│   ├── clients/           ← index, add, edit, trades
│   ├── attendance/        ← bulk, single, view
│   ├── payments/          ← generate, salarylist, payslip, advances, fuel, misc, allowances
│   ├── receipts/          ← clientbills, addinvoice, viewinvoice, receivepayment, uniforms, deductions
│   ├── positions/         ← index, appoint, history
│   └── accounts/          ← statements, add, ledgers
└── uploads/
    ├── fuel_bills/
    ├── misc_bills/
    ├── cheque_photos/
    └── deduction_files/
```

---

## Key Business Logic

- **Salary Calculation**: Basic wage ÷ days in month × days present. EPF = 12% of basic. ESI = 0.75% if gross ≤ ₹21,000.
- **Attendance Incentive**: ₹1,000 added if employee is present all working days.
- **Advances**: Deducted monthly from salary over the specified number of due months.
- **Uniform Dues**: Deducted monthly from salary if balance outstanding.
- **Invoice**: GST type or RCM type. Rate per hour × total hours per designation.
- **Double-entry Accounts**: Every transaction has a debit ledger and credit ledger.

---

## Security Notes

1. Change the default admin password immediately
2. Move `uploads/` folder outside the web root in production
3. Add HTTPS to your Apache config
4. Set `display_errors = Off` in php.ini for production
