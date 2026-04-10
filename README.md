# Clothing Store Inventory and Sales System

**COMP 2154 – System Development Project**
Parth Kasodariya | Student ID: 101559010 | April 2026

---

## What This Is

A web-based system for managing inventory and sales at a small clothing store. Staff can add, edit, and delete products and view sales reports. Customers can browse products, add items to a cart, and checkout.

Built with PHP, MySQL, and HTML/CSS. No frameworks or external libraries needed.

---

## Features

- User registration and login (customers and staff roles)
- Product browsing with search
- Shopping cart (session-based)
- Checkout with automatic stock updates (uses MySQL transaction)
- Staff dashboard – add, edit, delete products, see low-stock warnings
- Sales report – total revenue, top products, recent orders

---

## Tech Stack

| Layer    | Technology     |
|----------|---------------|
| Backend  | PHP (PDO)     |
| Database | MySQL         |
| Frontend | HTML/CSS      |
| Server   | XAMPP (local) |

---

## Setup Instructions

### Requirements
- XAMPP installed (Apache + MySQL + PHP)

### Steps

**1. Clone the repo into your htdocs folder**
```
git clone https://github.com/Parth8818/clothing-store-comp2154
```

**2. Start XAMPP**

Open XAMPP Control Panel and start Apache and MySQL.

**3. Set up the database**

- Go to http://localhost/phpmyadmin
- Click "New" and create a database called `clothing_store`
- Select it, click "Import", and import the `database.sql` file from this repo

**4. Fix the passwords**

The sample user passwords in database.sql are placeholders. Run this once in your browser to set real ones:

```
http://localhost/clothing-store-comp2154/generate-passwords.php
```

This will set the passwords and confirm it worked. Delete the file after.

**5. Open the site**

```
http://localhost/clothing-store-comp2154
```

---

## Login Details (after running generate-passwords.php)

| Role     | Email               | Password      |
|----------|---------------------|---------------|
| Staff    | admin@test.com      | admin123      |
| Customer | customer@test.com   | customer123   |

---

## File Structure

```
clothing-store-comp2154/
├── config.php            ← database connection
├── database.sql          ← DB schema and sample data
├── generate-passwords.php ← run once to fix sample passwords
├── index.php             ← home page
├── login.php             ← login
├── register.php          ← customer registration
├── logout.php            ← clears session
├── products.php          ← product listing with search
├── add-to-cart.php       ← handles add to cart POST
├── cart.php              ← view and update cart
├── checkout.php          ← confirm and place order
├── orders.php            ← customer order history
├── staff.php             ← staff dashboard (add/delete products)
├── edit-product.php      ← edit an existing product
└── report.php            ← sales report (staff only)
```

---

## Notes

- The system uses a MySQL transaction in checkout.php so that stock is only reduced if the order is successfully created. If anything goes wrong, both the order and stock changes are rolled back.
- Passwords are hashed using PHP's `password_hash()` function.
- All user input is either validated server-side or escaped with `htmlspecialchars()` before display.
- Prepared statements are used for all database queries.
