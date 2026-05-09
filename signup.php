<?php 
    require_once("config.php");
    require_once("auth.php");
    redirect_if_logged_in();

$flash_success = get_flash('flash_success');
$flash_error   = get_flash('flash_error');


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {

        if(!isset($_POST['terms'])) {
           set_flash('flash_error', 'You must accept the Terms and Conditions to sign up.');
            header("Location: signup.php");
            exit();
        }

        $pdo = get_pdo();

        $stmt = $pdo->prepare("Select Email From User where Email = ?");

        $stmt->execute([$_POST['Email']]);
        
        $Email_row = $stmt->fetch(PDO::FETCH_ASSOC);

        $DB_Email = $Email_row['Email'];

        $LowerEmail = strtolower(trim($_POST['Email'] ?? ''));

        if (!filter_input(INPUT_POST, "Email", FILTER_VALIDATE_EMAIL)) {
          set_flash('flash_error', 'Invalid email format. Please enter a valid email address.');
            header("Location: signup.php");
            exit();
        }elseif ($LowerEmail === $DB_Email) {
            set_flash('flash_error', 'Email already exists.');
            header("Location: signup.php");
            exit();
        } elseif (strlen($_POST['Password']) < 8) {
            set_flash('flash_error', 'Password is too small, must be at least 8 characters long.');
            header("Location: signup.php");
            exit();
        }elseif(preg_match("/[!@#$%^&*()<>?]/", $_POST['Password']) === 0){
                set_flash('flash_error', 'Password must contain at least one special character from this list: "!@#$%^&*()<>?"');
                header("Location: signup.php");
                exit();
        }elseif(strlen($_POST['PhoneNum']) > 10 || strlen($_POST['PhoneNum']) < 10){
                set_flash('flash_error', 'Your phone number needs to be 10 numbers long');
                header("Location: signup.php");
                exit();
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
        set_flash('flash_success', 'Signup successful. Please login to start.');
        exit();
       }

    } 
     catch (PDOException $e) {
        set_flash('flash_error', $e->getMessage());
        header("Location: signup.php");
        exit();
    }
}
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
            <h2>Create An Account</h2>
                <p>Join and start exploring games</p>
</div>
</div>
<div class="col-md-6 right-side">
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
    <h2 class="text-center text-white mb-4 signup-title"> Sign Up </h2>
    <form method="POST">
    <!--FNAME-->
        <input type="text" class="form-control neon-input mb-3"  id ="FName" name="FName" placeholder="First Name" aria-describedby="emailHelp" required>

    <!--LNAME-->
        <input type="text" class="form-control neon-input mb-3" id="LName" name="LName" placeholder="Last Name" aria-describedby="emailHelp" required>


    <!--Phone number-->

        <input type="tel" class="form-control neon-input mb-3" id="PhoneNum" name="PhoneNum"
        placeholder="xxx-xxx-xxxx" aria-describedby="emailHelp" required>


    <!--EMAIL-->

        <input type="email" class="form-control neon-input mb-3" id="Email" name="Email" placeholder="Email"
        aria-describedby="emailHelp" required>

    <!--PASSWORD-->

        <input type="password" class="form-control neon-input mb-3" id="Password" name="Password" placeholder="Password">
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
