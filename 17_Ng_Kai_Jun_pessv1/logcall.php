<?php
session_start();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Energency Service System</title>
<link href="ngkaijun.css" rel="stylesheet" type="text/css">
<!-- put in your own css file as shown above -->
</head>

<body>
<script>
function validate()
{
var ngkaijunCaller=document.forms["ngkaijunLogCall"]['ngkaijunCaller'].value;
var ngkaijunContact=document.forms["ngkaijunLogCall"]['ngkaijunContact'].value;
var ngkaijunLocation=document.forms["ngkaijunLogCall"]['ngkaijunLocation'].value;
var ngkaijunType=document.forms["ngkaijunLogCall"]['ngkaijunLocation'].value;

if (!ngkaijunCaller || ngkaijunCaller == "")
{
alert("Caller Name is Required.");
return false;
}

else 
{
if (!isNaN(ngkaijunCaller))
{
alert("Only Characters are allowed");
return false;
}
}

if (!ngkaijunContact || ngkaijunContact == "")
{
alert("Contact Number is Required.");
return false;
}
else
{
if(isNaN(ngkaijunContact))
{
alert("Number only.");
return false; 
}
else
{
if(ngkaijunContact.length != 8)
{
alert("8 numbers only");
return false;
}
}
}

if (!ngkaijunLocation || ngkaijunLocation =="")
{
alert("Location is Required.");
return false;
}

if (!ngkaijunType || ngkaijunType =="")
{
alert("Description is Required.");
return false;
}
}

</script>
<?php require 'nav.php';?>
<?php require 'db_config.php';
    
//create database connection
$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	
if ($mysqli->connect_errno)
{
	die("Not able to connect to MYSQL: ".$mysqli->connect_errno);
}
	
$sql = "SELECT * FROM incidenttype";
	
if(!($stmt = $mysqli->prepare($sql)))
{
	die("Failed to run SQL Command: ".$mysqli->errno);
}
	
if (!$stmt->execute())
{
	die("result set failed: ".$stmt->errno);
}
	
if (!($resultset = $stmt->get_result()))
{
	die("No data found in resultset: ".$stmt->errno);
}
	
$incidentType; //an array variable
	
while ($row = $resultset->fetch_assoc())
{
	$incidentType[$row['incidentTypeId']] = $row['incidentTypeDesc'];
}
	
$stmt->close();

$resultset->close();
	
$mysqli->close();
	
	
?>
<fieldset>
<legend>Log Call</legend>
<form name="ngkaijunLogCall" method="post" action="dispatch.php" onSubmit="return validate();">
<table width="40%" align="center" cellpadding="5" cellspacing="5">
<tr>
<td width="50">Caller's Name:</td>
<td width="50"><input type="text" name="ngkaijunCaller" id="ngkaijunCaller"></td>
</tr>
<tr>
<td width="50">Contact Number:</td>
<td width="50"><input type="text" name="ngkaijunContact" id="ngkaijunContact"></td>
</tr>
<tr>
<td width="50">Location:</td>
<td width="50"><input type="text" name="ngkaijunLocation" id="ngkaijunLocation"></td>
</tr>
<tr>
<td width="50">Incident Type:</td>
<td width="50"><select type="text" name="ngkaijunType" id="ngkaijunType">
<?php foreach($incidentType as $key=> $value) {?>
<option value="<?php echo $key?>">
<?php echo $value ?> </option>
<?php }?>
</select>
</td>
</tr>
<tr>
<td width="50">Description</td>
<td width="50"><textarea name="incidentDesc" id="incidentDesc" cols="45" rows="5"></textarea>
</td>
</tr>
<tr>
<td><input type="reset" name="resetButton" id="resetbutton" value="Reset"></td>
<td><input type="submit" name="submitButton" id="submitButton" value="Submit" onClick="validate();"></td>
</tr>
</table>
</from>
</fieldset>
<br>
<br>
<hr>
<div align="center">
<p>&copy; 2020 Ng Kai Jun PESS System.&nbsp;&nbsp;All Right Reserved.</p>
<p>Developed and Done By: Ng Kai Jun&nbsp;&nbsp; Email me <a href="mailto:ng_kai_jun3@ite.connect.ite.edu.sg">ng_kai_jun3@ite.connect.ite.edu.sg</a></p>
</div>
</body>
</html>
