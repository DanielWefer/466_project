<?php
session_start();
require '../db_connect.php';

$error = "";

// Handle Login
if(isset($_POST['login'])){
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM Employee WHERE Name = ? AND Password = ?");
    $stmt->execute([$name, $password]);

    $emp = $stmt->fetch(PDO::FETCH_ASSOC);

    if($emp){
        $_SESSION['emp_id'] = $emp['EmpID'];
        $_SESSION['emp_name'] = $emp['Name'];
        
        header("Location: empInventory.php");
        exit();
    } else {
        $error = "Invalid employee login.";
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Meatball Store</title>
    <link rel="stylesheet" href="../style.css">
  </head>
  <body>
    <header>
      <div>
        <h1>Meatball Mall</h1>
        <p>Satisfying all your meatball needs since yesterday.</p>
      </div>
      <nav>
        <ul>
          <!-- IMPORTANT: use PHP pages -->
          <li><a href="home.php"><b>Home</b></a></li>
          <li><a href="login.php"><b>Login</b></a></li>
          <li><a href="cart.php"><b>Cart</b></a></li>
          <li><a href="order.php"><b>Orders</b></a></li>
        </ul>
      </nav>

      <?php if (!empty($_SESSION['emp_name'])): ?>
        <div class="user-info"><?= htmlspecialchars($_SESSION['emp_name']) ?></div>
      <?php endif; ?>
    </header>

    <main>
      <h1>Employee Login</h1>

      <!-- ERROR MESSAGE -->
      <?php if(!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <!-- LOGIN FORM -->
      <form method="POST">
        <label>Employee Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit" name="login">Login</button>
      </form>

    </main>

    <footer>
      <ul>
        <li><a href="empLogin.php"><b>Employee Login</b></a></li>
        <li><a href="empInventory.php"><b>Inventory Management</b></a></li>
        <li><a href="empOrder.php"><b>Order Fulfillment</b></a></li>
      </ul>
    </footer>
  </body>
</html>
