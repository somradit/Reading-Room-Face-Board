<!DOCTYPE html>
<html>
<body>
<link rel="stylesheet" href="./readingroom.css">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
<?php
//Room Variables
include('roomVariables.php');
//Get Residents on Leave
$residentLeaveShifts = array("ADMIN","CONF","VACATION","SICK");
$residentLeaveShiftsList = implode(",",$residentLeaveShifts);
$residentLeaveURL = buildQuery("ADMIN",array("uwradcall"));
$residentLeaveData = queryAmion($residentLeaveURL);
$residentsOnLeave = getShiftNetids($residentLeaveShifts, $residentLeaveData);


//Get all shifts in a string for Amion query
$shiftList = (getRowShiftNames($firstRow).",".getRowShiftNames($secondRow));

//Build the Amion query URL
$amionURL = buildQuery($shiftList, $calendars);

$amionData = queryAmion($amionURL);

//Output the header row
headerRow($pageTitle);
//Output the two people rows
makeRow($firstRow, True);
makeRow($secondRow);


//Implode the shift arrays
function getShiftNames($array){
	return implode(",",$array['Shifts']);
}
function getRowShiftNames($row){
	return implode(', ', array_map('getShiftNames', $row));
}

//Build query
function buildQuery($shifts, $calendars){

	$calendarString = "'" . implode("' '", $calendars) . "'";

	$query = http_build_query([
	 'Lo' => 'uwarad',
	 'Rpt' => '741',
	 'TSAssignments' => '1',
	 'TSIDs' => '1',
	 'TSBegin' => date("m-d-Y"),
	 'TSEnd_days' => '1',
	 'TSShift_Filter' => $shifts,
	 'TSGroup_Filter' => $calendarString,
	 'TSTSV export' => '1'
	]);

	$amionURL = "https://www.amion.com/cgi-bin/ocs?".$query;
	
	return $amionURL;
}

//Query Amion for the people on the selected shift today
//Returns array of amion shift data in multidim array [person](calendar, name, netid, date, shift)
function queryAmion($amionURL){

	$shifts = file_get_contents($amionURL);

	$lines = explode(PHP_EOL, $shifts);

	$amionData = array();
	//convert TSV to array
	foreach ($lines as $line) {
		$amionData[] = str_getcsv($line,"\t");
	}
	//remove headers
	unset($amionData[0],$amionData[1],$amionData[2]);
	//Reset array index
	$amionData = array_values($amionData);

	return $amionData;
}

//Search the Amion data array for the people on the listed shifts. Return their NetIDs.
function getShiftNetids($shiftsArray, $amionData){
	$netids = array();
	foreach($shiftsArray as &$fac){
		$peopleOnShift = array_keys(array_column($amionData,4), $fac, true);
		foreach($peopleOnShift as &$personOnShift){
			$netids[] = $amionData[$personOnShift][2];
		}
	}
	
	return $netids;
}

//Count the number of people in each group to set the grid spacing properly
function makeOuterGridTemplate($items){
	foreach ($items as &$item){
		$template .= $item ."fr ";
	}
	return $template;
}


function createPersonBox($classifcation, $people){
	global $residentsOnLeave;
	
	
	//Get Pictures
	$response = file_get_contents('http://rad.washington.edu/wp-json/people/v1/all');
	$response = json_decode($response);

	
	$personBox = "";
	$personBox .= "<div id='class-grid'>";
	$personBox .= "<div class='header'>".$classifcation."</div>";
	$personBox .= "<div id='inner-grid'>";
	
	//Array to track if any of the shifts were populated
	$keys = array();
	foreach ($people as &$person){
		
		//Don't display residents on leave
		if(!in_array($person, $residentsOnLeave)){
			$key = array_search($person, array_column($response, 'post_title'));
			$keys[] = $key;
			if($key){
				$pictureSrc = $response[$key]->picure;
				$firstname = $response[$key]->first_name;
				$lastname = $response[$key]->last_name;
				$suffix = $response[$key]->suffix;
				$fullname = $firstname.' '.$lastname.', '.$suffix;
				
				$personBox .= "<div class='flex-item'>";
				$personBox .= "<div class='picture'><img height='300px' src='".$pictureSrc."'></div>";
				$personBox .= "<div class='name'>".$fullname."</div>";
				$personBox .= "</div>";
			}
		}
	}
	$personBox .= "</div>";
	$personBox .= "</div>";
	//If there are people scheduled in this group today return the box for that group, else return null
	if($keys){
		return $personBox;
	};
	return null;
}

//Create a box for the reading room coordinator.
//Coordinators are not in the Amion schedule so they need to be added staticly
function coordinatorBox(){
	global $coordinatorName, $coordinatorPicture;
	
	$personBox = "";
	$personBox .= "<div id='class-grid'>";
	$personBox .= "<div class='header'>Reading Room Coordinator</div>";
	$personBox .= "<div id='inner-grid'>";
	$personBox .= "<div class='flex-item'>";
	$personBox .= "<div class='picture'><img height='300px' src='".$coordinatorPicture."'></div>";
	$personBox .= "<div class='name'>".$coordinatorName."</div>";
	$personBox .= "</div>";
	$personBox .= "</div>";
	$personBox .= "</div>";
	return $personBox;
}

//Ouput the people on the listed shifts, optionally add the coordinator if enabled
function makeRow($row, $coordinator=False){
	global $amionData, $residentsOnLeave;
	$groups = array_column($row, 'Shifts');
	$headers = array_column($row, 'Title');
	
	$counts = array();
	$i = 0;
	foreach($groups as &$group){
		$netids = getShiftNetids($group, $amionData);
		$netids = array_diff($netids, $residentsOnLeave);
		$personBox = createPersonBox($headers[$i], $netids);
		if($personBox){
			$innerrow .= $personBox;
			$counts[]  = count($netids);
		}
		$i++;
	}
	$gridTemplate = makeOuterGridTemplate($counts);
	if($coordinator){
		$gridTemplate .= " 1fr";
	}
	$rowOut .= "<div id='row-grid' style='grid-template-columns:".$gridTemplate."'>";
	$rowOut .= $innerrow;
	if($coordinator){
		$rowOut .= coordinatorBox();
	}
	$rowOut .= "</div>";
	echo $rowOut;
}

//Header Row
function headerRow($pageTitle){
	echo("<div id='row-grid'><div id='room-name'>".$pageTitle." - ".date("l\, F jS\, Y")."</div></div>");
}
?>
</body>
</html>