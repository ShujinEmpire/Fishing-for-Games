<?php
require_once("config.php");
require_once("auth.php");
redirect_if_logged_in();


$flash_success = get_flash('flash_success');
$flash_error   = get_flash('flash_error');

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
           set_flash('flash_error', 'Incorrect password. Please try again.');
            header("Location: login.php");
            exit();
          //  echo"here 2";
        }

    } else {
        set_flash('flash_error', 'Email incorrect or not found.');
        header("Location: login.php");
        exit();
        //echo("here 3");
    }
}
?>

<?php include 'main.php';i
        //echo("here 4");
?>
 
<html>
    <link href="style.css" rel="stylesheet">
<body>
    <?php include 'main.php';?>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="signup-box row g-0">
    <!--This is the left side (image) -->
    <div class="col-md-6 left-side d-flex align-items-end">
    <div class="overlay-text">
            <h2>Welcome Back!</h2>
                <p>Sign in to your account</p>
</div>
</div>
<div class="col-md-6 right-side">
    <div class="auth-page-stack">

    <div class="auth-flash-wrap">
        <?php if ($flash_success): ?>
            <div class="auth-flash auth-flash-success">
                <?= htmlspecialchars($flash_success) ?>
            </div>
        <?php endif; ?>

        <?php if ($flash_error): ?>
            <div class="auth-flash auth-flash-error">
                <?= htmlspecialchars($flash_error) ?>
            </div>
        <?php endif; ?>   
    </div>
    
    <h2 class="text-center text-white mb-4 signup-title"> Sign IN </h2>
    <form method="POST">
  <div class="mb-3">
        <input type="email" class="form-control neon-input mb-3" id="input" name="email" placeholder="Email"
        aria-describedby="emailHelp" required>
    </div>
    <div class="mb-3">
        <input type="password" class="form-control neon-input mb-3" id="input" name="password" placeholder="Password">
        <div class="form-check text-white mb-3" required> 
        </div>
    <button type="submit" class="btn neon-btn w-100 mb-3">Log in</button>
    <p class="login-link text-center mt-3">
        Don't have an account?
        <a href="signup.php">Sign up here</a>
    </form>
</div>
</div>
</div>
</body>
</html>
