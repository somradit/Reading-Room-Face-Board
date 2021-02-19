<?php
///////////////////////////////////////////////////////////////
///////////////////Per Room Variables//////////////////////////
///////////////////////////////////////////////////////////////

//Room Name
$pageTitle = "";

//Room Coordinator Info
$coordinator = false;
$coordinatorName = "";
$coordinatorPicture = "";

//Which Amion calendars to query
$calendars = array("",);

//First row of people/shifts
$firstRow = array();

$firstRow[] = array(
		"Title" => "Attendings",
		"Shifts" => array("", "")
		);
$firstRow[] = array(
		"Title" => "Fellows",
		"Shifts" => array("",)
		);
		
//Second row of people/shifts
$secondRow = array();

$secondRow[] = array(
		"Title" => "Residents",
		"Shifts" => array("",)
		);

$secondRow[] = array(
		"Title" => "APPs",
		"Shifts" => array("",)
		);

require('PageTemplate.php');
?>