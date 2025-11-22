<?php
session_start();

if (isset($_POST['confirm_logout'])) {
    session_unset();
    session_destroy();
    header("Location: dept_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Logout Confirmation</title>
<style>
    body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #E5E7EB; /* Light text for visibility */
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  position: relative; /* For pseudo-element positioning */
}

body::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: url('backimage1.jpg'); /* Replace with your image path */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  filter: blur(5px); /* Adjust blur amount (e.g., 5px for light blur, 10px for more) */
  z-index: -1; /* Behind content */
}
    /* Overlay background */
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
    }

    /* Popup box */
    .popup {
    background-color: #111827;      /* Match your dark theme */
    color: #E5E7EB;                 /* Light text */
    font-size: 1rem;                /* Reduced font size */
    padding: 30px 20px;             /* Smaller padding */
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 3px 12px rgba(0,0,0,0.3);
    width: 300px;                   /* Reduced width */
    animation: fadeIn 0.3s ease;
}
.popup h2 {
    color: #E5E7EB;
    font-size: 1.1rem;              /* Smaller heading */
    margin-top: 0;
}
.popup button {
    margin: 10px 6px;
    padding: 8px 18px;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: bold;
    transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
}

.popup .yes {
  background: linear-gradient(45deg, #ff6b6b, #ee5a52); /* Red gradient */
  color: white;
  box-shadow: 0 4px 10px rgba(238, 90, 82, 0.3);
}

.popup .yes:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(238, 90, 82, 0.4);
  background: linear-gradient(45deg, #ee5a52, #ff6b6b);
  color: black;
}

.popup .no {
    background: linear-gradient(45deg, #48dbfb, #0abde3); /* Blue gradient */
    color: white;
    box-shadow: 0 4px 10px rgba(10, 189, 227, 0.3);
}

.popup .no:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(10, 189, 227, 0.4);
    background: linear-gradient(45deg, #0abde3, #48dbfb); /* Reverse gradient on hover */
    color: black;
}

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
</head>
<body>

<!-- Hidden form for logout -->
<form id="logoutForm" method="POST">
    <input type="hidden" name="confirm_logout" value="1">
</form>

<!-- Popup modal -->
<div class="overlay" id="logoutPopup">
    <div class="popup">
        <h2>Are you sure you want to sign out?</h2>
        <button class="yes" onclick="confirmLogout()">Yes</button>
        <button class="no" onclick="cancelLogout()">No</button>
    </div>
</div>

<script>
    // Show the popup automatically when page loads
    window.onload = function() {
        document.getElementById('logoutPopup').style.display = 'flex';
    };

    function confirmLogout() {
        document.getElementById('logoutForm').submit();
    }

    function cancelLogout() {
        window.history.back();
    }
</script>

</body>
</html>
