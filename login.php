<html>
<body class="login-page">
    <?php include 'main.php';?>
    
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-box">
            <h2 class="login-title">Login</h2>
            <p class="login-sub">
                Welcome back! Please login to continue exploring games.</p>
     <form>
    <div class="mb-3 input-group">
     <span class="input-group-text bg-transparanet border-0">
        <i class="bi bi-envelope"></i> 
     </span>

       <!-- <label for="input" class="form-label">Email address</label> -->
        
       <input type="email" class="form-control login-input" id="email"  name="email" placeholder="Email Address" aria-describedby="emailHelp">
    </div>

    <div class="mb-3 input-group">
        <span class="input-group-text bg-transparent border-0">
            <i class="bi bi-lock"></i>
        </span>
       <!--<label for="input" class="form-label">Password</label> -->
        <input type="password" class="form-control login-input" id="password" name="password" placeholder="Password">
    </div>
    <button type="submit" class="btn btn neon-btn w-100 mb-3">Log in -> </button>
    
  <!-- the link-->
   <p class="text-center small-text mt-3">
    Don't have an account?
    <a href="signup.php" class ="login-link">Sign Up</a> </p>
</form>
</div>
</div>
</body>
</html>