<?php
require_once("../auth.php");
require_once("../config.php");

require_login("../login.php");

$pdo = get_pdo();

$stmt = $pdo->prepare("
    SELECT UID, FName, LName, Email, PhoneNum, Profile_Image
    FROM User
    WHERE UID = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$fullName = $user['FName'] . " " . $user['LName'];

$profileImage = !empty($user['Profile_Image'])
    ? $user['Profile_Image']
    : "https://mdbootstrap.com/img/Photos/Others/placeholder-avatar.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="../dashboard.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
</head>

<body>

<nav id="sidebar">
  <div class="logo">
    Fishing for Games
    <small>User Profile</small>
  </div>

  <div class="nav-section">Navigate</div>
  <a href="../dashboard.php" class="nav-link">The Dock</a>
  <a href="profile.php" class="nav-link active">Profile</a>

  <div class="sidebar-footer">
    <a href="../logout.php" class="btn-signin">Sign Out</a>
  </div>
</nav>

<main id="main">

  <div class="topbar">
    <div class="topbar-title">
      <small>Account</small>
      My Profile
    </div>
  </div>

  <div class="profile-card">

    <div class="profile-header">

      <form method="POST" action="upload_profile.php" enctype="multipart/form-data">
        <label for="fileInput" style="cursor:pointer;">
          <div class="avatar-box">
            <img 
              id="selectedAvatar"
              src="<?= substr($profileImage, 0, 4) === 'http'
                  ? htmlspecialchars($profileImage)
                  : htmlspecialchars('../' . $profileImage) ?>?v=<?= time() ?>"
              alt="Profile"
            >
          </div>
        </label>
        
        <input 
          type="file"
          name="profile_image"
          id="fileInput"
          accept="image/*"
          onchange="this.form.submit()"
          hidden
        >
      </form>

      <div>
        <div class="profile-name"><?= htmlspecialchars($fullName) ?></div>
        <div class="profile-email"><?= htmlspecialchars($user['Email']) ?></div>
      </div>

    </div>

    <form method="POST" action="update_profile.php">

      <div class="profile-info">

        <div class="info-row">
          <div class="info-label">First Name</div>
          <input type="text" name="FName" class="form-input"
            value="<?= htmlspecialchars($user['FName']) ?>" required>
        </div>

        <div class="info-row">
          <div class="info-label">Last Name</div>
          <input type="text" name="LName" class="form-input"
            value="<?= htmlspecialchars($user['LName']) ?>" required>
        </div>

        <div class="info-row">
          <div class="info-label">Email</div>
          <input type="email" name="Email" class="form-input"
            value="<?= htmlspecialchars($user['Email']) ?>" required>
        </div>

      </div>

      <br>
      <button type="submit" class="btn-cast">Save Changes</button>

    </form>

  </div>

</main>

</body>
</html>