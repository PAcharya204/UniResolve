<?php
date_default_timezone_set('Asia/Kolkata');  // India time
session_start();
include('db.php');

// ✅ Load PHPMailer via Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Debug mode off for production
error_reporting(E_ALL);
ini_set('display_errors', 1);

$popupMsg = "";
$popupType = "";

// =========================================================
// =============== USER LOGIN SECTION =======================
// =========================================================
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if ($password === $user['password'] || password_verify($password, $user['password'])) {
            $_SESSION['email'] = $user['email'];
            $popupMsg = "✅ Login Successful!";
            $popupType = "success";
        } else {
            $popupMsg = "❌ Wrong Password!";
            $popupType = "error";
        }
    } else {
        $popupMsg = "❌ Email not found!";
        $popupType = "error";
    }
}

// =========================================================
// =============== STEP 1: SEND OTP =========================
// =========================================================
if (isset($_POST['send_otp'])) {
    $email = trim($_POST['forgot_email']);

    if (!empty($email)) {
        $checkUser = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($checkUser) > 0) {
            $otp = rand(100000, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));
            mysqli_query($conn, "UPDATE users SET otp='$otp', otp_expire='$expiry' WHERE email='$email'");


            // ✉️ Send OTP via Gmail SMTP
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'chaatologyy@gmail.com'; // 
                $mail->Password   = 'dsbnkxcogimziclq';   // 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('yourgmail@gmail.com', 'Complaint Portal');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code - Complaint Portal';
                $mail->Body = "<h3>Your OTP is <b>$otp</b></h3><p>It’s valid for 5 minutes.</p>";

                $mail->send();

                $_SESSION['reset_email'] = $email;
                $popupMsg = "📩 OTP sent to your email! Check your inbox.";
                $popupType = "success";
            } catch (Exception $e) {
                $popupMsg = "⚠️ Email not sent. Error: {$mail->ErrorInfo}";
                $popupType = "error";
            }
        } else {
            $popupMsg = "❌ No account found with that email!";
            $popupType = "error";
        }
    } else {
        $popupMsg = "⚠️ Please enter your email!";
        $popupType = "error";
    }
}

// =========================================================
// =============== STEP 2: VERIFY OTP =======================
// =========================================================
if (isset($_POST['verify_otp'])) {
    $otp = trim($_POST['otp']);
    $email = $_SESSION['reset_email'] ?? '';

    if (!empty($otp) && !empty($email)) {
        $now = date("Y-m-d H:i:s");  // PHP current time
        $verify = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND otp='$otp' AND otp_expire > '$now'");

        if (mysqli_num_rows($verify) > 0) {
            mysqli_query($conn, "UPDATE users SET otp=NULL, otp_expire=NULL WHERE email='$email'");
            $_SESSION['email'] = $email;
            unset($_SESSION['reset_email']);

            $popupMsg = "✅ OTP Verified! You are now logged in.";
            $popupType = "success";
        } else {
            $popupMsg = "❌ Invalid or expired OTP!";
            $popupType = "error";
        }
    } else {
        $popupMsg = "⚠️ Please enter OTP!";
        $popupType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Login</title>
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
    padding-left: 0;
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
    height: auto;
    width: 310px; /* Reduced from 300px */
    background-color: #111827; /* Dark surface like sidebar */
    padding: 12px; /* Reduced from 15px */
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.5);
    text-align: center;
    border: 1px solid rgba(255,255,255,0.05);
    position: relative;
    z-index: 1;
}

h2 {
    font-size: 22px;
    color: #F1F5F9; /* Bright text */
    margin-bottom: 18px;
}

label {
    display: block;
    text-align: left;
    font-weight: 600;
    font-size: 0.95rem; /* Reduced from 1.2rem */
    color: #E5E7EB; /* Light text */
    margin-bottom: 5px;
}

input {
    width: 93%;
    padding: 10px;
    margin-bottom: 12px;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    font-size: 0.95rem; /* Reduced from 1.1rem */
    background-color: #1F2937;
    color: #E5E7EB;
}


input:focus {
    outline: none;
    border-color: #03bfafff; /* Accent color */
}

button {
    width: 100%;
    padding: 10px;
    font-size: 0.95rem; /* Reduced from 1.2rem */
    background: linear-gradient(90deg, #020267ff 0%, #03bfafff 100%); /* Matching dashboard gradient */
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

.forgot a {
    color: #03bfafff; /* Accent color */
    cursor: pointer;
    font-size: 1rem; /* Reduced from 1.2rem */
    text-decoration: none;
    font-weight: 600;
}


.forgot a:hover {
    text-decoration: underline;
}

.center-wrapper {
    /* flex: 1; */
    height: 87vh; /* subtract header height */
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
    background: rgba(0,0,0,0.7); /* Darker overlay */
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: #111827;
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    width: 400px;
    /* width: 90%; */
    box-shadow: 0 6px 20px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.05);
    color: #E5E7EB;
}

.modal-content label {
    display: block;
    text-align: left;
    font-weight: 600;
    font-size: 1rem;
    color: #E5E7EB;
    margin-bottom: 6px;
}

 .modal-open .userLogin {
    display: none;
} 

.modal-content form .forgotForm {
    width: 50%; /* Adjust to 50% or 70% if you want it narrower/wider */
    margin: 0 auto; /* Centers the form horizontally */
}

.success {
    color: #10B981; /* Green for success */
    font-weight: bold;
}

.error {
    color: #EF4444; /* Red for error */
    font-weight: bold;
}

p {
    margin-top: 20px;
    font-size: 1rem; /* Reduced from 1.1rem */
    color: #E5E7EB;
}

p a {
    color: #03bfafff;
    text-decoration: none;
    font-size: 1rem; /* Reduced from 1.2rem */
    font-weight: 600;
}

p a:hover {
    text-decoration: underline;
}

#popupModal .modal-content {
    width: 200px;
}

.close-btn {
    margin-top: 12px;
    width: 100px !important;
    margin-bottom: 0px; /* ensure no bottom margin */
    padding: 4px 0px !important; /* reduce padding */
    background: #e5e7eb !important;   /* Light grey background */
    color: #111 !important;           /* Black text */
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s, color 0.3s, transform 0.3s;
}

.close-btn:hover {
    background: #dc2626 !important;   /* Red background on hover */
    color: #fff !important;           /* White text on hover */
    transform: translateY(-2px);
}

.otp-btn {
    width: 150px !important;
    background: linear-gradient(90deg, #020267ff 0%, #03bfafff 100%) !important;
    color: #fff !important;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s, transform 0.3s;
}
.otp-btn:hover {
    background: linear-gradient(90deg, #03bfafff 0%, #020267ff 100%) !important;
    transform: translateY(-2px);
}



#forgotModal .close-btn:hover {
    transform: translateY(-2px);
}



#forgotModal .modal-content label{
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.2rem;
    color: #F1F5F9;
}



/* Specific styles for forgotModal */
/* ...existing code... */
#forgotModal .modal-content{
    width: 300px; /* Reduced from 400px */
    /* height: auto; */
}

#forgotModal .modal-content form {
    width: 250px; /* Reduced from 300px */
    margin: 0 auto;
}

#forgotModal .modal-content input, #forgotModal .modal-content button {
    width: 200px; /* Reduced from 250px */
    text-align: center;
}
/* ...existing code... */


#otpModal .modal-content {
    width: 300px;
}
#otpModal .modal-content form {
    width: 250px;
    margin: 0 auto;
}
#otpModal .modal-content input, #otpModal .modal-content button {
    width: 200px;
    text-align: center; /* Centers the text inside the input */
}

#popupModal .modal-content {
    width: 260px;
}

/* Tablet styles */
@media (max-width: 1024px) and (min-width: 768px) {
    /* form {
        width: 250px;
        padding: 12px;
    } */
    h2 {
        font-size: 22px;
        margin-bottom: 18px;
    }
    label {
        font-size: 0.95rem;
        margin-bottom: 5px;
    }
    input {
        padding: 10px;
        margin-bottom: 12px;
        font-size: 0.95rem;
    }
    button {
        padding: 10px;
        font-size: 0.95rem;
    }
    .modal-content {
        width: 350px;
        padding: 20px;
    }
    #forgotModal .modal-content, #otpModal .modal-content {
        width: 220px;
    }
    #forgotModal .modal-content form, #otpModal .modal-content form {
        width: 180px;
        margin: 0 auto;
    }
    #forgotModal .modal-content input, #forgotModal .modal-content button,
    #otpModal .modal-content input, #otpModal .modal-content button {
        width: 140px;
        text-align: center;
    }
    #popupModal .modal-content {
        width: 220px;
    }
}

/* Mobile styles */
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
        width: 260px; /* Reduced from 300px */
        padding: 12px; /* Reduced from 10px */
        margin: 0px 20px;
    }
    h2 {
        font-size: 20px;
        margin-bottom: 15px;
    }
    label {
        font-size: 0.9rem;
        margin-bottom: 4px;
    }
    input {
        padding: 8px;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    button {
        padding: 8px;
        font-size: 0.9rem;
    }
    p {
        font-size: 0.9rem;
    }
    .modal-content {
        width: 90vw;
        padding: 15px;
    }
    #forgotModal .modal-content, #otpModal .modal-content {
        width: 70vw;
    }
    #forgotModal .modal-content form, #otpModal .modal-content form {
        width: 60vw;
        margin: 0 auto;
    }
    #forgotModal .modal-content input, #forgotModal .modal-content button,
    #otpModal .modal-content input, #otpModal .modal-content button {
        width: 50vw;
        text-align: center;
    }
    #popupModal .modal-content {
        width: 70vw;
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
<form method="POST" action="">
    <h2>USER LOGIN</h2>
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required>

    <div class="forgot">
        <a id="forgotLink" >Forgot Password?</a>
    </div>
    <br>

    <button type="submit" name="login">Log in</button>
    <p>Don’t have an account? <a href="user_registration.php">Register</a></p>
    <p>Go Back <a href="/Complaint_Portal/index.html">Index</a></p>
</form>
</div>

<div id="forgotModal" class="modal">
  <div class="modal-content">
    <h3>Forgot Password</h3>
    <form class="forgotForm" method="POST">
      <label for="forgot_email">Enter your registered email</label>
      <input type="email" id="forgot_email" name="forgot_email" placeholder="Enter your email" required>
      <button type="submit" name="send_otp">Send OTP</button>
    </form>
    <button class="close-btn" onclick="closeModal('forgotModal')">Close</button>
  </div>
</div>

<!-- OTP VERIFICATION MODAL -->
<div id="otpModal" class="modal">
  <div class="modal-content">
    <h3>Enter OTP</h3>
    <form method="POST">
      <input type="text" name="otp" placeholder="Enter 6-digit OTP" required>
      <button type="submit" name="verify_otp">Verify OTP</button>
    </form>
    <button class="close-btn" onclick="closeModal('otpModal')">Close</button>
  </div>
</div>

<!-- POPUP MESSAGE -->
<div id="popupModal" class="modal" style="display: <?php echo $popupMsg ? 'flex' : 'none'; ?>;">
  <div class="modal-content">
    <p class="<?php echo $popupType; ?>"><?php echo $popupMsg; ?></p>
    <?php if (strpos($popupMsg, 'OTP sent') !== false): ?>
      <button class="otp-btn" onclick="openOtpModal()">Enter OTP</button><br>
    <?php endif; ?>
    <button class="close-btn" onclick="closeModal('popupModal')">Close</button>
  </div>
</div>


<script>
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.classList.remove('modal-open');
}
document.getElementById('forgotLink').onclick = function() {
    document.getElementById('forgotModal').style.display = 'flex';
    document.body.classList.add('modal-open');
};
function openOtpModal() {
    document.getElementById('popupModal').style.display = 'none';
    document.getElementById('otpModal').style.display = 'flex';
    document.body.classList.add('modal-open');
}
window.addEventListener('load', function() {
    if (document.getElementById('popupModal').style.display === 'flex') {
        document.body.classList.add('modal-open');
    }
});
function closeModalOnOutsideClick(modalId) {
    document.getElementById(modalId).addEventListener('click', function(event) {
        if (event.target === this) {
            closeModal(modalId);
        }
    });
}
closeModalOnOutsideClick('forgotModal');
closeModalOnOutsideClick('otpModal');
closeModalOnOutsideClick('popupModal');
</script>

<?php if ($popupType=="success" && strpos($popupMsg,'Login Successful')!==false): ?>
<script> setTimeout(()=>{window.location.href="http://localhost/Complaint_Portal/user_module/user_dashboard.php";},1500); </script>
<?php endif; ?>

<?php if ($popupType=="success" && strpos($popupMsg,'OTP Verified')!==false): ?>
<script> setTimeout(()=>{window.location.href="http://localhost/Complaint_Portal/user_module/user_dashboard.php";},1500); </script>
<?php endif; ?>

</body>
</html>