<?php 
    require_once("config.php");
?>


<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $pdo = get_pdo();

        $stmt = $pdo->prepare("Select Email From User where Email = ?");

        $stmt->execute([$_POST['Email']]);

        $Email_row = $stmt->fetch(PDO::FETCH_ASSOC);

        $DB_Email = $Email_row['Email'];

        if (!filter_input(INPUT_POST, "Email", FILTER_VALIDATE_EMAIL)) {
            echo ("<p style='color: red;'>Email Invalid</p>");
        } elseif ($_POST['Email'] === $DB_Email) {
            echo ("<p style='color: red;'>Email already exists</p>");
        }elseif (strlen($_POST['Password']) < 8) {
            echo("<p style='color: red;'>Password is too small, must be at least 8 characters long</p>"); 
        }elseif(preg_match("/[!@#$%^&*()<>?]/", $_POST['Password']) === 0){
                echo("<p style='color: red;'> Password must contain a special character from this list: \"!@#$%^&*()<>?\"");
        }else {

        $hashedPW = password_hash($_POST['Password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("Call AddUser(?,?,?,?,?)");
        $stmt->execute([
            $_POST['FName'],
            $_POST['LName'],
            $_POST['Email'],
            $hashedPW,
            $_POST['PhoneNum']
        ]);

        header("Location: login.php");
        echo "<p>Sigup successful. Please login to start</p>";
       }

    } 
     catch (PDOException $e) {
     echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    }
}
?>

<html>
<body>
    <?php include 'main.php';?>
    <div class="container-fluid">
        <div class="row h-100">
    <!--This is the left side (image) -->    
    <div class="col-md-6 left-side d-flex align-items-end">
    <div class="overlay-text">
            <h1>Create An Account<h1>
                <p> Join and start exploring games<p>
</div>
</div>

<div class="col-md-6  right-side d-flex justify-content-center align-items-center">
    <div class="signup-card">
    <form method="POST">
    <!--FNAME-->    
    <div class="mb-3">  
        <input type="text" class="form-control neon-input"  id ="FName" name="FName" placeholder="First Name" aria-describedby="emailHelp" required>
    </div>

    <!--LNAME-->
    <div class="mb-3">
        <input type="text" class="form-control neon-input" id="LName" name="LName" placeholder="Last Name" aria-describedby="emailHelp" required>
    </div>

    
    <!--EMAIL-->    
    <div class="mb-3">
        <input type="email" class="form-control neon-input" id="Email" name="Email" placeholder="Email"
        aria-describedby="emailHelp" required>
    </div>

    <!--PASSWORD-->
    <div class="mb-3">
        <input type="password" class="form-control neon-input" id="Password" name="Password" placeholder="Password" required>
    </div>

    <!--Phone number-->
    <div class="mb-3">
        <input type="tel" class="form-control neon-input" id="PhoneNum" name="PhoneNum" 
        placeholder="xxxxxxxxxx" aria-describedby="emailHelp" required>
    </div>

    <button type="submit" class="btn btn-custom w-100">Sign up</button>
    </form>
</div>
</div>
</div>
</div>
</body>
</html>

