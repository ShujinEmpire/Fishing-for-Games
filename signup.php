<html>
<body>
    <?php include 'main.php';?>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="signup-box row g-0">
    <!--This is the left side (image) -->    
    <div class="col-md-6 left-side d-flex align-items-end">
        <img src=".images/leftside.jpg" width="300">
    <div class="overlay-text">
            <h2>Create An Account<h2>
                <p> Join and start exploring games<p>
</div>
</div>

<div class="col-md-6 right-side">
    <h2 class="text-center text-white mb-4 signup-title"> Sign Up </h2>
    <form>
    <!--FNAME-->      
        <input type="text" class="form-control neon-input mb-3"  id ="firstName" name="firstName" placeholder="First Name" aria-describedby="emailHelp">

    <!--LNAME-->
        <input type="text" class="form-control neon-input mb-3" id="lastName" name="lastName" placeholder="Last Name" aria-describedby="emailHelp">
    

    <!--Phone number-->
    
        <input type="tel" class="form-control neon-input mb-3" id="number" name="number" 
        placeholder="xxx-xxx-xxxx" aria-describedby="emailHelp">
    
    
    <!--EMAIL-->    
    
        <input type="email" class="form-control neon-input mb-3" id="email" name="email" placeholder="Email"
        aria-describedby="emailHelp">
  
    <!--PASSWORD-->

        <input type="password" class="form-control neon-input mb-3" id="password" name="password" placeholder="Password">
       <div class="form-check text-white mb-3">
        <input class="form-check-input" type="checkbox">
        <label class="form-check-label">
            I accept the Terms & Conditions
</label>
</div> 
    <button type="submit" class="btn neon-btn w-100 mb-3">Join Us!</button>
    </form>
</div>
</div>
</div>
</body>
</html>
