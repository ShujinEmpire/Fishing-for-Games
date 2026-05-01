<html>
<body class="login-page">
    <?php include 'main.php';?>
    
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-box">
            <h2 class="login-title">Login</h2>
            <p class="login-sub">
                Welcome back! Please login to continue exploring games.</p>
     <form>
    <div class="mb-3 position-relative">
        <i class="bi bi-envelope input-icon"></i>
    
       <!-- <label for="input" class="form-label">Email address</label> -->
        
       <input type="email" class="form-control " id="email"  name="email" placeholder="Email Address" aria-describedby="emailHelp">
    </div>

    <div class="mb-3 position-relative">
            <i class="bi bi-lock input-icon"></i>
       <!--<label for="input" class="form-label">Password</label> -->
        <input type="password" class="form-control " id="password" name="password" placeholder="Password">
    </div>
    <button type="submit" class="login-btn">Log in</button>
</form>
  <!-- the link-->
   <p class="login-link text-center mt-3">
    Don't have an account?
    <a href="signup.php">Sign Up</a> </p>
</div>
</div>
</body>
</html>