<?php
include('db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: /Complaint_Portal/landing.htm");
    exit();
}

$email = $_SESSION['email'];
$query = "SELECT roll_no, name, class, section, sem, email, dob, password, phone, profile 
          FROM users 
          WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    die("❌ User not found.");
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Profile</title>

<style>
* {
  box-sizing: border-box; /* Apply globally to include padding in width calculations */
}
body{
  padding:0;
  margin:0;
  box-sizing:border-box;
  font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #0B1220;      /* deep charcoal from first.css */
  color: #E5E7EB;                 /* light gray text */
 font-size: 0.85rem;
}

header{
  /* height: 15vh; */
  height: 13vh;
  width: 100%;
  /* background: linear-gradient(90deg, #3E2A63 0%, #0E5C56 100%);  */
  background: linear-gradient(90deg, #020267ff 0%, #03bfafff 100%); 
  /* background: linear-gradient(90deg, #7e4d04ff 0%,  #e3e84bff 100%);  */
  color: #FFFFFF;                 /* white text on dark header */
  /* font-size: 70px; */
  font-size: 3rem;
  font-weight: bolder;
  display: flex;
  justify-content:space-between;
  align-items: center;
}

header #mainLogo {
  /* height: 25vh; */
  height: 20vh;
  width: auto;
  cursor: pointer;
}

header #signOut {
  height: 6vh;
  width: auto;
  margin-right: 1vw;
  cursor: pointer;
}

.container{
  height: 87vh;
  width: 100%;
  display: flex;
  gap: 20px; 
  padding: 20px;
  box-sizing: border-box;
  background: transparent;        
  /* background-color:chocolate;        */
}

.sideBar {
  display: flex;
  flex-direction: column;
  width: 25vw;
  background-color: #111827;
  align-items: center;
  justify-content: center;
  border-radius: 20px;
  border: 1px solid rgba(255,255,255,0.06);
  overflow: hidden;
}

.profileImg{
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}

.profileImg img{
  height: 20vh;
  width: 20vh;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #0E5C56;      /* muted teal accent */
  transition: transform 0.3s;
}

.profileImg p {
  margin-bottom: 4vh;
  font-size:large;
  font-weight: bold;
  color: #F1F5F9;                 /* bright text on dark card */
}

.sidebarButton img:hover, .profileImg img:hover{
  transform: scale(1.05);
}

/* Buttons keep your structure, adopt first.css colors */
.sidebarButton {
  display:flex;
  flex-direction:column;
  align-items: center;
  width:100%;
  gap:12px;
}

.sidebarButton a{
  display:block;
  width:15vw;                       /* matches old button width */
  text-align:center;
  justify-content: center ;
  /* background: linear-gradient(90deg, #3E2A63, #0E5C56); */
  background: linear-gradient(320deg, #020267ff 0%, #03bfafff 100%); 
  /* background: linear-gradient(320deg, #614d04ff 0%, #070c0cff 100%);  */
  color:#FFFFFF;
  border-radius:10px;
  font-size:1rem;
  font-weight:bold;
  padding:8px;
  border:0;
  cursor:pointer;
  text-decoration:none;
  box-shadow:0 3px 6px rgba(0,0,0,0.10);
  transition: transform .3s, background .2s, color .2s;
}

.sidebarButton a:hover{
  transform: translateY(-5px);
  background: linear-gradient(90deg, #4A3678, #146A63);
  color:#FFFFFF;
}

/* current page */
.sidebarButton a.active{
  background:#0E5C56;               /* solid muted teal */
  color:#FFFFFF;
  box-shadow: inset 0 0 10px rgba(255,255,255,.18);
}



/* ===================== PROFILE SECTION ===================== */
.profileSection {
    /* flex: 1; */
    width:75vw;
    background-color: #111827;
    border-radius: 15px;
    padding: 30px 20px;
    gap: 6vw;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.05);    
    height: 100%;
    overflow-y: scroll;
    scrollbar-width: none;
}


.profileSection #profileHeading {
  width:25%;
  text-align: center;
    /* align-items: center; */
    /* justify-content: center; */
    font-size: 1.2rem;
    color: #283e51;
    font-weight: bold;
    
    border-bottom: 5px solid #4b79a1;
    background: beige;
    border-radius: 10px;
    padding: 6px 0px;
    margin: 0 auto;
    margin-bottom: 25px;
}

.profileSection p {
    font-size: 1rem;
    font-weight: 500;
    margin: 10px 0;
    padding: 8px 14px;
    background: #1F2937; /* dark card background */
    border-radius: 10px;
    color: #E5E7EB;      /* light text */
    border: 1px solid #03bfafff; /* subtle border */
    box-shadow: none;
    letter-spacing: 0.02em;
}


.profileSection p:hover{
        border-color:rgba(78, 225, 117, 0.45);
        /* background-color:white; */
        box-shadow:0 0 10px 0 rgba(6, 234, 67, 0.5);
      }

.profileSection strong {
    color: #03bfafff; /* accent color */
    font-weight: 700;
    font-size: 1rem;
}

/* .profileSection {
  padding-bottom: 10px;
}
.profileSection p:last-child {
  margin-bottom: 0;
} */


@media (max-width: 1024px) {
body{
    background-color: #0B1220; /* stay dark on tablet */
  }
  header {
    height: 10vh;
    /* font-size: 6vw; */
    font-size: 2rem;
    /* padding: 0 2vw; */
  }
  header #mainLogo {
    height: 15vh;
  }
  header #signOut {
    height: 6vh;
    margin-right: 1vw;
  }
  .container {
    flex-direction: column;
    gap: 20px;
    padding: 16px;
    height: auto;
    min-height: 100vh;
  }
  .sideBar, .profileSection {
    width: 100%;
    padding: 16px;
    border-radius: 16px;
  }

  .sidebarButton{
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
  }

  .sidebarButton a {
    width:12vw;
    font-size: 1rem;
    padding: 10px;
    /* flex-direction: row; */
    overflow: hidden;
    
  }
  .profileImg img {
    height: 12vh;
    width: 12vh;
  }

  .profileSection {
    flex-direction: row;
    gap: 20px;
    padding: 16px;
  }

  .profileSection p {
  
          /* font-size: 0.8rem; */
          margin: 10px 20px;
        }
        /* .profileSection #profileHeading {
          
          font-size: 2rem;
          margin: 16px;
        } */

        .profileSection #profileHeading {
          /* width: 30%; */
    font-size: 1rem;
    font-weight: 700;
   
   
}


      
}

@media (max-width: 600px) {
  body{
    background-color: #0B1220; /* stay dark on mobile */
  }
  html, body {
    overflow-x: hidden;
    margin: 0;
    padding: 0;
  }
  header {
    height: 10vh;
    font-size: 5vw;
  }
  header #mainLogo { height: 10vh; }
  header #signOut { height: 4vh; margin-right: 1vw; }

  .container {
    flex-direction: column;
    gap: 12px;
    padding: 14px;
    height: auto;
    min-height: 100vh;
    background: none;
    width: 100%;
  }
  .sideBar, .contentSection {
    width: 100%;
    padding: 12px;
    border-radius: 12px;
  }

  .sidebarButton{
    flex-direction: column;
    /* flex-wrap: wrap;
    justify-content: center; */
  }
  .sidebarButton a {
    width:30vw;
    font-size: 0.9rem;
    padding: 10px;
    overflow-x: hidden;
  }
  .profileImg img {
    height: 10vh;
    width: 10vh;
  }

  .profileSection p {
    /* font-size: 0.7rem; */
    margin: 8px 20px;
    overflow: hidden;
  }
  
  .profileSection #profileHeading {
    font-size: 0.8rem;
    overflow: hidden;
    font-weight: 700;
  }

  /* .profileSection{
    overflow: auto;
  } */
}
</style>
</head>

<body>
<header>
    <img id="mainLogo" src="LOGO1.png" alt="Main Logo">
    <p>User Dashboard</p>
    <a href="user_signOut.php">
      <img id="signOut" src="signout.jpg" alt="Sign Out" title="Sign Out">
    </a>
</header>

<div class="container">
    <!-- Sidebar -->
    <div class="sideBar">
        <div class="profileImg">
            <img src="user_profile.jpg" alt="User Profile">
            <p><?php echo htmlspecialchars($user['name']); ?></p>
        </div>

        <div class="sidebarButton">
            <a href="http://localhost/Complaint_Portal/user_module/user_dashboard.php">Dashboard</a><br>
            <a href="http://localhost/Complaint_Portal/user_module/profile.php" class="active">Profile</a><br>
            <a href="http://localhost/Complaint_Portal/user_module/form.php">Lodge Complaint</a>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="profileSection">
        <p id="profileHeading">Profile Information</p>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Registration Number:</strong> <?php echo htmlspecialchars($user['roll_no']); ?></p>
        <p><strong>Course:</strong> <?php echo htmlspecialchars($user['class']); ?></p>
        <p><strong>Section:</strong> <?php echo htmlspecialchars($user['section']); ?></p>
        <p><strong>Semester:</strong> <?php echo htmlspecialchars($user['sem']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob']); ?></p>
        <p><strong>Password:</strong> <?php echo str_repeat("*", strlen($user['password'])); ?></p>
    </div>
</div>

</body>
</html>
