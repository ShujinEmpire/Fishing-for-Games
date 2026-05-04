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
        $stmt = $pdo->prepare("SELECT `FName`, `LName`, `UID`, `Password` FROM `User` WHERE `Email` = ?");
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
<body>
    <form method="POST">
    <div class="mb-3">
        <label for="input" class="form-label">Email address</label>
        <input type="email" class="form-control" id="input" aria-describedby="emailHelp" name = "email">
    </div>
    <div class="mb-3">
        <label for="input" class="form-label">Password</label>
        <input type="password" class="form-control" id="input" name = "password">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
    <p class="mt-3">
        Don't have an account?
        <a href="signup.php">Sign up here</a>
    </p>

    </form>
</body>
</html>
