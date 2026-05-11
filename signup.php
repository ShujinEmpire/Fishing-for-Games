<?php 
    require_once("config.php");
?>


<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {

        if(!isset($_POST['terms'])) {
            echo("<p stye='color: red;'>Please check terms and conditions box to continue</p>");
        }

        $pdo = get_pdo();

        $stmt = $pdo->prepare("Select Email From User where Email = ?");

        $stmt->execute([$_POST['Email']]);
        
        $Email_row = $stmt->fetch(PDO::FETCH_ASSOC);

        $DB_Email = $Email_row['Email'];

        $LowerEmail = strtolower(trim($_POST['Email'] ?? ''));

        if (!filter_input(INPUT_POST, "Email", FILTER_VALIDATE_EMAIL)) {
            echo ("<p style='color: red;'>Email Invalid</p>");
        }elseif ($LowerEmail === $DB_Email) {
            echo ("<p style='color: red;'>Email already exists</p>");
        } elseif (strlen($_POST['Password']) < 8) {
            echo("<p style='color: red;'>Password is too small, must be at least 8 characters long</p>"); 
        }elseif(preg_match("/[!@#$%^&*()<>?]/", $_POST['Password']) === 0){
                echo("<p style='color: red;'> Password must contain at least one special character from this list: \"!@#$%^&*()<>?\"");
        }elseif(strlen($_POST['PhoneNum']) > 10 || strlen($_POST['PhoneNum']) < 10){
                echo("<p style='color: red;'> Your phone number needs to be 10 numbers long</p>");
        }else {

        $hashedPW = password_hash($_POST['Password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("Call AddUser(?,?,?,?,?)");
        $stmt->execute([
            $_POST['FName'],
            $_POST['LName'],
            $LowerEmail,
            $hashedPW,
            $_POST['PhoneNum']
        ]);

        header("Location: login.php");
        echo "<p style='color: red;'>Sigup successful. Please login to start</p>";
        exit();
       }

    } 
     catch (PDOException $e) {
     echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    }
}
?>


<html>
    <link href="style.css" rel="stylesheet">
    <link href= "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"  rel="stylesheet" > 
<body>
    <?php include 'main.php';?>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="signup-box row g-0">
    <!--This is the left side (image) -->
    <div class="col-md-6 left-side d-flex align-items-end">
    <div class="overlay-text">
            <h2>Create An Account</h2>
                <p>Join and start exploring games</p>
</div>
</div>
<div class="col-md-6 right-side">
    <h2 class="text-center text-white mb-4 signup-title"> Sign Up </h2>
    <form method="POST">
    <!--FNAME-->
        <div class="input-wrapper mb-3">
            <i class="fa-solid fa-id-card input-icon"></i>
        <input type="text" class="form-control neon-input mb-3"  id ="FName" name="FName" placeholder="First Name" aria-describedby="emailHelp" required>
</div>
    <!--LNAME-->
         <div class="input-wrapper mb-3">
            <i class="fa-solid fa-id-card input-icon"></i>
        <input type="text" class="form-control neon-input mb-3" id="LName" name="LName" placeholder="Last Name" aria-describedby="emailHelp" required>
       </div>

    <!--Phone number-->
<div class="input-wrapper mb-3">
            <i class="fa-solid fa-mobile-screen input-icon"></i>
        <input type="tel" class="form-control neon-input mb-3" id="PhoneNum" name="PhoneNum"
        placeholder="xxx-xxx-xxxx" aria-describedby="emailHelp" required>

</div>
    <!--EMAIL-->
        <div class="input-wrapper mb-3">
            <i class="fa-solid fa-at input-icon"></i>
        <input type="email" class="form-control neon-input mb-3" id="Email" name="Email" placeholder="Email"
        aria-describedby="emailHelp" required>
</div>
    <!--PASSWORD-->
 <div class="input-wrapper mb-3">
            <i class="fa-solid fa-key input-icon"></i>
        <input type="password" class="form-control neon-input mb-3" id="Password" name="Password" placeholder="Password">
       
</div>
        <div class="form-check text-white mb-3" required>
        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
        <label class="form-check-label">
            I accept the Terms and Conditions
</label>
</div>
    <button type="submit" class="btn neon-btn w-100 mb-3">Join Us!</button>
    <p class="login-link text-center mt-3">
        Already have an account? Click here 
        <a href="login.php">Log in</a>
    </form>
</div>
</div>
</div>
</body>
</html>
