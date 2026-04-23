<html>
<body>
    <?php include 'main.php';?>
    <form>
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="neon-card p-4">
    <!--FNAME-->    
    <div class="mb-3">    
    <label for="input" class="form-label">First Name</label>
        <input type="text" class="form-control" id="input" aria-describedby="emailHelp">
    </div>

    <!--LNAME-->
    <div class="mb-3">
        <label for="input" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="input" aria-describedby="emailHelp">
    </div>

    <!--Phone number-->
    <div class="mb-3">
        <label for="input" class="form-label">Phone Number</label>
        <input type="tel" class="form-control" id="input" aria-describedby="emailHelp">
    </div>
    
    <!--EMAIL-->    
    <div class="mb-3">
        <label for="input" class="form-label">Email address</label>
        <input type="email" class="form-control" id="input" aria-describedby="emailHelp">
    </div>
    <!--PASSWORD-->
    <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Password</label>
        <input type="password" class="form-control" id="exampleInputPassword1">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</body>
</html>
