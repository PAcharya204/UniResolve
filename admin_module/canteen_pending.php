<?php
include('db.php');
session_start();

// Admin session check
if (!isset($_SESSION['username'])) {
    header("Location: /Complaint_Portal/landing.html");
    exit();
}

$admin_name = $_SESSION['username'];
$profile_image = "image/adminProfile.png";

// Fetch pending canteen complaints
$sql = "SELECT * FROM complaints WHERE category='Canteen' AND status='Pending' ORDER BY complaint_id DESC";
$result = mysqli_query($conn, $sql);

// Uploads folder
$uploadsFolderWeb = '/Complaint_Portal/uploads/';
$uploadsFolderServer = $_SERVER['DOCUMENT_ROOT'] . '/Complaint_Portal/uploads/';

// Handle AJAX Solution Submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaintId'])){
    $complaintId = intval($_POST['complaintId']);
    $solution = mysqli_real_escape_string($conn, $_POST['solution']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $stmt = $conn->prepare("UPDATE complaints SET solution=?, status=? WHERE complaint_id=?");
    $stmt->bind_param("ssi", $solution, $status, $complaintId);
    if($stmt->execute()){
        echo 'success';
    } else {
        echo 'error';
    }
    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Canteen Pending Complaints</title>
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
  gap: 12px;
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
    /* margin-bottom: 25px; */
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
  padding: 6px;
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

.contentSection .viewBtn:hover,.contentSection .solutionBtn:hover {
  transform: translateY(-2px);
  background-color:rgba(20, 223, 172, 1);
  color: black;
}
/* @media(max-width:768px){table,th,td{font-size:14px;}button{padding:5px 10px;}} */

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
  scrollbar-width: none;
  background: #232e3c; /* changed from #fff */
  color: #E5E7EB;      /* changed from #111827 */
  padding: 28px;
  border-radius: 14px;
  max-width: 520px;
  width: 98%;
  font-size: 1.08rem;
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  max-height: 80vh;
  overflow-y: auto;
  position: relative;
}

.closeBtnView,
.closeBtnSolution {
  position: absolute;
  top: 8px;
  right: 12px;
  background: none;
  border: none;
  color: white;
  font-size: 22px;
  font-weight: bold;
  cursor: pointer;
  padding: 0;
  margin: 0;
}
/* #complaintDetails table{width:100%;border-collapse:collapse;}
#complaintDetails th, #complaintDetails td{padding:10px;border:1px solid #ddd;}
#complaintDetails th{background:#4b79a1;color:white;text-align:left;}
#complaintDetails td{background:#f9f9f9;} */

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

  .modal-content {
    margin-left: 20px;
    margin-right: 20px;
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

  .contentSection p {
    font-size: 0.85rem;
    text-align: center;
    margin: 0 auto;
    overflow: hidden;
  }

  .modal-content {
    margin-left: 20px;
    margin-right: 20px;
  }
}


#solutionSubmit {
  display: block;
  margin: 18px auto 0 auto;
  background: greenyellow ;
  color: black ;
  font-weight: bold;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(3,191,175,0.15);
  transition: background 0.3s, color 0.3s, transform 0.2s;
}
#solutionSubmit:hover {
  background: green;
  color: white ;
  transform: translateY(-2px) scale(1.05);
}

#successModal .modal-content {
  width: 260px;
  margin: 0 auto;
  padding: 18px;
  border-radius: 12px;
  text-align: center;
}
#successModal .modal-content button {
  display: block;
  margin: 18px auto 0 auto;
  background: linear-gradient(90deg, #03bfaf 0%, #020267 100%);
  color: #fff;
  font-weight: bold;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  padding: 10px 32px;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(3,191,175,0.15);
  transition: background 0.3s, color 0.3s, transform 0.2s;
}
#successModal .modal-content button:hover {
  background: linear-gradient(90deg, #020267 0%, #03bfaf 100%);
  color: #fff;
  transform: translateY(-2px) scale(1.05);
}
</style>
</head>
<body>

<header>
  <img id="mainLogo" src="LOGO1.png" alt="Logo">
  <p>Admin Dashboard</p>
  <a href="ad_signout.php"><img id="signOut" src="signout.jpg" alt="Sign Out" ></a>
</header>

<div class="container">
  <div class="sideBar">
    <div class="profileImg">
      <img src="admin.png" alt="Profile">
      <p><?php echo htmlspecialchars($admin_name); ?></p>
    </div>
    <div class="sidebarButton">
      <a href="ad_dashboard.php">Dashboard</a><br>
      <!-- <a href="canteen_pending.php" class="active">Canteen Complaints</a> -->
       <a href="user_details.php">User Details</a><br>
      <a href="ad_details.php">Department Admin Details</a>
    </div>
  </div>

  <div class="contentSection">
    <p>Canteen - Pending Complaints</p>
    <table>
      <tr>
        <th>ID</th>
        <th>User Name</th>
        <th>Title</th>
        <th>Status</th>
        <th>Date</th>
        <th>File</th>
        <th>Actions</th>
      </tr>
      <?php
      if(mysqli_num_rows($result) > 0){
          while($row = mysqli_fetch_assoc($result)){
              $fileName = basename($row['file']);
              $serverPath = $uploadsFolderServer . $fileName;
              $webPath = $uploadsFolderWeb . $fileName;

              $fileLink = (!empty($row['file']) && file_exists($serverPath))
                  ? '<a href="'.htmlspecialchars($webPath).'" download style="color:#1976d2;font-weight:bold;text-decoration:none;">Download File</a>'
                  : 'No file uploaded';

              echo "<tr>
                      <td>{$row['complaint_id']}</td>
                      <td>".htmlspecialchars($row['name'])."</td>
                      <td>".htmlspecialchars($row['title'])."</td>
                      <td>".htmlspecialchars($row['status'])."</td>
                      <td>".htmlspecialchars($row['t_date'])."</td>
                      <td>{$fileLink}</td>
                      <td>
                        <button class='viewBtn'
                          data-name='".htmlspecialchars($row['name'])."'
                          data-email='".htmlspecialchars($row['email'])."'
                          data-roll='".htmlspecialchars($row['roll_no'])."'
                          data-category='".htmlspecialchars($row['category'])."'
                          data-title='".htmlspecialchars($row['title'])."'
                          data-complaint='".htmlspecialchars($row['complaint'])."'
                          data-solution='".htmlspecialchars($row['solution'])."'
                          data-status='".htmlspecialchars($row['status'])."'
                          data-file='".(!empty($row['file']) && file_exists($serverPath) ? $webPath : '')."'
                        >View</button>
                        <button class='solutionBtn'
                          data-id='{$row['complaint_id']}'
                          data-name='".htmlspecialchars($row['name'])."'
                          data-email='".htmlspecialchars($row['email'])."'
                          data-roll='".htmlspecialchars($row['roll_no'])."'
                          data-category='".htmlspecialchars($row['category'])."'
                          data-title='".htmlspecialchars($row['title'])."'
                          data-complaint='".htmlspecialchars($row['complaint'])."'
                          data-solution='".htmlspecialchars($row['solution'])."'
                          data-status='".htmlspecialchars($row['status'])."'
                          data-file='".(!empty($row['file']) && file_exists($serverPath) ? $webPath : '')."'
                        >Solution</button>
                      </td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='7' class='no-record'>No pending complaints found</td></tr>";
      }
      ?>
    </table>
  </div>
</div>

<!-- View Modal -->
<div class="modal" id="viewModal">
  <div class="modal-content">
    <span class="closeBtnView">&times;</span>
    <h3><u><center>Complaint Details</center></u></h3>
    <p><strong>User Name:</strong> <span id="viewUser"></span></p>
    <p><strong>Email:</strong> <span id="viewEmail"></span></p>
    <p><strong>Roll No:</strong> <span id="viewRoll"></span></p>
    <p><strong>Category:</strong> <span id="viewCategory"></span></p>
    <p><strong>Title:</strong> <span id="viewTitle"></span></p>
    <p><strong>Complaint:</strong> <span id="viewComplaint"></span></p>
    <p><strong>Solution:</strong> <span id="viewSolution"></span></p>
    <p><strong>Status:</strong> <span id="viewStatus"></span></p>
    <p><strong>File:</strong> <span id="viewFile"></span></p>
  </div>
</div>

<!-- Solution Modal -->
<div class="modal" id="solutionModal">
  <div class="modal-content">
    <span class="closeBtnSolution">&times;</span>
    <h3><u><center>Update Solution</center></u></h3>
    <form id="solutionForm">
      <input type="hidden" name="complaintId" id="solutionComplaintId">
      <p><strong>User Name:</strong> <span id="solutionUser"></span></p>
      <p><strong>Email:</strong> <span id="solutionEmail"></span></p>
      <p><strong>Roll No:</strong> <span id="solutionRoll"></span></p>
      <p><strong>Category:</strong> <span id="solutionCategory"></span></p>
      <p><strong>Title:</strong> <span id="solutionTitle"></span></p>
      <p><strong>Complaint:</strong> <span id="solutionComplaint"></span></p>
      <p><strong>File:</strong> <span id="solutionFile"></span></p><br>
      <label style="font-weight: bold;">Solution</label><br>
      <textarea rows="4" cols="50" name="solution" id="solutionText" required ></textarea><br><br>
      <label style="font-weight: bold;">Status</label><br>
      <select name="status" id="solutionStatus" style="width: 30%;height: 30px;height: 30px; font-size: 1rem; ">
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
        <!-- <option value="Rejected">Rejected</option> -->
      </select><br><br>
      <input id="solutionSubmit" type="submit" value="Save Solution">
    </form>
  </div>
</div>

<!-- Success Modal -->
<div class="modal" id="successModal">
  <div class="modal-content">
    <h3>✅ Solution saved successfully!</h3>
    <button onclick="document.getElementById('successModal').style.display='none'">OK</button>
  </div>
</div>

<script>
// --- View Modal ---
const viewModal = document.getElementById('viewModal');
const closeBtnView = document.querySelector('.closeBtnView');
document.querySelectorAll('.viewBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('viewUser').textContent = btn.dataset.name;
        document.getElementById('viewEmail').textContent = btn.dataset.email;
        document.getElementById('viewRoll').textContent = btn.dataset.roll;
        document.getElementById('viewCategory').textContent = btn.dataset.category;
        document.getElementById('viewTitle').textContent = btn.dataset.title;
        document.getElementById('viewComplaint').textContent = btn.dataset.complaint;
        document.getElementById('viewSolution').textContent = btn.dataset.solution;
        document.getElementById('viewStatus').textContent = btn.dataset.status;
        document.getElementById('viewFile').innerHTML = btn.dataset.file ? `<a href="${btn.dataset.file}" download style="color:#1976d2;font-weight:bold;text-decoration:none;">Download File</a>` : 'No file uploaded';
        viewModal.style.display = 'flex';
    });
});
closeBtnView.onclick = function() {
    viewModal.style.display = 'none';
};
viewModal.onclick = function(e) {
    if (e.target === viewModal) {
        viewModal.style.display = 'none';
    }
};
// --- Solution Modal ---
const solutionModal = document.getElementById('solutionModal');
const closeBtnSolution = document.querySelector('.closeBtnSolution');
document.querySelectorAll('.solutionBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('solutionComplaintId').value = btn.dataset.id;
        document.getElementById('solutionUser').textContent = btn.dataset.name;
        document.getElementById('solutionEmail').textContent = btn.dataset.email;
        document.getElementById('solutionRoll').textContent = btn.dataset.roll;
        document.getElementById('solutionCategory').textContent = btn.dataset.category;
        document.getElementById('solutionTitle').textContent = btn.dataset.title;
        document.getElementById('solutionComplaint').textContent = btn.dataset.complaint;
        document.getElementById('solutionText').value = '';
        document.getElementById('solutionStatus').value = btn.dataset.status;
        const fileSpan = document.getElementById('solutionFile');
        fileSpan.innerHTML = btn.dataset.file ? `<a href="${btn.dataset.file}" download style="color:#1976d2;font-weight:bold;text-decoration:none;">Download File</a>` : 'No file uploaded';
        solutionModal.style.display = 'flex';
    });
});
closeBtnSolution.onclick = function() {
    solutionModal.style.display = 'none';
};
solutionModal.onclick = function(e) {
    if (e.target === solutionModal) {
        solutionModal.style.display = 'none';
    }
};
// --- AJAX Submission ---
document.getElementById('solutionForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('', { method:'POST', body:formData })
        .then(res => res.text())
        .then(data => {
            if(data.trim() === 'success'){
                solutionModal.style.display='none';
                document.getElementById('successModal').style.display='flex';
                const id = document.getElementById('solutionComplaintId').value;
                document.querySelector(`button.solutionBtn[data-id='${id}']`).closest('tr').cells[3].textContent = document.getElementById('solutionStatus').value;
                document.querySelector(`button.solutionBtn[data-id='${id}']`).closest('tr').cells[2].textContent = document.getElementById('solutionText').value; // update solution column if needed
            } else {
                alert('❌ Error saving solution.');
            }
        })
        .catch(err => console.error(err));
});

const successModal = document.getElementById('successModal');
successModal.onclick = function(e) {
    if (e.target === successModal) {
        successModal.style.display = 'none';
    }
};
</script>

</body>
</html>
