# 🍝 Meatball Mall

An e-commerce web app for ordering the world's finest meatballs, with customer cart/checkout and employee inventory management. Satisfying all your meatball needs since yesterday.

---

## What's Sold

**MEATBALLS** — the one and only product. Tracked per unit with price and stock count.

---

# IMPORTANT: Create db_connect.php locally
```
<?php
// Database Connection File
// Used In All Other .php Files
// Through: require 'db_connect.php';
$host = "courses";
$db = "z1234567";
$user = "z1234567"; // MariaDB
$pass = "YYYYMmmDD"; // MariaDB

try{
	$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
	die("Connection failed: " . $e->getMessage());
}
?>
```
### Change db/user/pass to your Z-ID and Password
---

## Entities & Relationships

| Entity | Primary Key | Attributes | Relationships |
|---|---|---|---|
| Customer | Email | Name | Owns Cart, views Orders |
| Employee | ID | Name | Manages inventory, fulfills Orders |
| Product | ID | Price, Stock | Lives in Cart, updated by Employee |
| Cart | CartID | Items (via CartItem) | Owned by Customer, converts to Order |
| Orders | OrderNum | Price, Delivery Time, Billing Info, Shipping Info, Status, Tracking | Checked by Customer, fulfilled by Employee |

---

## Relational Schema

```
Employee  ( *EmpID, Name, Password )
Customer  ( *UserID, Name, Email, Password )
Product   ( *ProductID, Name, Price, Stock )
Orders    ( *OrderID, UserID†, EmpID†, ShippingAddr, BillingInfo, Status )
OrderItem ( *OrderID†, *ProductID†, Quantity )
Cart      ( *CartID, UserID† )
CartItem  ( *CartID†, *ProductID†, Quantity )
```

> **Key:** * = primary key &nbsp;·&nbsp; `†` = foreign key

---

## Pages & Navigation

| Page | Description | Redirect |
|---|---|---|
| **Main / Storefront** | Showcases products with live stock info | → Login (if unauthenticated, on add-to-cart) |
| **Login** | Shared login for customers and employees | → Main (customer) or Employee Panel (staff) |
| **Employee Panel** | Inventory management — add, edit, remove products and stock | Employee access only |
| **Cart** | Shows items in cart and running subtotal | → Checkout |
| **Checkout** | Enter billing and shipping information | → Orders (on successful checkout) |
| **Orders** | Order history, status, and grand total for a customer | Customer view |
| **Order Fulfillment** | Employees update order status and tracking numbers | Employee access only |

---

## User Roles

### Customer
- Browse products and stock levels
- Add items to cart
- Checkout with billing and shipping info
- View own orders and delivery status

### Employee
- Check and update product inventory
- View all pending orders
- Update order status and tracking
- Manage product listings

---

## Database Tables

| Table | Purpose |
|---|---|
| `Employee` | Staff accounts with credentials |
| `Customer` | Customer accounts with credentials |
| `Product` | Product catalog with pricing and stock |
| `Orders` | Placed orders with fulfillment details |
| `OrderItem` | Line items belonging to an order |
| `Cart` | Active shopping cart per customer |
| `CartItem` | Line items in a customer's cart |
