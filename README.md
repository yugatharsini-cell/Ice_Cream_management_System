# Ice Cream Shop E-Commerce Management System

## Project Title

**Ice Cream Shop E-Commerce Management System**

## 1. Project Description

The Ice Cream Shop E-Commerce Management System is a web-based application developed to make ice cream shopping easier and more convenient for customers.

The system allows customers to register, log in, browse ice cream products, view product prices and stock availability, add products to a shopping cart, place orders, and make online payments through the **PayHere Sandbox payment gateway**.

The system also provides an administrator section for managing ice cream products, categories, prices, stock quantities, customer information, and customer orders.

The main purpose of this system is to provide a user-friendly online shopping platform while improving the efficiency of ice cream shop management and reducing manual work.

---

## 2. Problem Statement

Traditional ice cream shops may require customers to visit the physical shop to view available products, check prices, and make purchases. This can be inconvenient for customers who prefer online shopping.

Similarly, manually managing products, categories, stock quantities, customer information, and orders can be time-consuming for shop administrators and may lead to errors.

Therefore, an online ice cream shop management system is needed to help customers browse and purchase products conveniently while allowing administrators to manage shop operations efficiently.

---

## 3. Project Objectives

The main objectives of this project are:

- To develop a simple and user-friendly online ice cream shopping system.
- To provide customer registration, login, and logout functionality.
- To allow customers to browse available ice cream products.
- To display product information, prices, and stock quantities.
- To allow customers to add, update, and remove products from their shopping carts.
- To calculate shopping cart totals automatically.
- To allow customers to proceed through checkout and place orders.
- To integrate PayHere Sandbox for online payment testing.
- To verify payment notifications before confirming orders.
- To manage stock quantities based on successful payments.
- To allow customers to view their order details and order history.
- To provide administrators with product, category, stock, customer, and order management functionality.
- To reduce manual work and improve the efficiency of ice cream shop management.

---

## 4. Main Features

### 4.1 Customer Features

- Customer registration
- Customer login and logout
- Browse ice cream products
- View product information and prices
- Browse products by category
- View available stock quantities
- Add products to the shopping cart
- Update product quantities in the cart
- Remove products from the cart
- View cart totals
- Proceed to checkout
- Make payments through PayHere Sandbox
- View order details
- View order history
- Check order status

### 4.2 Administrator Features

- Administrator login
- Admin dashboard
- Add, edit, and delete ice cream products
- Manage product categories
- Manage product prices and images
- Update stock quantities
- View customer information
- View customer orders
- Manage customer orders
- Update order information and status

### 4.3 Stock Management

The system allows administrators to manage the available stock quantity of each ice cream product.

Customers can view stock availability before placing an order. The system validates product quantities against available stock during checkout.

After receiving and verifying a successful PayHere payment notification, the system updates the payment and order statuses and reduces the relevant stock quantities. Stock should not be reduced repeatedly when the same notification is received more than once.

### 4.4 Online Payment

The system integrates the PayHere Checkout API in **Sandbox mode**.

Customers are redirected to the PayHere Sandbox checkout page to test the payment process. Sandbox transactions are for testing purposes and do not charge real money.

---

## 5. Technologies and Tools Used

### Frontend

- HTML5
- CSS3
- JavaScript

### Backend

- PHP

### Database

- MySQL

### Development Environment

- XAMPP
- Apache
- MySQL
- phpMyAdmin

### Code Editor

- Visual Studio Code

### Version Control

- Git
- GitHub

### Web Browser

- Google Chrome
- Microsoft Edge

### Payment Gateway

- PayHere Checkout API
- PayHere Sandbox

---

## 6. Project Scope

The project focuses on online ice cream shopping and the basic management of an ice cream shop.

### 6.1 In Scope

- Customer registration and authentication
- Administrator authentication
- Product browsing and product information
- Product and category management
- Product image management
- Stock management
- Shopping cart management
- Checkout and order creation
- PayHere Sandbox payment integration
- Payment notification verification
- Payment and order status management
- Customer order history
- Customer information management
- Administrator order management

### 6.2 Out of Scope

The following features are outside the scope of the current project version:

- Delivery driver management
- Real-time delivery tracking
- Third-party delivery service integration
- Dedicated mobile application
- Advanced accounting functions
- Supplier management
- Multi-branch shop management
- Advanced product recommendation systems

---

## 7. Target Users

### 7.1 Customers

Customers use the system to:

- Create an account.
- Log in securely.
- Browse ice cream products.
- View product prices and stock availability.
- Manage shopping cart items.
- Proceed to checkout.
- Test online payments through PayHere Sandbox.
- View order details and order history.
- Check order status.

### 7.2 Administrator

The administrator uses the system to:

- Manage ice cream products.
- Manage product categories.
- Update product prices and images.
- Manage stock quantities.
- View customer information.
- View and manage customer orders.
- Monitor shop operations through the admin dashboard.

---

## 8. System Modules

### 8.1 User Management Module

- Customer registration
- Customer login and logout
- Administrator authentication
- Session-based authentication
- Access control for customer and administrator pages

### 8.2 Product Management Module

- Add new ice cream products
- Edit product information
- Delete products
- Manage categories
- Manage prices and product images
- Display product availability

### 8.3 Stock Management Module

- Add and update stock quantities
- Display available stock
- Validate stock during checkout
- Prevent orders that exceed available stock
- Reduce stock after verified successful payment
- Prevent duplicate stock deductions for repeated payment notifications

### 8.4 Shopping Cart Module

- Add products to the cart
- Update product quantities
- Remove products
- Display cart items
- Calculate the total amount

### 8.5 Order Management Module

- Create orders during checkout
- Store order items and quantities
- View order details
- View order history
- Track order status
- Allow administrators to manage orders

### 8.6 Online Payment Module

- Validate checkout information
- Generate PayHere payment requests
- Redirect customers to PayHere Sandbox
- Receive server-to-server payment notifications
- Verify payment notification signatures
- Validate payment amount and currency
- Update payment status
- Confirm orders after verified successful payments

### 8.7 Administrator Module

- Access the admin dashboard
- Manage products and categories
- Manage stock
- View customers
- View and manage orders
- Monitor the overall system

---

## 9. Payment Method

The project uses **PayHere Sandbox Online Payment** for testing the online payment process.

The payment process follows these steps:

1. The customer logs in and adds products to the shopping cart.
2. The customer proceeds to checkout.
3. The system validates the order and available stock.
4. The system creates a pending order and its order items.
5. The customer is redirected to the PayHere Sandbox checkout page.
6. The customer completes the simulated payment.
7. PayHere sends a server-to-server notification to the configured notification endpoint.
8. The system verifies the notification signature, merchant details, payment amount, currency, and order information.
9. If the payment is verified as successful, the system updates the payment status and confirms the order.
10. The system reduces stock quantities once for the successful payment.

**Important:** The customer returning to the website is not, by itself, proof of successful payment. The order must be confirmed based on a verified payment notification.

Sandbox payments are for testing and do not represent real-money transactions.

---

## 10. Database

The project uses **MySQL** as its database management system.

The database is named:

`ice_cream_shop`

The database stores information related to:

- Users
- Product categories
- Ice cream products
- Stock quantities
- Orders
- Order items
- Payments

The shopping cart is maintained using the customer's session.

The database can be managed through phpMyAdmin in XAMPP.

The database SQL file should be included in the project root as:

`database.sql`

If the actual SQL file has a different filename, the documentation should be updated to match it.

---

## 11. Project Requirements and Local Installation

### 11.1 Required Software

- XAMPP
- Visual Studio Code
- A web browser
- PHP
- Apache
- MySQL
- phpMyAdmin
- Git (optional, for version control)

### 11.2 Installation Steps

1. Install and open XAMPP.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Place the project folder inside the XAMPP `htdocs` directory.
4. Ensure the project folder is named `ice_cream_shop`.
5. Import the project database into MySQL using phpMyAdmin.
6. Configure the database connection in `config/db.php`.
7. Configure PayHere Sandbox settings in `config/payhere.php`.
8. Open the project in a web browser.

### 11.3 Project Directory

```text
C:\xampp\htdocs\ice_cream_shop\
```

### 11.4 Local Website URL

```text
http://localhost/ice_cream_shop/
```

The project runs locally when Apache and MySQL are running and the database connection is configured correctly.

---

## 12. PayHere Sandbox Configuration

The project uses the PayHere Checkout API in Sandbox mode for academic payment testing.

### 12.1 Configuration Steps

1. Create or access a PayHere Sandbox merchant account.
2. Obtain the appropriate Sandbox Merchant ID and Merchant Secret.
3. Open `config/payhere.php`.
4. Configure the Sandbox Merchant ID and Merchant Secret.
5. Set the checkout URL to the PayHere Sandbox checkout endpoint.
6. Configure the return and cancellation URLs.
7. Configure a publicly accessible HTTPS notification URL that points to `payment_notify.php`.
8. Configure the integration domain or application according to the PayHere Sandbox requirements.
9. Start Apache and MySQL.
10. Test the checkout and payment notification process using the appropriate Sandbox test details.

### 12.2 Important Configuration Notes

- Keep Sandbox credentials private.
- Do not commit the Merchant Secret to a public GitHub repository.
- Do not publish real payment credentials in source code or screenshots.
- A normal `localhost` URL cannot receive server-to-server notifications directly from PayHere.
- During local testing, a valid public HTTPS tunnel can expose the local notification endpoint.
- A tunnel URL may change or stop working. Update the configured notification URL whenever necessary.
- The return URL and cancellation URL must match the intended local or hosted environment.
- The PayHere checkout URL must remain the official Sandbox checkout endpoint when testing in Sandbox mode.
- Successful payment handling must rely on verified notifications rather than trusting browser redirects alone.

Example local return and cancellation URLs:

```text
http://localhost/ice_cream_shop/payment_return.php
http://localhost/ice_cream_shop/payment_cancel.php
```

Example notification URL format for local testing through a public HTTPS tunnel:

```text
https://YOUR-ACTIVE-PUBLIC-URL/ice_cream_shop/payment_notify.php
```

Replace `YOUR-ACTIVE-PUBLIC-URL` with the actual working HTTPS URL. Do not use the example as a real endpoint.

### 12.3 Card Information

The project does not need to store customers' card numbers, expiry dates, or CVV values. Payment card details should be entered on the PayHere-hosted checkout page.

---

## 13. Project Documentation

The repository may contain the academic and technical documents prepared during project development, including:

- Project Proposal
- Requirements Baseline
- Requirements Documentation
- Software Requirements Specification (SRS)
- Project Planning Documents
- Software Change Management Documentation
- Change Request Documents
- Testing Documentation

Only documents actually included in the repository should be listed as available files.

---

## 14. Project Purpose

The purpose of this project is to demonstrate the design and development of a web-based Ice Cream Shop E-Commerce Management System using frontend technologies, PHP, MySQL, and an online payment gateway.

The system aims to make ice cream shopping more convenient for customers while helping administrators manage products, categories, stock quantities, customer information, payments, and orders.

The PayHere Sandbox integration demonstrates the online payment workflow in a controlled testing environment without charging real money.

---

## 15. Student Details

**Student Name:** Ugatharsini Yoganathan

**Student ID:** JFICT24_33

**Project Title:** Ice Cream Shop E-Commerce Management System

**Project Type:** Individual Academic Project

**Course / Programme:** HND in Information Technology

**Institution:** University College of Jaffna

**Academic Year:** 2026

---

## 16. Project Status

**Project Status:** Academic Project – Development and Testing

The repository contains the project source code, database-related files, documentation, and other relevant project materials.

The completed features and payment integration should be verified through functional testing before the project is described as fully tested or complete.

---

## 17. Author

**Ugatharsini Yoganathan**

Ice Cream Shop E-Commerce Management System
