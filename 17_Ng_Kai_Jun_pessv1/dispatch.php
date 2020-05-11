<?php
if(!isset($_POST['ngkaijunCaller']))
{
header("location: logcall.php");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
<link href="ngkaijun.css" rel="stylesheet" type="text/css"></head>
</head>
<body>
<?php require_once 'nav.php'; ?>

<?php //if post back
if (isset($_POST["btnDispatch"]))
{
require_once 'db_config.php';

// create database connection
$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
// Check connection
if ($mysqli->connect_errno)
{
die("Failed to connect to MySQL: ".$mysqli->connect_errno);
}

$patrolcarDispatched = $_POST["chkPatrolcar"]; // array of patrolcar being dispatched from post back
$numofPatrolcarDispatched = count($patrolcarDispatched);
    
// insert new incident
$incidentStatus;
if ($numofPatrolcarDispatched > 0) {
$incidentStatus='2'; //incident status to be set as Dispatched
} else {
$incidentStatus='1'; //incident status to be set as Pending
}
    
$sql = "INSERT INTO incident (callerName, phoneNumber, incidentTypeId, incidentLocation, incidentDesc, incidentStatusId) VALUES (?, ?, ?, ?, ?, ?)";

if(!($stmt = $mysqli->prepare($sql)))
{
die("Prepare failed: ".$mysqli->errno);
}
    
if (!$stmt->bind_param('ssssss', $_POST['ngkaijunCaller'], $_POST['ngkaijunContact'],
$_POST['ngkaijunType'], $_POST['ngkaijunLocation'], $_POST['incidentDesc'], $incidentStatus))
    
{
die("Binding parameters failed: ".$stmt->errno);
}

if (!$stmt->execute())
{
die("Insert incident table failed: ".$stmt->errno);
}

// retrieve incident_id for the newly inserted incident
		$incidentId=mysqli_insert_id($mysqli);;
		
		//update patrolcar status table and add into dispatch table
		for($i=0; $i < $numofPatrolcarDispatched; $i++)
			
	{
		// update patrol car status
		$sql = "Update patrolcar SET patrolcarStatusId='1' WHERE patrolcarId = ?";
		
		if (!($stmt = $mysqli->prepare($sql)))
		{
			die("Prepare failed: ".$mysqli->errno);
		}
		
		if (!$stmt->bind_param('s', $patrolcarDispatched[$i]))
		{
			die("Binding parameters failed: ".$stmt->errno);
		}
			
		if (!$stmt->execute())
		{
			die("Update patrolcar_status table failed: ".$stmt->errno);
		}
			
		//insert dispatch data
		$sql = "INSERT INTO dispatch (incidentId, patrolcarId, timeDispatched) VALUES (?, ?, NOW())";
		
		if (!($stmt = $mysqli->prepare($sql)))
		{
			die("Prepare failed: ".$mysqli->errno);
		}
			
		if (!$stmt->bind_param('ss', $incidentId,
							  		$patrolcarDispatched[$i]))
		{
			die("Binding parameters failed: ".$stmt->errno);
		}
			
		if(!$stmt->execute())
		{
			die("Insert dispatch table failed: ".$stmt->errno);
		}
	}
$stmt->close();
    
$mysqli->close();

} ?>
<form name="form1" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
<table align="center" border="1">
<tr>
<td colspan="2">Incident Detail</td>
</tr>
<tr>
<td>Caller's Name:</td>
<td><?php echo $_POST['ngkaijunCaller'] ?>
<input type="hidden" name="ngkaijunCaller" id="ngkaijunCaller"
value="<?php echo $_POST['ngkaijunCaller'] ?>"></td>
</tr>
<tr>
<td>Contact No:</td>
<td><?php echo $_POST['ngkaijunContact'] ?>
<input type="hidden" name="ngkaijunContact" id="ngkaijunContact"
value="<?php echo $_POST['ngkaijunContact'] ?>"></td>
</tr>
<tr>
<td>Location:</td>
<td><?php echo $_POST['ngkaijunLocation'] ?>
<input type="hidden" name="ngkaijunLocation" id="ngkaijunLocation" 
value="<?php echo $_POST['ngkaijunLocation'] ?>"></td>
</tr>
<tr>
<td>Incident Type:</td>
<td><?php echo $_POST['ngkaijunType'] ?>
<input type="hidden" name="ngkaijunType" id="ngkaijunType"
value="<?php echo $_POST['ngkaijunType'] ?>"></td>
</tr>
<tr>
<td>Description:</td>
<td><textarea name="incidentDesc" cols="45"
rows="5" readonly id="incidentDesc"><?php echo $_POST['incidentDesc'] ?></textarea>
<input name="incidentDesc" type="hidden"
id="incidentDesc" value="<?php echo $_POST['incidentDesc'] ?>"</td>
</tr>
</table>
<?php
require_once 'db_config.php';

$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
if ($mysqli->connect_errno) 
{
die("Failed to connect to MYSQL: ".$mysqli-->connect_errno);
}
    
$sql = "SELECT patrolcarId, statusDesc FROM patrolcar JOIN patrolcar_Status
ON patrolcar.patrolcarStatusId=patrolcar_status.StatusId
WHERE patrolcar.patrolcarStatusId='2' OR patrolcar.patrolcarStatusId='3'";

if(!($stmt = $mysqli->prepare($sql))) {
die("Prepare failed: ".$mysqli->errno);
}

if(!$stmt->execute()) {
die("Execute failed: ".$stmt->errno);
}

if (!($resultset = $stmt->get_result())) {
die("Getting result set failed: ".$stmt->errno);
}

$patrolcarArray;

while($row = $resultset->fetch_assoc()) {
$patrolcarArray[$row['patrolcarId']] = $row['statusDesc'];
}

$stmt->close();

$resultset->close();
    
$mysqli->close();
?>

<br><br><table border="1" align="center">
<tr>
<td colspan="3">Dispatch Patrolcar Panel</td>
</tr>
<?php
foreach($patrolcarArray as $key=>$value){
?>
<tr>
<td><input type="checkbox" name="chkPatrolcar[]"
value="<?php echo $key?>"></td>
<td><?php echo $key ?></td>
<td><?php echo $value ?></td>   
</tr>
</tr>
<?php  }  ?>
<tr>
<td><input type="reset" name="btnCancel" id="btnCancel" value="Reset"></td>
<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="btnDispatch" id="btnDispatch">
</td>
</tr>
</table>
</form>
<div align="center">
<p>&copy; 2020 Ng Kai Jun PESS System.&nbsp;&nbsp;All Right Reserved.</p>
<p>Developed and Done By: Ng Kai Jun&nbsp;&nbsp; Email me <a href="mailto:ng_kai_jun3@ite.connect.ite.edu.sg">ng_kai_jun3@ite.connect.ite.edu.sg</a></p>
</div>
</body>
</html>