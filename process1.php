<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$GLOBALS['conn'] = mysqli_connect("localhost", "root", "password", "demo_large_sql");
	if(!$GLOBALS['conn']) { die("ERROR: Could not connect. " . mysqli_connect_error()); }


$postData = (isset($_POST) && !empty($_POST))?$_POST:'';
if(!empty($postData)) {
	$limit = !empty($postData['limit'])?$postData['limit']:10;
	$offset = !empty($postData['offset'])?$postData['offset']:0;
	switch ($postData['method']) {

		case 'searchData':
			if(!empty($postData['data'])) {

				
				search($postData['data'],$limit,$offset);
				break;
			} else {
				
				getAllUsers($limit,$offset);
				break;
			}
		
		default:
			getAllUsers($limit,$offset);
			break;
	}

} else {
	getAllUsers($limit,$offset);
}

	function getAllUsers($limit,$offset) {

		$view_more = false;
		$totalRecSql = "SELECT * FROM user_details ORDER BY user_id ASC";
		$totalRecRes = mysqli_query($GLOBALS['conn'],$totalRecSql);
		$totalRecCount = mysqli_num_rows($totalRecRes);

		$sql = "SELECT * FROM user_details ORDER BY user_id ASC LIMIT ".$limit." OFFSET ".$offset; 
	    //SQL select query
	    $result = mysqli_query($GLOBALS['conn'],$sql);
		// get number of rows returned
		$result = mysqli_query($GLOBALS['conn'],$sql);
		$finalArr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($finalArr, $row);
		}

		/* check if view more will be visible or not */
		if($totalRecCount > ($limit+$offset)) { 
			$view_more = true;
		}

		$response = array('status'=> TRUE, 'view_more'=>$view_more, 'totalRecords'=>$totalRecCount,'details' => $finalArr);
		echo json_encode($response);die;
	}

	function search($search, $limit, $offset) {

		$view_more = false;
		$totalRecSql = " SELECT * FROM user_details WHERE username LIKE '".$search."%' OR first_name LIKE '".$search."%' OR last_name LIKE '%".$search."'";
		$totalRecRes = mysqli_query($GLOBALS['conn'],$totalRecSql);
		$totalRecCount = mysqli_num_rows($totalRecRes);


		$sql = '';
		$sql .= " SELECT * FROM user_details WHERE username LIKE '".$search."%' OR first_name LIKE '".$search."%' OR last_name LIKE '%".$search."'";

		$sql .= " ORDER BY user_id ASC LIMIT ".$limit." OFFSET ".$offset;
	    $result = mysqli_query($GLOBALS['conn'],$sql);
	    $finalArr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($finalArr, $row);
		}

		/* check if view more will be visible or not */
		if($totalRecCount > ($limit+$offset)) { 
			$view_more = true;
		}

		$response = array('status'=> TRUE, 'view_more'=>$view_more, 'totalRecords'=>$totalRecCount, 'details' => $finalArr);
		echo json_encode($response);die;
	}


?>