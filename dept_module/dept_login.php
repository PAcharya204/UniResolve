<?php
session_start();
include("db.php");

$popupMsg = "";
$popupType = "";
$popup_redirect = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $user = trim($_POST['userid']);
    $pass = trim($_POST['password']);

    if (empty($user) || empty($pass)) {
        $popupMsg = "Please fill in both fields.";
        $popupType = "error";
    } else {
        $stmt = $conn->prepare("SELECT dept_password FROM admin WHERE dept_email = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if ($pass === $row['dept_password']) {
                $_SESSION['dept_email'] = $user;
                $popupMsg = "✅ Login successful! Welcome.";
                $popupType = "success";
                $popup_redirect = "dept_dashboard.php";
            } else {
                $popupMsg = "❌ Invalid password.";
                $popupType = "error";
            }
        } else {
            $popupMsg = "❌ Email not found.";
            $popupType = "error";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Department Login</title>
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #0B1220;
    color: #E5E7EB;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}
body::before {
    content: '';
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background-image: url('backimage.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    filter: blur(2px);
    z-index: -1;
}
header {
    background: #131d31ff;
    color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 13vh;
    width: 100vw;
}
#mainLogo {
    height: 100px;
    width: auto;
    margin: 0;
}
.headerTitle {
    font-size: 2rem;
    font-weight: bold;
    letter-spacing: 1px;
}
form {
    width: 300px;
    background-color: #111827;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.5);
    text-align: center;
    border: 1px solid rgba(255,255,255,0.05);
    position: relative;
    z-index: 1;
}
h2 {
    font-size: 22px;
    color: #F1F5F9;
    margin-bottom: 18px;
}
label {
    display: block;
    text-align: left;
    font-weight: 600;
    font-size: 0.95rem;
    color: #E5E7EB;
    margin-bottom: 5px;
}
input {
    width: 93%;
    padding: 10px;
    margin-bottom: 12px;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    font-size: 0.95rem;
    background-color: #1F2937;
    color: #E5E7EB;
}
input:focus {
    outline: none;
    border-color: #03bfafff;
}
button {
    width: 100%;
    padding: 10px;
    font-size: 0.95rem;
    background: linear-gradient(90deg, #020267ff 0%, #03bfafff 100%);
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: transform 0.3s ease, background 0.3s ease;
}
button:hover {
    background: linear-gradient(90deg, #03bfafff 0%, #020267ff 100%);
    transform: translateY(-2px);
}
.center-wrapper {
    height: 87vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
}
.modal {
    display: none;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-content {
    background-color: #111827;
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    width: 260px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.05);
    color: #E5E7EB;
}
.close-btn {
    margin-top: 12px;
    width: 100px !important;
    padding: 4px 0px !important;
    background: #e5e7eb !important;
    color: #111 !important;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s, color 0.3s, transform 0.3s;
}
.close-btn:hover {
    background: #dc2626 !important;
    color: #fff !important;
    transform: translateY(-2px);
}
.success { color: #10B981; font-weight: bold; }
.error { color: #EF4444; font-weight: bold; }
p {
    margin-top: 20px;
    font-size: 1rem;
    color: #E5E7EB;
}
p a {
    color: #03bfafff;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 600;
}
p a:hover {
    text-decoration: underline;
}
/* Tablet */
@media (max-width: 1024px) {
    form {
        width: 260px;
        padding: 16px;
    }
    h2 {
        font-size: 20px;
        margin-bottom: 15px;
    }
    label, input, button, p, p a {
        font-size: 0.95rem;
    }
    .modal-content {
        width: 220px;
        padding: 16px;
    }
}
/* Mobile */
@media (max-width: 767px) {
    header {
        height: 10vh;
    }
    #mainLogo {
        height: 50px;
    }
    .headerTitle {
        font-size: 1.5rem;
    }
    form {
        width: 220px;
        padding: 10px;
        margin: 0px 10px;
    }
    h2 {
        font-size: 18px;
        margin-bottom: 12px;
    }
    label, input, button, p, p a {
        font-size: 0.9rem;
    }
    .modal-content {
        width: 90vw;
        padding: 12px;
    }
}
</style>
</head>
<body>

<header>
  <img id="mainLogo" src="LOGO1.png" alt="Logo">
  <span class="headerTitle">UniResolve</span>
</header>

<div class="center-wrapper">
<form id="loginForm" method="POST" action="">
    <h2>DEPARTMENT LOGIN</h2>
    <label for="userid">Email</label>
    <input type="email" name="userid" id="userid" placeholder="Enter your email" required>
    <label for="password">Password</label>
    <input type="password" name="password" id="password" placeholder="Enter your password" required>
    <button type="submit" name="login" style="width: 65%;">Log in</button>
    <p>Don’t have an account? <a href="dept_registration.php">Register</a></p>
    <p>Go back <a href="/Complaint_Portal/index.html">Index</a></p>
</form>
</div>

<!-- Popup Modal -->
<div id="popupModal" class="modal">
    <div class="modal-content">
      <p class="<?php echo $popupType; ?>"><?php echo $popupMsg; ?></p>
      <button class="close-btn" onclick="document.getElementById('popupModal').style.display='none'">Close</button>
    </div>
</div>

<?php if (!empty($popupMsg)): ?>
<script>
    document.getElementById('popupModal').style.display = 'flex';
    <?php if ($popupType === "success" && !empty($popup_redirect)): ?>
    setTimeout(function() {
        window.location.href = "<?php echo $popup_redirect; ?>";
    }, 1500);
    <?php endif; ?>
</script>
<?php endif; ?>
</body>
</html>
