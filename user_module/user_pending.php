<?php
include('db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: user_login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user info
$userQuery = "SELECT roll_no, name, class, section, sem FROM users WHERE email='$email'";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult);

// Fetch pending complaints
$complaintsQuery = "SELECT * 
                    FROM complaints 
                    WHERE email='$email' AND status='Pending'
                    ORDER BY complaint_id DESC";
$complaintsResult = mysqli_query($conn, $complaintsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pending Complaints</title>
<link rel="stylesheet" href="/Complaint_Portal/assets/css/uniresolve-theme.css">
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

/* Content */
.contentSection {
    /* flex: 1; */
    /* width:75vw;
    background-color: #111827;
    border-radius: 15px;
    padding: 40px;
    gap: 6vw;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      border: 1px solid rgba(255,255,255,0.05); */

width: 75vw;
      background-color: #111827;       
 align-items:center;
  justify-content: center;
  border-radius: 20px;
  padding-top: 30px;
  padding-bottom: 30px;
  gap:4vw;
  box-shadow:8px -5px 20px 0 rgba(0, 0, 0, 0.25);
  border: 1px solid rgba(255,255,255,0.05);
  overflow-y: auto;
  scrollbar-width: none;
}

.contentSection p {
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
    /* margin-bottom: 20px; */
}

.contentSection table {
  width: 90%;
  margin: 20px auto;
  border-collapse: collapse;
  background-color: #1F2937; /* dark background */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  overflow: hidden;
  border: 2px solid #232e3c; /* dark border */
}

.contentSection th, .contentSection td {
  padding: 4px;
  text-align: center;
  border-bottom: 1px solid #232e3c;
  font-size: 1rem;
  border: 2px solid #28394eff;
  color: #E5E7EB; /* light text */
  font-weight: 600;
}

.contentSection th {
  background-color: #03bfafff; /* accent color */
  color: #111827; /* dark text */
  font-weight: bold;
}

.contentSection tr:hover {
  background-color: #232e3c; /* slightly lighter dark */
}

.contentSection button {
  background-color: darkcyan;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 6px 10px;
  font-size: 0.7rem;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.3s, background-color 0.3s;
}

.contentSection .status {
  font-weight: bold;
  color: green;
}

.contentSection .viewBtn:hover {
  transform: translateY(-2px);
  background-color:rgba(20, 223, 172, 1);
  color: black;
}

.modal {
  display: none; 
  position: fixed; 
  left: 0; top: 0; 
  width: 100%; height: 100%; 
  background: rgba(193, 184, 184, 0.5); 
  justify-content: center; 
  align-items: center; 
  z-index: 1000;
}
.modal-content {
  background:#232e3c; 
  color: white;
  padding: 20px;
  border-radius: 10px; 
  max-width: 425px;
  width: 90%; 
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  max-height: 70vh;
  overflow-y: auto;
  scrollbar-width: none;
}

.close-btn {
    /* Center the button horizontally below the modal content */
    position: static;
    display: block;
    margin: 20px auto 0 auto;
    padding: 8px 16px;
    background: #0077ff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}

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
  .sideBar, .contentSection {
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

  .contentSection {
    flex: 1;
    flex-direction: row;
    gap: 20px;
    padding: 16px;
    overflow: scroll;
  }

  .contentSection p {
  
          /* font-size: 1.4rem;
          margin: 10px 20px; */
          font-size:1rem ;
          text-align: center;
          margin: 0 auto;
        }
        /* .profileSection #profileHeading {
          
          font-size: 2rem;
          margin: 16px;
        } */

      
   
  .contentSection table {
    width: 95%;
    /* font-size: 6rem; */
  }

  /* .contentSection th, .contentSection td {
    padding: 5px;
  } */

  /* Tablet button styles */
  /* .contentSection button {
    padding: 6px 10px;
    font-size: 0.9rem;
  } */
   
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

  .contentSection p {
    font-size: 0.85rem;
    text-align: center;
    margin: 0 auto;
    overflow: hidden;
  }
  
  /* .contentSection #profileHeading {
    font-size: 1.5rem;
    overflow: hidden;
  } */
}


/* Modal */

</style>
</head>
<body>

<header>
  <img id="mainLogo" src="LOGO1.png" alt="Logo">
  <p>User Dashboard</p>
  <a href="user_signout.php"><img id="signOut" src="signout.jpg" alt="Sign Out"></a>
</header>

<div class="container">
  <div class="sideBar">
    <div class="profileImg">
      <img src="user_profile.jpg" alt="">
      <p><?php echo htmlspecialchars($user['name']); ?></p>
    </div>
    <div class="sidebarButton">
      <a href="user_dashboard.php">Dashboard</a><br>
      <a href="profile.php">Profile</a><br>
      <a href="form.php">Lodge Complaint</a>
    </div>
  </div>

  <div class="contentSection">
    <p>Pending Complaints</p>
    <table>
      <thead>
        <tr>
          <th>Sr. No</th>
          <th>Complaint Title</th>
          <th>Department</th>
          <th>Status</th>
          <th>View</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if(mysqli_num_rows($complaintsResult)>0){
            $sr=1;
            while($row=mysqli_fetch_assoc($complaintsResult)){
                echo "<tr>
                        <td>{$sr}</td>
                        <td>".htmlspecialchars($row['title'])."</td>
                        <td>".htmlspecialchars($row['category'])."</td>
                        <td class='status'>".htmlspecialchars($row['status'])."</td>
                        <td><button class='viewBtn' data-id='{$row['complaint_id']}'>View</button></td>
                      </tr>";
                $sr++;
            }
        }else{
            echo "<tr><td colspan='5' style='color:red;text-align:center;'>No pending complaints found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Complaint Modal -->
<div id="complaintModal" class="modal">
  <div class="modal-content">
    <h3 style="color:white; font-size:large; text-align:center; ">Complaint Details</h3>
    <div id="complaintDetails"></div>
    <button class="close-btn" onclick="closeModal('complaintModal')">Close</button>
  </div>
</div>

<script>
function closeModal(id){ document.getElementById(id).style.display='none'; }

document.querySelectorAll('.viewBtn').forEach(btn => {
    btn.addEventListener('click', function(){
        let complaintId = this.dataset.id;

        fetch('user_view.php?id=' + complaintId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('complaintDetails').innerHTML = data;
            document.getElementById('complaintModal').style.display = 'flex';
        })
        .catch(err => alert('Error loading complaint details: '+err));
    });
});

// Assuming your modal ID is 'complaintModal' (common in user_completed.php); change if different
window.addEventListener('click', function(event) {
    var modal = document.getElementById('complaintModal'); // Update ID if needed
    if (event.target === modal) {
        modal.style.display = 'none'; // Or use your close function if it has animation
    }
});

// If you have a close button, ensure it's still working
document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('complaintModal').style.display = 'none';
});

</script>

</body>
</html>
