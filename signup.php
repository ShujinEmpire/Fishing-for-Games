<html>
<body>
    <?php include 'main.php';?>
    <form>
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="neon-card p-4">
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
    <button type="submit" class="btn neon-btn">Signup</button>
    </form>
</body>
</html>
