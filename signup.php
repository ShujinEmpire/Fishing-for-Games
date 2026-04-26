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
    <form>
    <!--FNAME-->    
    <div class="mb-3">  
        <input type="text" class="form-control neon-input"  id ="firstName" name="firstName" placeholder="First Name" aria-describedby="emailHelp">
    </div>

    <!--LNAME-->
    <div class="mb-3">
        <input type="text" class="form-control neon-input" id="lastName" name="lastName" placeholder="Last Name" aria-describedby="emailHelp">
    </div>

    <!--Phone number-->
    <div class="mb-3">
        <input type="tel" class="form-control neon-input" id="number" name="number" 
        placeholder="xxx-xxx-xxxx" aria-describedby="emailHelp">
    </div>
    
    <!--EMAIL-->    
    <div class="mb-3">
        <input type="email" class="form-control neon-input" id="email" name="email" placeholder="Email"
        aria-describedby="emailHelp">
    </div>
    <!--PASSWORD-->
    <div class="mb-3">
        <input type="password" class="form-control neon-input" id="password" name="password" placeholder="Password">
    </div>
    <button type="submit" class="btn btn-custom w-100">Sign up</button>
    </form>
</div>
</div>
</div>
</div>
</body>
</html>
