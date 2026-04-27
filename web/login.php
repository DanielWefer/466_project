<?php
session_start();
require '../db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['psw'] ?? '');

    try {
        $stmt = $pdo->prepare("SELECT UserID, Name, Email FROM Customer WHERE Email = ? AND Password = ?");
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['user_name'] = $user['Name'];
            $_SESSION['user_email'] = $user['Email'];
            header('Location: home.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
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
          <li><a href="home.php"><b>Home</b></a></li>
          <li><a href="login.php"><b>Login</b></a></li>
          <li><a href="cart.php"><b>Cart</b></a></li>
          <li><a href="order.php"><b>Orders</b></a></li>
        </ul>
      </nav>
    </header>
    <main>
      <?php if ($error): ?>
        <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
      <form action="login.php" method="post">
        <div class="imgcontainer">
          <img src="../meatballs/meatball1.png" alt="Spinning Meatball" class="spinning">
        </div>
        <div class="container">
          <label for="emaii"><b>Email</b></label>
          <input type="text" placeholder="Enter Email" name="email" required>

          <label for="psw"><b>Password</b></label>
          <input type="password" placeholder="Enter Password" name="psw" required>

          <button type="submit">Login</button>
        </div>
        <div class="container" style="background-color:#f1f1f1">
          <button type="button" class="cancelbtn" onclick="window.location.href='home.php'">Cancel</button>
        </div>
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
