# Ice Cream Shop E-Commerce Management System

## Project Title

**Ice Cream Shop E-Commerce Management System**

---

## 1. Project Description

The Ice Cream Shop E-Commerce Management System is a web-based application developed to make ice cream shopping easier and more convenient for customers. The system allows customers to register, log in, browse available ice cream products, view product details, check available stock quantities, add products to the shopping cart, place orders, and complete payments through **PayHere Sandbox**.

The system also provides an administrative section where the administrator can manage ice cream products, categories, stock quantities, customers, and customer orders.

The main purpose of this system is to provide a simple online platform for purchasing ice cream products while reducing the manual work involved in managing products, stock, and customer orders.

---

## 2. Problem Statement

Traditional ice cream shops may require customers to physically visit the shop to view available products, check prices, and make purchases.

For shop administrators, manually managing products, categories, stock quantities, customers, and customer orders can be time-consuming and may lead to errors.

Therefore, there is a need for a simple online ice cream shop e-commerce system that allows customers to conveniently browse and purchase ice cream products while helping administrators manage products, stock, and orders efficiently.

---

## 3. Project Objectives

The main objectives of the Ice Cream Shop E-Commerce Management System are:

- To develop a simple and user-friendly online ice cream shopping system.
- To allow customers to register and securely log in to the system.
- To allow customers to browse available ice cream products.
- To allow customers to view product details and prices.
- To display available stock quantities to customers.
- To allow customers to add, update, and remove products from the shopping cart.
- To allow customers to place ice cream orders online.
- To provide an online payment facility.
- To allow customers to view their order details and order history.
- To allow administrators to manage products and categories.
- To allow administrators to manage stock quantities.
- To allow administrators to manage customer information and orders.
- To reduce manual work and improve the efficiency of ice cream shop management.

---

## 4. Main Features

### Customer Features

- Customer registration
- Customer login and logout
- Browse ice cream products
- View product details
- Browse products by category
- View product prices
- View available stock quantities
- Add products to cart
- Update cart quantity
- Remove products from cart
- View cart total
- Checkout
- PayHere Sandbox online payment
- Place orders
- View order details
- View order history
- View order status

### Administrator Features

- Administrator login
- Admin dashboard
- Add ice cream products
- Edit ice cream products
- Delete ice cream products
- Manage product categories
- Manage product prices
- Manage product images
- Manage stock quantities
- View customer information
- View customer orders
- Manage customer orders
- Update order information

### Stock Management

The administrator can manage the available stock quantity of each ice cream product.

Customers can view the available stock quantity before adding a product to the shopping cart.

The system checks stock before creating an order and reduces stock only after a verified successful PayHere payment notification.

### Payment Method

The system uses **PayHere Sandbox** for online payment testing. Sandbox payments are simulated for testing and do not charge real money.

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

### Browser

- Google Chrome / Microsoft Edge

### Payment Gateway

- PayHere Checkout API
- PayHere Sandbox

---

## 6. Project Scope

The system is designed to provide basic online ice cream shopping and ice cream shop management functions.

### In Scope

The system includes:

- Customer registration and login
- Administrator login
- Product browsing
- Product details
- Product category management
- Product management
- Product image management
- Stock quantity management
- Shopping cart management
- Checkout
- PayHere Sandbox online payment
- Payment status handling
- Order placement
- Order history
- Order management
- Customer management

### Out of Scope

The following features are not included in the current version:

- Delivery driver management
- Real-time delivery tracking
- Third-party delivery service integration
- Mobile application
- Advanced accounting functions
- Supplier management
- Multi-branch shop management
- Advanced recommendation system

---

## 7. Target Users

### Customers

The system is intended for customers who want to browse and purchase ice cream products online without visiting the physical shop.

Customers can:

- Create an account.
- Log in to the system.
- Browse ice cream products.
- View available stock.
- Add products to the shopping cart.
- Checkout.
- Make online payments through PayHere Sandbox.
- Place orders.
- View their order history.

### Administrator

The administrator manages the ice cream shop system, including:

- Products
- Categories
- Stock quantities
- Customers
- Customer orders
- Product information

---

## 8. System Modules

The system consists of the following main modules:

### 1. User Management

- Customer registration
- Customer login
- Customer logout
- Administrator login
- User authentication

### 2. Product Management

- Add ice cream products
- Edit products
- Delete products
- Manage product categories
- Manage product prices
- Manage product images
- View product details
- View product availability

### 3. Stock Management

- Add stock quantity
- Update stock quantity
- Display available stock
- Monitor product availability
- Prevent ordering unavailable products
- Reduce stock after successful payment

### 4. Shopping Cart

- Add products to cart
- Update product quantity
- Remove products
- View cart
- Calculate total amount

### 5. Order Management

- Place orders
- View order details
- View order history
- Manage customer orders
- Update order information

### 6. Online Payment

- Proceed to checkout
- Redirect customer to PayHere Sandbox
- Process simulated online payment
- Receive PayHere payment notification
- Verify payment notification checksum
- Update payment status
- Complete the order after successful payment

### 7. Administrator Management

- Manage customers
- Manage products
- Manage categories
- Manage stock
- Manage orders
- Monitor the overall system

---

## 9. Payment Method

The project uses **PayHere Sandbox Online Payment** as the payment method.

Customers proceed to checkout and are redirected to the PayHere Sandbox payment gateway. The payment is simulated for academic testing and no real money is charged.

The system receives the payment notification from PayHere and verifies the notification before marking the payment as successful and confirming the order.

---

## 10. Database

The project uses **MySQL** as the database management system.

The database stores information related to:

- Users
- Categories
- Products
- Stock quantities
- Orders
- Order items
- Payments

The shopping cart is maintained using the customer session.

The database is managed using **phpMyAdmin** through XAMPP.

The final database SQL file is included in the project root:

```text
database.sql
```

---

## 11. Project Requirements

To run the project locally, the following software is required:

- XAMPP
- Visual Studio Code
- Web browser
- MySQL
- PHP
- Apache
- phpMyAdmin

The project should be placed inside the XAMPP `htdocs` folder.

Example:

```text
C:\xampp\htdocs\ice_cream_shop\
```

Apache and MySQL should be running in the XAMPP Control Panel.

---

## 12. PayHere Sandbox Setup

The project uses the **PayHere Checkout API in Sandbox mode** for online payment testing.

Before testing payment:

1. Create or use a PayHere Sandbox merchant account.
2. Get the Sandbox Merchant ID and Merchant Secret from the PayHere merchant portal.
3. Open `config/payhere.php`.
4. Replace `YOUR_SANDBOX_MERCHANT_ID` with the Sandbox Merchant ID.
5. Replace `YOUR_SANDBOX_MERCHANT_SECRET` with the Sandbox Merchant Secret.
6. Set `notify_url` to a public URL that points to `payment_notify.php`.
7. Start Apache and MySQL in XAMPP.
8. Add the integration domain/app in the PayHere Sandbox merchant portal as required.
9. Test checkout using PayHere Sandbox test card details.

### Important

The PayHere notification URL must be publicly accessible. A normal `localhost` URL cannot receive PayHere server notifications directly.

The project does **not** store the customer's card number, expiry date, or CVV. Card information is entered only on the PayHere Sandbox payment page.

---

## 13. Project Documentation

The repository contains the relevant project documentation developed during the project, including:

- Project Proposal
- Requirements Baseline
- Requirements Documentation
- Software Requirements Specification (SRS)
- Project Planning Documents
- Change Management Documentation
- Change Request Documents
- Testing Documentation

---

## 14. Project Purpose

The purpose of this project is to demonstrate the development of a web-based **Ice Cream Shop E-Commerce Management System** using frontend, backend, database, and online payment technologies.

The system provides a simple platform for customers to browse and purchase ice cream products online while helping administrators manage products, stock quantities, categories, customers, and customer orders.

---

## 15. Student Details

**Student Name:** Ugatharsini Yoganathan

**Project:** Ice Cream Shop E-Commerce Management System

**Project Type:** Individual Academic Project

**Course / Programme:** HND in Information Technology

**Student ID:** JFICT24_33

**Institution:** University College of Jaffna

**Academic Year:** 2026

---

## 16. Project Status

**Project Status:** Final Project

This repository contains the source code, database-related files, documentation, and other relevant project materials for the **Ice Cream Shop E-Commerce Management System**.

---

## 17. Author

**Ugatharsini Yoganathan**

**Ice Cream Shop E-Commerce Management System**
