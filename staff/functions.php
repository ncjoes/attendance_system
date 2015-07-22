<?php
require_once('../Utility.php');

function getPendingRequests(Staff &$staff) {
    //Get all pending request for this staff
	$rows = array();
    if (isset($staff)) {
        /* Your code goes here */
		$link = Utility::getDefaultDBConnection();
		$query = "select * from leave_requests where staff_id='".$staff->getID()."' and is_approved='PENDING'";
		$result = mysqli_query($link, $query) or die(mysqli_error($link));
		if($result){
			while($row = mysqli_fetch_array($result)){
				$rows[] = $row;
			}
		}
    }
    return $rows;
}

function getApprovedRequests(Staff &$staff) {
    //Get all approved request for this staff
	$rows = array();
    if (isset($staff)) {
        /* Your code goes here */
		$link = Utility::getDefaultDBConnection();
		$query = "select * from leave_requests where staff_id='".$staff->getID()."' and is_approved='APPROVED'";
		$result = mysqli_query($link, $query) or die(mysqli_error($link));
		if($result){
			while($row = mysqli_fetch_array($result)){
				$rows[] = $row;
			}
		}
    }
    return $rows;
}

function getDisapprovedRequests(Staff &$staff) {
    //Get all disapproved requests for this staff
	$rows = array();
    if (isset($staff)) {
		$link = Utility::getDefaultDBConnection();
		$query = "select * from leave_requests where staff_id='".$staff->getID()."' and is_approved='DISAPPROVED'";
		$result = mysqli_query($link, $query) or die(mysqli_error($link));
		if($result){
			while($row = mysqli_fetch_array($result)){
				$rows[] = $row;
			}
		}
    }
    return $rows;
}

function getAttendance(Staff &$staff, $date) {
    //Return attendance records on or before the given date for this staff
	$rows = array();
    if (isset($staff)) {
		$link = Utility::getDefaultDBConnection();
		if(isset($date)){
			$query = "select * from attendance where staff_id='".$staff->getID()."' and day<='$date'";
		}else{
			$query = "select * from attendance where staff_id='".$staff->getID()."'";
		}
		$result = mysqli_query($link, $query) or die(mysqli_error($link));
		if($result){
			while($row = mysqli_fetch_array($result)){
				$rows[] = $row;
			}
		}
    }
    return $rows;
}

function getTotalHoursWorked(Staff $staff, $start_date, $end_date) {
    //Return total working hours for this staff within the given dates
    if (isset($staff)) {
        /* Your code goes here */
    }
    return 0;
}
