<?php
require_once("config/db.php");
$recent_action = "Last action status";

// Actions:
$action = ($_POST["action"]);
if ($action=='deletenote'){
	$id = $_POST["id"];
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "DELETE FROM notes WHERE id=$id";

if ($conn->query($sql) === TRUE) {
$recent_action = $action . " successfully processed"; 
  
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

}

else if ($action=='postnote'){
    $priority = $_POST["priority"];
    $note = $_POST["note"];
	$title = $_POST["title"];
	$author = $_POST["author"];
	$date = $_POST["date"];
	$time = $_POST["time"];
	$id = $_POST["id"];
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "INSERT INTO notes (priority,title, author, blogpost,date,time)
VALUES ('$priority','$title', '$author', '$note', '$date', '$time')";

if ($conn->query($sql) === TRUE) {
$recent_action = $action . " successfully processed";

   
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();


}


else if ($action=='emptyerrors'){
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$sql="TRUNCATE TABLE site_errors";
$recent_action = $action . "  successfully processed";   
mysqli_query($con,$sql);
mysqli_close($con);
}

else if ($action=='setstatus'){

	$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
	$status = $_POST["status"];
	$id = $_POST["id"];
$sql="UPDATE notes SET status='$status' WHERE id=$id";
$recent_action = $action . "  successfully processed";  

mysqli_query($con,$sql);
mysqli_close($con);
}

// Count results for each category
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$sql="SELECT * FROM site_errors";
if ($result=mysqli_query($con,$sql))
 {
$rowcount_errors=mysqli_num_rows($result);
mysqli_free_result($result);
  }
$sql="SELECT * FROM main";
if ($result=mysqli_query($con,$sql))
 {
$rowcount_allposts=mysqli_num_rows($result);
mysqli_free_result($result);
  }
$sql="SELECT * FROM notes";
if ($result=mysqli_query($con,$sql))
 {
$rowcount_notes=mysqli_num_rows($result);
mysqli_free_result($result);
  }
  
mysqli_close($con);
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<title>DEV Dashboard</title>
<link href="style/main.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="//code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript">
function showonlyone(thechosenone) {
     $('.boxes').each(function(index) {
          if ($(this).attr("id") == thechosenone) {
            //   $(this).show();
			        $(this).animate({height: 'toggle'},200)}
          else {
               $(this).hide();
          }
});
}
</script>
</head>
<body><br>

<!-- Container beginning -->
<div class=container>

<!-- Header -->
<div class=header>
<?php
$domain = $_SERVER['HTTP_HOST'];
?>
<p align="right"><h2 align="right"><?php echo $domain; ?> Dashboard&nbsp;</h2></p>
</div>
<!-- /Header -->

<!-- Top menu -->
<div class=menu_top >
<p align="right"><h4 align="right"><?php echo $recent_action ?>&nbsp;</h4></p>
</div>
<!-- /Top menu -->


<div class=main_content>
<!-- Dashboard -->
<div class="dashboard">
<b>Dashboard</b>
<?php echo $hoi ?>
<br>
Logged in as <? echo $_SERVER['PHP_AUTH_USER'] ?><br>
</div>
<!-- /Dashboard -->

<!-- Container menu -->
<a href="javascript:showonlyone('boxes2');" style="padding:0px 3px;padding-left:6px;"><font class="small">Recent posts</a>
<a href="javascript:showonlyone('boxes5');" style="padding:0px 3px;padding-left:6px;"><font class="small">All posts (<?php echo  $rowcount_allposts; ?>)</a>
<a href="javascript:showonlyone('boxes1');" style="padding:0px 3px;padding-left:6px;"><font class="small">Notes (<?php echo  $rowcount_notes; ?>)</a>
<a href="javascript:showonlyone('boxes3');" style="padding:0px 3px;padding-left:6px;"><font class="small">Site errors (<?php echo  $rowcount_errors; ?>)</a>
<a href="javascript:showonlyone('boxes4');" style="padding:0px 3px;padding-left:6px;"><font class="small">Site status</a>

<a href="javascript:showonlyone('boxes6');" style="padding:0px 3px;padding-left:6px;"><font class="small">Visitors</a>

<a href="javascript:showonlyone('boxes7');" style="padding:0px 3px;padding-left:6px;"><font class="small">Users</a>
<!-- /Container menu -->

<!--Notes -->
<div class="boxes" id="boxes1">
<table>
<tr><td  width="70%">    
<h3>Notes</h3>
<?php
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT * FROM notes ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {
?>



<b><?php echo  $row["title"]; ?> @ <?php echo  $row["time"]; ?> UTC+1 - <?php echo  $row["date"]; ?> by <?php echo  $row["author"]; ?></b>
 <br><br>
 <?php echo  $row["blogpost"]; ?>
  <br><br>
   
<?php
/* Isn't in use at the moment
if ($row["status"]==0){
	$status_desc = 'Open';
}
else if ($row["status"]==1){
	$status_desc = 'In progress';
}
else if ($row["status"]==2){
	$status_desc = 'Completed';
}
*/
?>
<table> <!-- Table preventing line break after /forms -->
<tr><td>
<b>Status:</b>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="action" value="setstatus" >
<select name="status">
 				<option value="0" <?php if ($row["status"]==0) { echo 'selected'; } ?>>Open</option>
                <option value="1" <?php if ($row["status"]==1) { echo 'selected'; } ?>>In progress</option>
                <option value="2" <?php if ($row["status"]==2) { echo 'selected'; } ?>>Completed</option>
             </select>
<input type="submit" value="Set status" onclick="return confirm('Are you sure you want to update the status?')">
</form>
</td><td>
<b>Priority:</b>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"}>
<input type="hidden" name="action" value="setpriority" >
<select name="priority">
 				<option value="0" <?php if ($row["priority"]==0) { echo 'selected'; } ?>>Not set</option>
                <option value="1" <?php if ($row["priority"]==1) { echo 'selected'; } ?>>Very high</option>
                <option value="2" <?php if ($row["priority"]==2) { echo 'selected'; } ?>>High</option>
                <option value="3" <?php if ($row["priority"]==3) { echo 'selected'; } ?>>Medium</option>
				<option value="4" <?php if ($row["priority"]==4) { echo 'selected'; } ?>>Low</option>
				<option value="5" <?php if ($row["priority"]==5) { echo 'selected'; } ?>>Very low</option>
			 </select>
<input type="submit" value="Update" onclick="return confirm('Are you sure you want to update the priority?')">
</form>
</td><td>
<b>Other:</b>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="action" value="deletenote" >
  <input type="hidden" name="id" value="<?php echo $row["id"]; ?>" >
<input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this note?')">
 </form>
 </td>

</tr></table>
<br>
 
  <br>
  <b>
  </b>
  <br>
  <?
}
} else {
    echo "0 results";
}
mysqli_close($conn);
$date= date("Y.m.d");
?>
<?
$time =  date("H:i:s");
?>

</td>
<td style="vertical-align: text-top">
<h3>Actions</h3>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
     <input type="hidden" name="action" value="removeallnotes" >
    <input type="submit" value="Delete all notes" onclick="return confirm('Are you sure you want to delete all notes?')" disabled>
</form><br>
<b>Add note</b><br>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 <input type="hidden" name="action" value="postnote" >
 <input type="hidden" name="author" value="<? echo $_SERVER['PHP_AUTH_USER'] ?>" >
 <input type="hidden" name="date" value="<? echo $date ?>" >
 <input type="hidden" name="time" value="<? echo $time ?>" >
 Title<br>
<input type="textarea" name="title" id="title"><br>
Priority<br>
<select name="priority">
                <option value="1">Very High</option>
                <option value="2">High</option>
                <option value="3">Medium</option>
                <option value="4">Low</option>
                <option value="5">Very low</option>
                </select><br>
        
Note<br>
 <textarea  rows="4" cols="50" name="note" id="note"></textarea><br>
 <input type="submit">
 </form>
</td></tr>
<br><br>
</table>
</div>
<!--/Notes -->

<!-- Recent posts -->
<div class="boxes" id="boxes2">
<?php
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT * FROM main ORDER BY id DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {
$blogpost = $row["blogpost"];
// Shortens the blogpost output to 100 chars
if (strlen($blogpost) > 100) {
$blogpost_short = substr($blogpost, 0, 100);
$blogpost = substr($blogpost_short, 0, strrpos($blogpost_short, ' ')).''; 
}
?>
Title: <?php echo  $row["title"]; ?> @ <?php echo  $row["time"]; ?> UTC+1 - <?php echo  $row["date"]; ?> by <?php echo  $row["author"]; ?> postid: <?php echo  $row["id"]; ?> <br>
  <?php echo  $blogpost; ?>
  <br>
  <b>
  <a href="post.php?action=read&id=<?php echo $row["id"]; ?>">Read</a>
  <a href="post.php?action=edit&id=<?php echo $row["id"]; ?>"> Edit</a>
  <a href="post.php?action=delete&id=<?php echo $row["id"]; ?>"> Delete</a>
  </b>
  <br><br>
  <?
  
    }
} else {
    echo "0 results";
}

mysqli_close($conn);
?>
</div>
<!-- /Recent posts -->

<!-- All posts -->
<div class="boxes" id="boxes5">
<?php
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT * FROM main ORDER BY id DESC ";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {
$blogpost = $row["blogpost"];
// Shortens the blogpost output to 100 chars
if (strlen($blogpost) > 100) {
$blogpost_short = substr($blogpost, 0, 100);
$blogpost = substr($blogpost_short, 0, strrpos($blogpost_short, ' ')).''; 
}
?>
Title: <?php echo  $row["title"]; ?> @ <?php echo  $row["time"]; ?> UTC+1 - <?php echo  $row["date"]; ?> by <?php echo  $row["author"]; ?> postid: <?php echo  $row["id"]; ?> <br>
  <?php echo  $blogpost; ?>
  <br>
  <b>
  <a href="post.php?action=read&id=<?php echo $row["id"]; ?>">Read</a>
  <a href="post.php?action=edit&id=<?php echo $row["id"]; ?>"> Edit</a>
  <a href="post.php?action=delete&id=<?php echo $row["id"]; ?>"> Delete</a>
  </b>
  <br><br>
  <?
  
    }
} else {
    echo "0 results";
}

mysqli_close($conn);
?>
</div>
<!-- /All posts -->

<!-- Site errors -->
<div class="boxes" id="boxes3">
<table>
<tr><td td width="50%">  
<h3>25 Last errors</h3><br>
<?php
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT * FROM site_errors ORDER BY id DESC LIMIT 25";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {
$browser = $row["browser"];

if (strlen($browser) > 100) {
$browser_short = substr($browser, 0, 100);
$browser = substr($browser_short, 0, strrpos($browser_short, ' ')).''; 
}
?>
<br>
<a href="https://httpstatuses.com/<?php echo  $row["errorcode"]; ?>" target="_blank"><?php echo  $row["errorcode"]; ?></a> @ <?php echo  $row["time"]; ?> UTC+1 - <?php echo  $row["date"]; ?>. 
<br>
IP: <a href="http://www.ip-tracker.org/locator/ip-lookup.php?ip=<?php echo  $row["ip"]; ?>" target="_blank"><?php echo  $row["ip"]; ?></a><br>
Location: <?php echo  $row["location"]; ?><br>
Browser: <?php echo  $browser; ?><br>
Redir: <?php echo  $row["redir"]; ?>
<br>
<br>

  <?
}
} else {
    echo "0 errors in DB";
}
mysqli_close($conn);

?>
</td>
<td width="50%" style="vertical-align: text-top">
    <h3>Actions</h3>
<br>


<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="action" value="emptyerrors">
<?php
if ($rowcount_errors == 0){
	
}
else
{
	?>
    <input type="submit" value="Clear errors" onclick="return confirm('Are you sure you want to delete all errors?')"> 
 <?
}
?>
</form>
<b>Wanneer er een nieuwe login procedure is; mailen bij errors?<br></b>

</td>
</tr>
</table>
<br>
<br>
</div>
<!-- /Site errors -->

<!-- Site status -->
<div class="boxes" id="boxes4">
<table>
    <tr>
        <td style="vertical-align: text-top">
<h3>Server version status</h3><br><br>
<?php
echo "PHP server version - " . phpversion(). "\n";
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
?>
<br>
<?
echo "\n mySQLi Server version -\n", $mysqli->server_info;
$mysqli->close();
?>
</td>
<td width="50%" style="vertical-align: text-top">
<h3>All sites status</h3><br><br>
www.howtobuyether.eu status: <b style="color:green;">ok</b> (werkt nog niet)
</td>
</tr>
</table>
</div>
<!-- / Site status -->

<!-- Visitors -->
<div class="boxes" id="boxes6">
<table>
    <tr>
        <td style="vertical-align: text-top">
<h3>Recent visitors</h3><br><br>
<?php echo $domain ?><br>
komt nog
</td>
<td width="50%" style="vertical-align: text-top">
<h3>Actions</h3><br><br>
<a href="https://howtobuyether.eu/piwik" target="_blank">Piwik panel</a>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="action" value="emptyvisitors">
<input type="submit" value="Clear visitors" onclick="return confirm('Are you sure you want to delete all visitors?')">

</td>
</tr>
</table>
</div>
<!-- / Visitors -->

<!-- Users -->
<div class="boxes" id="boxes7">
<table>
    <tr>
        <td style="vertical-align: text-top">
<h3>Users</h3><br><br>
<?php echo $domain ?> users<br>
komt nog
</td>
<td width="50%" style="vertical-align: text-top">
<h3>Actions</h3><br><br>

</td>
</tr>
</table>
</div>
<!-- / Users -->




<!-- Footer -->
<div class="footer">
Logged in as <? echo $_SERVER['PHP_AUTH_USER'] ?>&nbsp;<br>
</div>
<!-- /Footer -->


<!-- Container end -->
</div>
</div>
</div>
</font>
</body>
</html>