<?php
///////////////////////////////////////////////////////////////
///////////////////Per Room Variables//////////////////////////
///////////////////////////////////////////////////////////////
//Room Name
$pageTitle = "HMC Neuroradiology Reading Room";

//Room Coordinator Info
$coordinatorName = "Rafael Regan";
$coordinatorPicture = "https://rad.washington.edu/wp-content/uploads/2021/01/rafael.jpg";

//Which Amion calendars to query
$calendars = array("neurorad", "uwradcall");

//First row of people/shifts
$firstRow = array();

$firstRow[] = array(
		"Title" => "Faculty",
		"Shifts" => array("HMC Att RR - Early", "HMC Att RR - Late")
		);
		
$firstRow[] = array(
		"Title" => "Fellows",
		"Shifts" => array("HMC Fellow R1","HMC Fellow R2", "HMC Fellow A1")
		);
		
//Second row of people/shifts
$secondRow = array();

$secondRow[] = array(
		"Title" => "Spine Residents",
		"Shifts" => array("HSP")
		);
		
$secondRow[] = array(
		"Title" => "Neuroradiology Residents",
		"Shifts" => array("HN")
		);
$secondRow[] = array(
		"Title" => "Float Residents",
		"Shifts" => array("HFC")
		);
$secondRow[] = array(
		"Title" => "Teaching Associate",
		"Shifts" => array("HMC Teaching Associate")
		);
?>