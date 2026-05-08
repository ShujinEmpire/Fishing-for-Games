<?php
require_once("config.php");
require_once("auth.php");
redirect_if_logged_in();
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //echo("here 0<br>");
    $pdo = get_pdo();
    session_start();

    //echo("here -1 <br>");

    $EmailGiven = strtolower($_POST['email']);
    //echo("here -2 <br>");
    $PwGiven = $_POST['password'];

    //echo("here -3 <br>");
    
    try {
        //echo("here pre");
        $stmt = $pdo->prepare("SELECT `FName`, `LName`, `UID`, `Password`, U_Type FROM `User` WHERE `Email` = ?");
        //echo "here -4<br>";
    } catch (PDOException $e) {
        die("Prepare failed: " . $e->getMessage());
    }
    
    //echo("here -4 <br>");
    
    $stmt->execute([$EmailGiven]);

    //echo("here -5 <br>");

    $UserRow = $stmt->fetch(PDO::FETCH_ASSOC);

    //echo("here -6");

    if($UserRow) {
        if(password_verify($PwGiven, $UserRow['Password'])) {
            //    echo"Here 1";
            $_SESSION['Type'] = $UserRow['U_Type'];
            $_SESSION['user_id'] = $UserRow['UID'];
            $_SESSION['username'] = $UserRow['FName'] . " " . $UserRow['LName'];
            $_SESSION['email'] = $EmailGiven;
            header("Location: dashboard.php");
            exit();
        }else {
            echo("<p style='color: red;'> Password incorrect </p>");
          //  echo"here 2";
        }

    } else {
        echo("<p style='color: red;'>Email incorrect or not found</p>");
        //echo("here 3");
    }
}
?>

<?php include 'main.php';i
        //echo("here 4");
?>
 

<html>
    <link href="style.css" rel="stylesheet">
<body class="login-page"> 
    <?php include 'main.php';?>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-box">
        <h2 class="login-title">Login </h2>
        <p class="login-sub">
            Welcome Back! Please login to continue exploring games.</p>

<form method="POST">
<div class="mb-3 position-relative">
    <i class="bi bi-envelope input-icon"></i>

        <input type="email" class="form-control neon-input mb-3" id="input" name="email" placeholder="Email"
        aria-describedby="emailHelp" required>
    </div>
    
    <div class="mb-3 position-relative">
    <i class="bi bi-lock input-icon"></i>

    <input type="password" class="form-control neon-input mb-3" id="input" name="password" placeholder="Password">
</div>
    <button type="submit" class="login-btn">Log in</button>
</form>
    <p class="login-link text-center mt-3">
        Don't have an account?
        <a href="signup.php">Sign up here</a>
</div>
</div>
</body>
</html>
