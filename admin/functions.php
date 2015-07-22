<?php
require_once('../Utility.php');

const SORT_STAFF_TYPE_STAFF_ID = "staff_id";
const SORT_STAFF_TYPE_FIRSTNAME = "first_name";
const SORT_STAFF_TYPE_LASTNAME = "last_name";
const SORT_STAFF_TYPE_LEVEL = "level";
const ORDER_STAFF_ASC = "ASC";
const ORDER_STAFF_DESC = "DESC";

function getNumberOfActiveStaff() {
    $query = "select * from staff where is_deleted != 1 and is_suspended != 1";
    $link = Utility::getDefaultDBConnection();
    $result = mysqli_query($link, $query);
    if ($result) {
        return mysqli_num_rows($result);
    }
    //Log error
    Utility::logMySQLError($link);

    return 0;
}

function getActiveStaff() {
    $staff = array();
    $query = "select * from staff where is_deleted != 1 and is_suspended != 1";
    $link = Utility::getDefaultDBConnection();
    $result = mysqli_query($link, $query);
    if ($result) {
        while ($row = mysqli_fetch_array($result)) {
            $staff[] = $row;
        }
        sortStaff($staff, SORT_STAFF_TYPE_LASTNAME, ORDER_STAFF_ASC);
    }
    //Log error
    Utility::logMySQLError($link);

    return $staff;
}

function getNumberOfSuspendedStaff() {
    $query = "select * from staff where is_suspended = 1";
    $link = Utility::getDefaultDBConnection();
    $result = mysqli_query($link, $query);
    if ($result) {
        return mysqli_num_rows($result);
    }
    //Log error
    Utility::logMySQLError($link);

    return 0;
}

function getSuspendedStaff() {
    $suspended_staff = array();
    $query = "select * from staff where is_suspended = 1";
    $link = Utility::getDefaultDBConnection();
    $result = mysqli_query($link, $query);
    if ($result) {
        while ($row = mysqli_fetch_array($result)) {
            $suspended_staff[] = $row;
        }
    }
    //Log error
    Utility::logMySQLError($link);

    return $suspended_staff;
}

function getNumberOfDeletedStaff() {
    $query = "select * from staff where is_deleted = 1";
    $link = Utility::getDefaultDBConnection();
    $result = mysqli_query($link, $query);
    if ($result) {
        return mysqli_num_rows($result);
    }
    //Log error
    Utility::logMySQLError($link);
    return 0;
}

function getDeletedStaff() {
    $deleted_staff = array();
    $query = "select * from staff where is_deleted = 1";
    $link = Utility::getDefaultDBConnection();
    $result = mysqli_query($link, $query);
    if ($result) {
        while ($row = mysqli_fetch_array($result)) {
            $deleted_staff[] = $row;
        }
    }
    //Log error
    Utility::logMySQLError($link);
    return $deleted_staff;
}

function getStaffInfo($id) {
    $query = "select * from staff where staff_id = '$id'";
    $link = Utility::getDefaultDBConnection();
    $result = mysqli_query($link, $query);
    if ($result) {
        $row = mysqli_fetch_array($result);
        return $row;
    }
    //Log error
    Utility::logMySQLError($link);
    return array();
}

function searchStaff($search_query, $is_deleted = false, $is_suspended = false, $sort_type = null, $sort_order = null) {
    $staffs = array();
    $link = Utility::getDefaultDBConnection();
    //process query
    $fields = explode(" ", $search_query);
    $query = "select * from users where (is_deleted = " . ( $is_deleted ? "1" : "0" ) . " and "
            . "is_suspended = " . ( $is_suspended ? "1" : "0" ) . ") and "
            . "(";
    for ($count = 0; $count < count($fields); $count++) {
        $query .= "staff_id = '$fields[$count]' or "
                . "last_name like '%$fields[$count]%' or "
                . "level = '$fields[$count]' or "
                . "first_name like '%$fields[$count]%'";
        if ($count !== (count($fields) - 1)) {
            $query .= " or ";
        } else {
            $query .= ")";
        }
    }
    //Search
    $result = mysqli_query($link, $query);
    if ($result) {
        while ($row = mysqli_fetch_array($result)) {
            array_push($staffs, $row);
        }
    }
    sortStaff($staffs, $sort_type, $sort_order);
    //Log error
    Utility::logMySQLError($link);

    return $staffs;
}

function sortStaff(array &$staff, $sort_type, $sort_order) {
    if (empty($staffs) || empty($sort_type) || empty($sort_order)) {
        return;
    }

    foreach ($staffs as $key => $row) {
        $last_name[$key] = $row['last_name'];
        $first_name[$key] = $row['first_name'];
        $staff_id[$key] = $row['staff_id'];
        $level[$key] = $row['designation'];
    }

    switch ($sort_type) {
        case SORT_STAFF_TYPE_FIRSTNAME:
            array_multisort($first_name, ($sort_order == ORDER_USER_DESC ? SORT_DESC : SORT_ASC), $last_name, SORT_ASC, $level, SORT_DESC, $staffs);
            break;
        case SORT_USER_TYPE_LASTNAME:
            array_multisort($last_name, ($sort_order == ORDER_USER_DESC ? SORT_DESC : SORT_ASC), $first_name, SORT_ASC, $level, SORT_DESC, $staffs);
            break;
        case SORT_USER_TYPE_REGNO:
            array_multisort($staff_id, ($sort_order == ORDER_USER_DESC ? SORT_DESC : SORT_ASC), $last_name, SORT_ASC, $first_name, SORT_DESC, $staffs);
            break;
        case SORT_USER_TYPE_LEVEL:
            array_multisort($level, ($sort_order == ORDER_USER_DESC ? SORT_DESC : SORT_ASC), $last_name, SORT_ASC, $first_name, SORT_DESC, $staffs);
            break;
        default :
            throw new Exception("Invalid sort type");
    }
}

function getPendingRequests($staff=NULL, $date=NULL) {
    //Get all pending request for this staff
	$rows = array();
	/* Your code goes here */
	$link = Utility::getDefaultDBConnection();
	$query = getLeaveQuery('PENDING', $staff, $date);
	$result = mysqli_query($link, $query) or die(mysqli_error($link));
	if($result){
		while($row = mysqli_fetch_array($result)){
			$rows[] = $row;
		}
    }
    return $rows;
}

function getApprovedRequests($staff=NULL, $date=NULL) {
    //Get all approved request for this staff
	$rows = array();
	/* Your code goes here */
	$link = Utility::getDefaultDBConnection();
	$query = getLeaveQuery('APPROVED', $staff, $date);
	$result = mysqli_query($link, $query) or die(mysqli_error($link));
	if($result){
		while($row = mysqli_fetch_array($result)){
			$rows[] = $row;
		}
	}
    return $rows;
}

function getDisapprovedRequests($staff=NULL, $date=NULL) {
    //Get all disapproved requests for this staff
	$rows = array();
	/* Your code goes here */
	$link = Utility::getDefaultDBConnection();
	$query = getLeaveQuery('DISAPPROVED', $staff, $date);
	$result = mysqli_query($link, $query) or die(mysqli_error($link));
	if($result){
		while($row = mysqli_fetch_array($result)){
			$rows[] = $row;
		}
	}
    return $rows;
}

function getLeaveQuery($status, $staff=NULL, $date=NULL){
	$fields="l.id,l.staff_id,s.first_name,s.last_name,l.request_time,l.reason,l.type,l.request_start_time,l.request_duration";
	$query = "l.is_approved='".$status."'";
	if(!is_null($staff)){
	$query = "s.staff_id='".$staff."' && l.is_approved='$status'";
	}
	if(!is_null($date)){
	$query = "l.request_time='".$date."' && l.is_approved='$status'";
	}
	if(!is_null($staff) and !is_null($date)){
	$query = "s.staff_id='".$staff."' && l.request_time='".$date."' && l.is_approved='$status'";
	}
	return "SELECT ".$fields." from leave_requests as l, staff as s where (l.staff_id = s.staff_id && ".$query.")";
}

function getAttendance($staff, $date){
    //Return attendance records on or before the given date for this staff
	$rows = array();
			$query = "select * from attendance";
		$link = Utility::getDefaultDBConnection();
		if(isset($staff) and isset($date)){
			$query = "select * from attendance where staff_id='".$staff."' and day<='$date'";
		}
		elseif(isset($date)){
			$query = "select * from attendance where day<='$date'";
		}
		elseif(isset($staff)){
			$query = "select * from attendance where staff_id='".$staff."'";
		}
		$result = mysqli_query($link, $query) or die(mysqli_error($link));
		if($result){
			while($row = mysqli_fetch_array($result)){
				$rows[] = $row;
			}
		}
    return $rows;
}
