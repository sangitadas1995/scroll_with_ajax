<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$GLOBALS['conn'] = mysqli_connect("localhost", "root", "password", "demo_large_sql");
	if(!$GLOBALS['conn']) { die("ERROR: Could not connect. " . mysqli_connect_error()); }


$postData = (isset($_POST) && !empty($_POST))?$_POST:'';
if(!empty($postData)) {

	switch ($postData['method']) {

		case 'searchData':
			if(!empty($postData['data'])) {

				$limit = !empty($postData['limit'])?$postData['limit']:10;
				$offset = !empty($postData['offset'])?$postData['offset']:0;
				search($postData['data'],$limit,$offset);
				break;
			} else {
				
				getAllUsers();
				break;
			}
		
		default:
			getAllUsers();
			break;
	}

} else {
	getAllUsers();
}

	function getAllUsers() {
		$sql = "SELECT * FROM user_details ORDER BY user_id ASC LIMIT 20 OFFSET 0"; 
	    //SQL select query
	    $result = mysqli_query($GLOBALS['conn'],$sql);
		// get number of rows returned
		$result = mysqli_query($GLOBALS['conn'],$sql);
		$finalArr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($finalArr, $row);
		}

		$response = array('status'=> TRUE, 'details' => $finalArr);
		echo json_encode($response);die;
	}

	function search($search, $limit, $offset) {
		$sql = '';
		$sql .= " SELECT * FROM user_details WHERE username LIKE '".$search."%' OR first_name LIKE '".$search."%' OR last_name LIKE '%".$search."'";

		$sql .= " ORDER BY user_id ASC LIMIT ".$limit." OFFSET ".$offset;
		// echo $sql;die;
	 	//SQL select query
	    $result = mysqli_query($GLOBALS['conn'],$sql);
	    $finalArr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($finalArr, $row);
		}

		$response = array('status'=> TRUE, 'details' => $finalArr);
		echo json_encode($response);die;
	}


?>