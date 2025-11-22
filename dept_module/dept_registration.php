<?php
include('db.php');

$popupMsg = "";
$popupType = "";

if (isset($_POST['register'])) {
    $deptad_name = trim($_POST['deptad_name']);
    $dob = trim($_POST['dob']);
    $department = trim($_POST['department']);
    $dept_email = trim($_POST['dept_email']);
    $dept_password = trim($_POST['dept_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $phone = trim($_POST['phone']);

    // Validation
    if (empty($deptad_name) || strlen($deptad_name) < 3) {
        $popupMsg = "Department Admin Name must be at least 3 characters.";
        $popupType = "error";
    } elseif (!filter_var($dept_email, FILTER_VALIDATE_EMAIL)) {
        $popupMsg = "Invalid email format.";
        $popupType = "error";
    } elseif ($dept_password !== $confirm_password) {
        $popupMsg = "Passwords do not match.";
        $popupType = "error";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $popupMsg = "Phone number must be 10 digits.";
        $popupType = "error";
    } elseif (empty($department)) {
        $popupMsg = "Please select a department.";
        $popupType = "error";
    } else {
        // Check duplicate email
        $check = mysqli_query($conn, "SELECT * FROM admin WHERE dept_email='$dept_email'");
        if (mysqli_num_rows($check) > 0) {
            $popupMsg = "This email is already registered!";
            $popupType = "error";
        } else {
            // Insert
            $sql = "INSERT INTO admin (deptad_name, dob, department, dept_email, dept_password, phone) 
                    VALUES ('$deptad_name', '$dob', '$department', '$dept_email', '$dept_password', '$phone')";
            if (mysqli_query($conn, $sql)) {
                $popupMsg = "Registration Successful!";
                $popupType = "success";
            } else {
                $popupMsg = "Registration Failed!";
                $popupType = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Department Registration</title>
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
.center-wrapper {
    min-height: calc(100vh - 13vh);
    width: 100vw;
    display: flex;
    align-items: center;
    justify-content: center;
}
form {
    width: 100%;
    max-width: 480px;
    min-width: 220px;
    min-height: 420px;
    padding: 28px;
    background-color: #111827;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.5);
    text-align: center;
    border: 1px solid rgba(255,255,255,0.05);
    position: relative;
    z-index: 1;
    margin: 32px 0;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
}
h2 {
    font-size: 22px;
    color: #F1F5F9;
    margin-bottom: 16px;
}
label {
    width: 100%;
    text-align: left;
    font-weight: 600;
    font-size: 1rem;
    color: #E5E7EB;
    margin-bottom: 4px;
    margin-top: 10px;
}
input[type="text"], 
input[type="date"],  
input[type="email"],  
input[type="password"],  
input[type="number"],  
input[type="tel"],  
select,
input[type="file"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    font-size: 1rem;
    background-color: #1F2937;
    color: #E5E7EB;
    box-sizing: border-box;
}
input:focus, select:focus {
    outline: none;
    border-color: #03bfafff;
    box-shadow: 0 0 5px rgba(3,191,175,0.2);
}
.btn {
    display: flex;
    justify-content: space-between;
    width: 100%;
    margin-top: 20px;
    gap: 10px;
}
input[type="submit"], input[type="reset"] {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(90deg, #020267ff 0%, #03bfafff 100%);
    color: white;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}
input[type="submit"]:hover, input[type="reset"]:hover {
    background: linear-gradient(90deg, #03bfafff 0%, #020267ff 100%);
    transform: translateY(-2px);
}
#login {
    width: 100%;
    margin: 10px 0 5px 0;
    font-size: 1rem;
    font-weight: bold;
    color: #E5E7EB;
    text-align:center;
}
#login a {
    color: #03bfafff;
    text-decoration: none;
    font-weight: 600;
}
#login a:hover {
    text-decoration: underline;
}
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0; top: 0;
    width: 100vw; height: 100vh;
    background-color: rgba(0,0,0,0.7);
    justify-content: center; 
    align-items: center;
}
.modal-content {
    background: #111827;
    color: #E5E7EB;
    padding: 18px;
    border-radius: 10px;
    text-align: center;
    max-width: 340px;
    min-width: 220px;
    width: 90%;
    box-shadow: 0 6px 20px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.05);
}
.success { color: #10B981; font-weight: bold; }
.error { color: #EF4444; font-weight: bold; }
.close-btn {
    margin-top: 12px;
    width: 100px  !important; 
    min-width: unset;
    padding: 6px 0px !important;
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
@media (max-width: 900px) {
    form {
        /* max-width: 400px; */
        min-height: 260px;
        /* padding: 18px; */
    }
}
@media (max-width: 600px) {
    header {
        height: 9vh;
    }
    #mainLogo {
        height: 40px;
    }
    .headerTitle {
        font-size: 1.1rem;
    }
    form {
        max-width: 85vw;
        min-width: 0;
        min-height: 180px;
        padding: 10px;
        border-radius: 10px;
    }
    h2 {
        font-size: 1.1rem;
    }
    label {
        font-size: 0.95rem;
    }
    input, select {
        font-size: 0.95rem;
        padding: 6px;
    }
    input[type="submit"], input[type="reset"] {
        font-size: 0.95rem;
        padding: 8px 0;
    }
    .modal-content {
        max-width: 95vw;
        min-width: 180px;
        padding: 8px;
        border-radius: 8px;
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
    <h2>DEPARTMENT REGISTRATION</h2>
    <label for="deptad_name">Department Admin Name</label>
    <input type="text" name="deptad_name" id="deptad_name" placeholder="Enter Admin Name" required>

    <label for="dob">Date of Birth</label>
    <input type="date" name="dob" id="dob" required>

    <label for="department">Department</label>
    <select name="department" id="department" required>
        <option value="">Select Department</option>
        <option value="Academic">Academic</option>
        <option value="Canteen">Canteen</option>
        <option value="Hostel">Hostel</option>
    </select>

    <label for="dept_email">Department Email</label>
    <input type="email" name="dept_email" id="dept_email" placeholder="Enter Department Email" required>

    <label for="dept_password">Password</label>
    <input type="password" name="dept_password" id="dept_password" placeholder="Enter Password" required>

    <label for="confirm_password">Confirm Password</label>
    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>

    <label for="phone">Phone Number</label>
    <input type="tel" name="phone" id="phone" placeholder="Enter Phone Number" maxlength="10" required>

    <div class="btn">
        <input type="submit" name="register" value="Register">
        <input type="reset" value="Reset">
    </div>
    <p id="login">Already registered?&nbsp;&nbsp;<a href="dept_login.php">Login</a></p>
    <p id="login">Go Back&nbsp;&nbsp;<a href="/Complaint_Portal/index.html">Index</a></p>
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
</script>
<?php endif; ?>

<?php if ($popupType === "success"): ?>
<script>
  setTimeout(() => { window.location.href = "dept_login.php"; }, 2000);
</script>
<?php endif; ?>
</body>
</html>
