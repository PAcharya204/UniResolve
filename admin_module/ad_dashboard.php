<?php
include('db.php'); 
session_start(); 

// ✅ Check if admin is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ad_login.php");
    exit();
}

// Admin info
$admin_name = $_SESSION['username'];
$profile_image = "admin.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<style>
/* ===== GLOBAL ===== */
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
  /* height: 7vh; */
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
}

.sideBar{
  display:flex;
  flex-direction:column;
  /* width:25vw; */
  width:25vw;
  /* height: 100%; */
  background-color: #111827;       
  /* background-color: burlywood;      */
  align-items:center;
  justify-content:center;
  border-radius: 20px;
  /* box-shadow:8px -5px 20px 0 rgba(220, 215, 215, 1); */
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
  /* height: 15vh;
  width: 15vh; */
  height: 20vh;
  width: 20vh;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #0E5C56;      /* muted teal accent */
  transition: transform 0.3s;
}

.profileImg p {
  margin-bottom: 4vh;
  /* font-size:x-large; */
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
  /* width:15vw;                        */
  width:15vw;                       /* matches old button width */
  text-align:center;
  justify-content: center ;
  /* background: linear-gradient(90deg, #3E2A63, #0E5C56); */
  background: linear-gradient(320deg, #020267ff 0%, #03bfafff 100%); 
  /* background: linear-gradient(320deg, #614d04ff 0%, #070c0cff 100%);  */
  color:#FFFFFF;
  border-radius:10px;
  /* font-size:x-large; */
  font-size:1rem;
  font-weight:bold;
  /* padding:10px; */
  padding:8px;
  border:0;
  cursor:pointer;
  text-decoration:none;
  box-shadow:0 3px 6px rgba(0,0,0,0.10);
  margin-bottom:10px;
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

/* Complaint section now matches dept_dashboard: vertical cards only */
.complaintSection{
  display:flex;
  flex-direction:row;
  width:75vw;
  background-color: #111827;
  align-items:center;
  justify-content: center;
  flex-wrap: wrap;
  border-radius: 20px;
  gap:4vw;
  box-shadow:8px -5px 20px 0 rgba(0, 0, 0, 0.25);
  border: 1px solid rgba(255,255,255,0.05);
}

/* Constrain each category and center its internal content */
.category {
   /* 220px is the "flex-basis" value, meaning the initial width of the flex item (category box) before growing/shrinking. */
  width: 210px;
  min-width: 180px;
  display: flex;
  flex-direction: column;
  align-items: center;
  background: #0B1220;
  border-radius: 20px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  padding-bottom: 20px;
  /* margin: px; */
  /* padding-bottom: 34px; */
}

.cardContainer {
  display: flex;
  flex-direction: column;
  gap: 18px;
  justify-content: center;
  align-items: center;
}

.card {
  /* flex: 1 1 19vw; */
  /* max-width: 19vw; */
  height: 14vh; /* reduced from 22vh/1vh/12vh/70px etc */
  width: 160px;
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  font-size:large;
  font-weight: bold;
  color: white;
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.3);
  transition: transform 0.3s, background 0.2s, color 0.2s;
  cursor: pointer;
  background: linear-gradient(335deg, #020267ff 0%, #03bfafff 100%);
}

.card img {
  width: 2vw;
  height: 4vh;
}

.card:hover {
  transform: translateY(-5px);
  color: #FFFFFF;
  background: linear-gradient(135deg, #4A3678, #146A63);
}

/* =========== Responsive (keep original layout, keep dark theme) =========== */
@media (max-width: 1024px) {
  body{
    background-color: #0B1220; /* stay dark on tablet */
  }
  header {
    height: 10vh;
    /* font-size: 6vw; */
    font-size: 2rem;
    padding: 0 2vw;
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
    gap: 20px;         /* reduced from 20px */
    padding: 16px;    /* reduced from 16px */
    height: auto;
    min-height: 100vh;
  }
  .sideBar, .complaintSection {
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
    /* width:15vw; */
    width:17vw;
    font-size: 1rem;
    padding: 10px;
    /* flex-direction: row; */
    overflow: hidden;
  }
  .profileImg img {
    height: 12vh;
    width: 12vh;
  }
  /* .complaintSection p {
    font-size: 2rem;
    margin: 16px;
  } */
  .complaintSection {
    flex: 1;
    flex-direction: row;
    gap: 8px;         /* reduced from 20px */
    padding: 10px;    /* reduced from 16px */
    justify-content: center; /* ensure centered on tablet */
  }
  .category {
    max-width: 90vw;
    min-width: 120px;
    padding: 18px;
    margin: 6px;      /* reduced from 10px */
  }
  .cardContainer {
    gap: 14px;
  }
  .card {
    max-width: 180px;
    width: 180px;
    height: 56px;
    font-size: .8rem;
    padding: 8px;
  }
  .card img {
    width: 24px;
    height: 24px;
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
    /* font-size: 1.5rem; */
  }
  header #mainLogo { height: 10vh; }
  header #signOut { height: 4vh; margin-right: 1vw; }

  .container {
    flex-direction: column;
    gap: 12px;         /* reduced from 12px */
    padding: 14px;     /* reduced from 14px */
    height: auto;
    min-height: 100vh;
    background: none;
    width: 100%;
  }
  .sideBar, .complaintSection {
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
    /* font-size: 1rem; */
    font-size: 0.9rem;
    padding: 10px;
    overflow-x: hidden;
  }
  .profileImg img {
    height: 10vh;
    width: 10vh;
  }
  .complaintSection {
    flex-direction: column;
    gap: 8px;         /* reduced from 2vh */
    padding: 6px;     /* reduced from 12px */
  }
  .category {
  overflow: hidden;
  max-width: 70%;
  min-width: 0;
  width: 100%;
  padding: 12px;
  margin: 4px;      /* reduced from 6px */
}
.cardContainer {
  gap: 10px;
}
.card {
    overflow: hidden;
    width: 80vw;
    max-width: 50%;
    height: 48px;
    font-size: 0.95rem;
    padding: 6px;
  }
  .card img {
    width: 18px;
    height: 18px;
  }
}
</style>
</head>
<body>

<header>
  <img id="mainLogo" src="LOGO1.png" alt="Logo">
  <span class="headerTitle">Admin Dashboard</span>
  <a href="ad_signout.php"><img id="signOut" src="signout.jpg" alt="Sign Out"></a>
</header>

<div class="container">
  <!-- Sidebar -->
  <div class="sideBar">
    <div class="profileImg">
      <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile">
      <p><?php echo htmlspecialchars($admin_name); ?></p>
    </div>
    <div class="sidebarButton">
      <a href="ad_dashboard.php" class="active">Dashboard</a><br>
      <a href="user_details.php">User Details</a><br>
      <a href="ad_details.php">Department Admin Details</a>
    </div>
  </div>

  <!-- Main Content: Restore previous layout with categories and cards -->
  <div class="complaintSection">
    <!-- Academic Category -->
    <div class="category">
      <h2>Academic</h2>
      <div class="cardContainer">
        <div class="card" onclick="window.location.href='academic_completed.php'">
          <img src="folder.png" alt="">Completed Complaints
        </div>
        <div class="card" onclick="window.location.href='academic_pending.php'">
          <img src="folder.png" alt="">Pending Complaints
        </div>
      </div>
    </div>
    <!-- Hostel Category -->
    <div class="category">
      <h2>Hostel</h2>
      <div class="cardContainer">
        <div class="card" onclick="window.location.href='hostel_completed.php'">
          <img src="folder.png" alt="">Completed Complaints
        </div>
        <div class="card" onclick="window.location.href='hostel_pending.php'">
          <img src="folder.png" alt="">Pending Complaints
        </div>
      </div>
    </div>
    <!-- canteen Category -->
    <div class="category">
      <h2>canteen</h2>
      <div class="cardContainer">
        <div class="card" onclick="window.location.href='canteen_completed.php'">
          <img src="folder.png" alt="">Completed Complaints
        </div>
        <div class="card" onclick="window.location.href='canteen_pending.php'">
          <img src="folder.png" alt="">Pending Complaints
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>

