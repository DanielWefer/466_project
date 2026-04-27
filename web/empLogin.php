<?php
require '../db_connect.php';

$error = "";

// Handle Login
if (isset($_POST['login'])) {
    $name = $_POST['name'];

    $stmt = $pdo->prepare("SELECT * FROM Employee WHERE Name = ?");
    $stmt->execute([$name]);

    $emp = $stmt->fetch();

    if ($emp) {
        // redirect to admin page (or wherever your employee dashboard is)
        header("Location: admin.php?empID=" . $emp['EmpID']);
        exit();
    } else {
        $error = "Invalid employee name.";
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
    </header>

    <main>
      <h1>Employee Login</h1>

      <!-- ERROR MESSAGE -->
      <?php
      if ($error != "") {
          echo "<p style='color:red;'>$error</p>";
      }
      ?>

      <!-- LOGIN FORM -->
      <form method="POST">
        <label>Employee Name:</label><br>
        <input type="text" name="name" required><br><br>

        <button type="submit" name="login">Login</button>
      </form>

    </main>

    <footer>
      <ul>
        <li><a href="empLogin.php"><b>Employee Login</b></a></li>
      </ul>
    </footer>
  </body>
</html>