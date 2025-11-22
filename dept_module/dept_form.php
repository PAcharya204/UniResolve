<?php
session_start();
include("db.php");

// Redirect if not logged in
if (!isset($_SESSION['dept_email'])) {
    header("Location: dept_login.php");
    exit();
}
// for dept name in header
$dept_email = $_SESSION['dept_email'];
// Fetch department name
$deptResult = mysqli_query($conn, "SELECT deptad_name, department FROM admin WHERE dept_email='$dept_email'");
$dept = mysqli_fetch_assoc($deptResult);
$department = $dept['department'];
// Get complaint ID from URL
$complaintId = $_GET['id'] ?? '';

// Fetch complaint and user info
$complaintData = [];
if ($complaintId) {
    $stmt = $conn->prepare("SELECT c.*, u.name, u.roll_no, u.class, u.section, u.sem, u.email  
                            FROM complaints c 
                            JOIN users u ON c.roll_no = u.roll_no 
                            WHERE c.complaint_id = ?");
    $stmt->bind_param("i", $complaintId);
    $stmt->execute();
    $result = $stmt->get_result();
    $complaintData = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
$popupMsg = "";
$popupType = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $solution = mysqli_real_escape_string($conn, $_POST['solution'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');
    $complaintId = $_POST['complaintId'] ?? '';

    if($complaintId){
        $stmt = $conn->prepare("UPDATE complaints SET solution = ?, status = ? WHERE complaint_id = ?");
        $stmt->bind_param("ssi", $solution, $status, $complaintId);
        if($stmt->execute()){
            $popupMsg = "Solution saved successfully!";
            $popupType = "success";
        } else {
            $popupMsg = "Error saving solution.";
            $popupType = "error";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Department Form</title>
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
  height: 13vh;
  width: 100%;
  /* background: linear-gradient(90deg, #3E2A63 0%, #0E5C56 100%);  */
  background: linear-gradient(90deg, #020267ff 0%, #03bfafff 100%); 
  /* background: linear-gradient(90deg, #7e4d04ff 0%,  #e3e84bff 100%);  */
  color: #FFFFFF;                 /* white text on dark header */
  font-size: 3rem;
  font-weight: bolder;
  display: flex;
  justify-content:space-between;
  align-items: center;
}

header #mainLogo {
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
  /* width: 100%; */
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

.complaintSection { 
  /* flex:1; 
  background-color: #111827; 
  border-radius:20px; 
  padding:30px 40px; 
  box-shadow:0 8px 25px rgba(0,0,0,0.2);
  gap: 4vw;
  box-shadow:8px -5px 20px 0 rgba(0, 0, 0, 0.25);
  border: 1px solid rgba(255,255,255,0.05); */
  width: 75vw;
      background-color: #111827;       
 align-items:center;
  justify-content: center;
  border-radius: 20px;
  padding: 30px;
  
  gap:4vw;
  box-shadow:8px -5px 20px 0 rgba(0, 0, 0, 0.25);
  border: 1px solid rgba(255,255,255,0.05);
  overflow-y: auto;
  scrollbar-width: none;

}
.complaintSection #head{ 
  text-align:center; 
  margin-bottom:25px; 
  color:#283e51;

  width: 27%;
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
    margin-bottom: 20px;
}
.two-column-form { 
  display:flex; 
  flex-direction:column; 
  gap:20px;

}
.form-row { 
  display:flex; 
  gap:20px; 
}

.form-group { 
  flex:1; 
  display:flex; 
  flex-direction:column;
 }
.form-group label { 
  font-weight:600; 
  font-size:1rem;
  margin-bottom:5px; 
  color: #03bfafff; /* accent color for label */
}


.form-group input,
.form-group select,
.form-group textarea {
  padding: 8px 12px;
  border-radius: 10px;
  border: 1px solid #03bfafff; /* accent border */
  background: #1F2937;         /* dark card background */
  color: #E5E7EB;             
  font-size: 1rem;
  box-sizing: border-box;
}
/* .form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: #03bfafff;
  background: #232e3c;
  outline: none;
} */

.form-group input:hover,
      .form-group textarea:hover,
      .form-group select:hover {
        border-color:rgba(78, 225, 117, 0.45);
        /* background-color:white; */
        box-shadow:0 0 10px 0 rgba(6, 234, 67, 0.5);
      }

.buttons { 
  display:flex; 
  justify-content:center; 
  gap:20px; 
  margin-top:20px; 
}

.buttons input[type="submit"],
.buttons button{
  /* background:linear-gradient(90deg,#4b79a1,#283e51); */
  color:#ffffff;
  border:none;
  padding:8px 20px;
  border-radius:10px;
  font-size:1rem;
  cursor:pointer;
  font-weight:bold;
  transition:transform .2s, background .3s;
}

.buttons input[type="submit"]{
    background: linear-gradient(135deg, #1ecf03ff, #04742fff  );

}

.buttons button{
    background: linear-gradient(135deg, #8d919eff, #3d3d3fff);

}

.buttons input[type="submit"]:hover{
  background: linear-gradient(135deg, #029e07ff, #6ceb6aff);
  color: black;
}

.buttons button:hover{
  background: linear-gradient(135deg, #ed7070ff, #a30303ff );
  color: black;
}
  
.buttons input[type="submit"]:hover,
.buttons button:hover{
  transform:translateY(-3px);
  /* background:linear-gradient(90deg,#283e51,#4b79a1); */
}



.modal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0; top: 0;
  width: 100vw; 
  height: 100vh;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: #111827;
  color: #E5E7EB;
  padding: 24px 32px;
  border-radius: 12px;
  min-width: 290px;
  max-width: 90vw;
  text-align: center;
  position: relative;
  box-shadow: 0 8px 32px rgba(0,0,0,0.25);
  animation: fadeInModal 0.4s;
}
@keyframes fadeInModal {
  from { transform: scale(0.8); opacity: 0; }
  to   { transform: scale(1); opacity: 1; }
}
.close {
  position: absolute;
  right: 18px;
  top: 10px;
  font-size: 1.5rem;
  color: #888;
  cursor: pointer;
}
.close:hover { color:#E5E7EB ; }

#modalMsg {
  font-size: 1rem;
  color: #E5E7EB;
  font-weight: 500;
  margin-top: 10px;
  margin-bottom: 5px;
  line-height: 1.6;
  text-align: center;
  /* Optional: add background or border for emphasis */
  /* background: #f0f4fa; */
  /* border-radius: 8px; */
  /* padding: 10px 0; */
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

  .complaintSection p{ font-size: 1rem;
    font-weight: 700; }
  /* .complaintSection {
    flex-direction: row;
    gap: 20px;
    padding: 16px;
  } */
        .two-column-form {
          width: 100%;
        }
        .form-row {
          flex-wrap: wrap;
          gap: 16px;
        }
        .form-group {
          flex: 1 1 48%;
          min-width: 260px;
        }
        /* .form-group input, .form-group textarea, .form-group select {
          height: 44px;
          font-size: 1rem;
        } */
        .form-group textarea {
          min-height: 100px;
        }
        .buttons {
          gap: 16px;
        }
        /* .buttons input[type="submit"], .buttons input[type="reset"] {
          font-size: 1rem;
          padding: 10px 16px;
        } */
      }

/* MOBILE */
@media (max-width:600px){
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


  .complaintSection {
    overflow-x: scroll;
  }
  .complaintSection p{ font-size:1rem; overflow: hidden;
    font-weight: 700;}
  .form-row{ flex-direction:column; gap:15px; }
  .form-group{ flex:1 1 100%; }
  .form-group input[type="file"] {
      width: 100%;
      box-sizing: border-box;
      font-size: 1rem;
      height: 44px;
      background: lightblue;
      padding: 5px;
  }
  /* .form-group input, .form-group textarea, .form-group select,.form-group{ margin-bottom: 8px; } */
  .buttons{ flex-direction:column; gap:12px; }
  /* .buttons input[type="submit"], .buttons input[type="reset"]{ width:40%; font-size:0.8rem; padding:8px 20px;margin: 0 auto; } */

  .buttons input[type="submit"], .buttons button{
    width: auto;      /* Prevents shrinking to full width */
    min-width: 120px; /* Optional: ensures a minimum button width */
    font-size: 0.95rem;
    padding: 10px 24px; /* More horizontal padding for better appearance */
    margin: 0 auto;
}
}
</style>
</head>
<body>
<header>
   <img  id="mainLogo" src="LOGO1.png" alt="Logo">
  <p><?php echo htmlspecialchars($department); ?> Dashboard</p>
  <a href="dept_signout.php"><img id="signOut" src="signout.jpg" alt="Sign Out"></a>
</header>

<div class="container">
    <div class="sideBar">
        <div class="profileImg">
            <img src="<?php echo !empty($complaintData['profile']) ? htmlspecialchars($complaintData['profile']) : 'dept_admin.png'; ?>" alt="">
            <p><?php echo htmlspecialchars($complaintData['name'] ?? 'Admin'); ?></p>
        </div>
        <div class="sidebarButton">
            <a href="dept_dashboard.php">Dashboard</a><br>
            <a href="dept_profile.php">Profile</a>
        </div>
    </div>

    <div class="complaintSection">
        <p id="head">Complaint Details</p>
        <form class="two-column-form" method="POST">
            <input type="hidden" name="complaintId" value="<?php echo htmlspecialchars($complaintData['complaint_id'] ?? ''); ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Register Number</label>
                    <input type="text" value="<?php echo htmlspecialchars($complaintData['roll_no'] ?? ''); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($complaintData['name'] ?? ''); ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Course</label>
                    <input type="text" value="<?php echo htmlspecialchars($complaintData['class'] ?? ''); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Section</label>
                    <input type="text" value="<?php echo htmlspecialchars($complaintData['section'] ?? ''); ?>" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Semester</label>
                    <input type="text" value="<?php echo htmlspecialchars($complaintData['sem'] ?? ''); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Complaint Title</label>
                    <input type="text" value="<?php echo htmlspecialchars($complaintData['title'] ?? ''); ?>" readonly>
                </div>
            </div>

            <div class="form-group">
                <label>Complaint Details</label>
                <textarea readonly><?php echo htmlspecialchars($complaintData['complaint'] ?? ''); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date of Inconvenience</label>
                    <input type="date" value="<?php echo htmlspecialchars($complaintData['date_inc'] ?? ''); ?>" readonly>
                </div>
                <div class="form-group">
    <label>Related File</label>
    <?php
    if (!empty($complaintData['file'])) {
        // Server path for file existence check
        $uploadsFolderServer = $_SERVER['DOCUMENT_ROOT'] . '/Complaint_Portal/uploads/';
        $uploadsFolderWeb = '/Complaint_Portal/uploads/';

        $fileName = basename($complaintData['file']);
        $serverPath = $uploadsFolderServer . $fileName;
        $webPath = $uploadsFolderWeb . $fileName;

        if (file_exists($serverPath)) {
            echo '<p><a href="' . htmlspecialchars($webPath) . '" target="_blank" download style="color:#1976d2; font-weight:bold; text-decoration:none;">Download File</a></p>';
        } else {
            echo '<p style="color:red; font-weight:bold;">File not found</p>';
        }
    } else {
        echo '<p style="color:red; font-weight:bold;">No file uploaded</p>';
    }
    ?>
</div>

</div>

            <div class="form-group">
                <label>Solution</label>
                <textarea name="solution"></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="Pending" <?php if(($complaintData['status'] ?? '')=="Pending") echo "selected";?>>Pending</option>
                    <option value="Completed" <?php if(($complaintData['status'] ?? '')=="Completed") echo "selected";?>>Completed</option>
                    <!-- <option value="Rejected" <?php if(($complaintData['status'] ?? '')=="Rejected") echo "selected";?>>Rejected</option> -->
                </select>
            </div>

            <div class="buttons">
                <input type="submit" value="Submit">
                <button type="button" onclick="window.location.href='dept_pending.php'">Back</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Popup -->
<div id="popupModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeModalBtn">&times;</span>
    <img id="modalImg" src="" alt="Status" style="width:60px;display:block;margin:0 auto 12px auto;">
    <div id="modalMsg"></div>
  </div>
</div>

<?php if(!empty($popupMsg)): ?>
<script>
window.addEventListener('DOMContentLoaded', function() {
  showModal(`<?php echo $popupMsg; ?>`, '<?php echo $popupType; ?>');
});
</script>
<?php endif; ?>

<script>
function showModal(msg, type) {
  let imgSrc = '';
  if(type === 'success') imgSrc = 'check.png';
  else if(type === 'error') imgSrc = 'cross.png';
  else imgSrc = 'images/warning.png';

  document.getElementById('modalImg').src = imgSrc;
  document.getElementById('modalMsg').innerHTML = msg;
  document.getElementById('popupModal').style.display = 'flex';
}
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('closeModalBtn').onclick = function() {
    document.getElementById('popupModal').style.display = 'none';
  };
  document.getElementById('popupModal').onclick = function(event) {
    if (event.target === this) {
      this.style.display = 'none';
    }
  };
});
</script>

</body>
</html>
