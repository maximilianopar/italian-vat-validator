# Italian VAT Numbers Validator

A simple PHP application that validates Italian VAT numbers from a CSV file or through a manual input form.

The application classifies VAT numbers into three groups:

- **Valid**
- **Corrected**
- **Invalid**

It also stores processed results in a MySQL database and displays the latest imported batch in a clear, user-friendly way.

---

## Features

- Upload a CSV file and process multiple VAT numbers at once
- Validate a single VAT number manually
- Automatically correct numeric VAT values without the `IT` prefix
- Reject invalid VAT numbers
- Store results in MySQL
- Group results by batch
- Display:
  - valid VAT numbers
  - corrected VAT numbers and what was modified
  - invalid VAT numbers and why they were rejected

---

## Validation Rules

A VAT number is considered valid if it:

- starts with `IT`
- is followed by **11 digits**

### Examples

- `IT12345678901` → valid
- `98765432158` → corrected to `IT98765432158`
- `IT12345` → invalid
- `123-hello` → invalid

---

## Project Structure

```text
project-root/
│
├── config/
│   └── config.php
│
├── database/
│   ├── schema.sql
│   └── seed.sql
│
├── public/
│   └── index.php
│
├── src/
│   ├── Config/
│   │   └── Database.php
│   ├── Controllers/
│   │   └── VatController.php
│   ├── Helpers/
│   │   └── CsvReader.php
│   ├── Models/
│   │   └── VatAnalysisResult.php
│   ├── Repositories/
│   │   └── VatRepository.php
│   └── Services/
│       └── VatValidationService.php
│
├── storage/
│   └── uploads/
│
└── views/
    ├── home.php
    └── partials/
        ├── header.php
        └── footer.php
```
---

## Requirements

- PHP 8.0 or higher
- MySQL
- XAMPP (recommended for this exercise)

---

## Installation

### 1. Clone or download the project

Place the project folder inside your XAMPP `htdocs` directory.

**Example:**
C:\xampp\htdocs\italian-vat-app

---

### 2. Start Apache and MySQL

Open XAMPP Control Panel and start:

- Apache
- MySQL

---

### 3. Create the database

Open phpMyAdmin and create a database named:

italian_vat_app

---

### 4. Import the database schema

Import the `schema.sql` file located in:
/database/schema.sql

This will create the required tables.

---

### 5. Import seed data (optional)

If you want sample data, import:
/database/seed.sql

This will insert an example batch and sample VAT results.

---

### 6. Configure the application

Open:

```text
/config/config.php
```

Make sure your database connection matches your local XAMPP setup.

**Example:**

```php
return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'dbname' => 'italian_vat_app',
        'username' => 'root',
        'password' => '',
    ],
    'app' => [
        'base_url' => '/italian-vat-app/public',
        'upload_dir' => __DIR__ . '/../storage/uploads',
        'max_upload_size' => 2097152,
    ],
];
```

---

### 7. Running the application

Open your browser and go to:
http://localhost/italian-vat-app/public/index.php

