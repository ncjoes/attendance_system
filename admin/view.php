<?php
require_once '../class_lib.php';
require_once 'Admin.php';
require_once 'functions.php';

$user = new Admin();
if (!$user->isLoggedIn()) {
    header("Location: index.php");
}

//Set page number
$page = filter_input(INPUT_GET, "p");
if (empty($page)) {
    $page = 1;
}

//Check for post request
$array = filter_input_array(INPUT_POST);
if ($array !== FALSE && $array !== null) {
    foreach ($array as $key => $value) {
        if (is_array($array[$key])) {
            foreach ($array[$key] as $subkey => $subvalue) {
                $array[$key][$subkey] = html_entity_decode($array[$key][$subkey]);
            }
        } else {
            $array[$key] = html_entity_decode($array[$key]);
        }
    }
    //Further processing is done in the page to which the request was directed to
}

//If request was sent to settings.php
if (isset($array['change_password'])) {
    //Handle request
    $isPasswordChangeRequest = true;
    try {
        $user->changePassword($array['password'], $array['password1'], $array['password2']);
        $success = true;
        $error_message = "";
    } catch (Exception $exc) {
        $success = false;
        $error_message = $exc->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <link rel="icon" href="../favicon.ico" type="image/x-icon" />
        <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />

        <link href="../css/metro-bootstrap.css" rel="stylesheet">
        <link href="../css/metro-bootstrap-responsive.css" rel="stylesheet">
        <link href="../css/iconFont.css" rel="stylesheet">
        <link href="../js/prettify/prettify.css" rel="stylesheet">

        <script src="../js/metro/metro.min.js"></script>

        <!-- Load JavaScript Libraries -->
        <script src="../js/jquery/jquery.min.js"></script>
        <script src="../js/jquery/jquery.widget.min.js"></script>
        <script src="../js/jquery/jquery.mousewheel.js"></script>
        <script src="../js/prettify/prettify.js"></script>

        <!-- Metro UI CSS JavaScript plugins -->
        <script src="../js/metro.min.js"></script>

        <!-- Local JavaScript -->
        <script src="../js/docs.js"></script>
        <script src="../js/github.info.js"></script>
        
        <script type="text/javascript">
        	function warn(formName){
				if(confirm('Are you sure?')==true){
					document.forms[formName].submit();
				}
			}
        </script>

        <!-- Page Title -->
        <title>CPanel</title>     

    </head>
    <body class="metro">
        <div class="">
            <div class="bg-white">            
                <?php require_once '../header.php'; ?>
                <div class="padding20">
                    <h2>Admin</h2>
                    <div class="grid">
                        <div class="row">
                            <div class="span3">
                                <nav class="sidebar dark">
                                    <ul class="">
                                        <li class="<?= $page == 1 || ($page >= 11 && $page <= 14) ? "stick bg-darkBlue" : "" ?>">
                                            <a class="dropdown-toggle" href="#"><i class="icon-user-2"></i> Staff</a>
                                            <ul class="dropdown-menu" data-role="dropdown">
                                                <li><a href="view.php?p=1">Staff List</a></li>
                                                <li><a href="view.php?p=13">Add Staff</a></li>
                                                <li><a href="view.php?p=14">Enroll Fingerprints</a></li>
                                            </ul>
                                        </li>
                                        <li class="<?=
                                        $page == 2 ||
                                        $page == 201 ||
                                        $page == 202 ? "stick bg-darkBlue" : ""
                                        ?>">
                                            <a href="view.php?p=2"><i class="icon-shipping"></i> Leave Requests</a>
                                        </li>
                                        <li class="<?= $page == 3 || $page == 31 || $page == 32 ? "stick bg-darkBlue" : "" ?>">
                                            <a class="dropdown-toggle" href="#"><i class="icon-clock"></i> Attendance</a>
                                            <ul class="dropdown-menu" data-role="dropdown">
                                                <li><a href="view.php?p=3">Info</a></li>
                                                <li><a href="view.php?p=31">Check In</a></li>
                                                <li><a href="view.php?p=32">Check Out</a></li>
                                            </ul>
                                        </li>
                                        <li class="<?= $page == 4 ? "stick bg-darkBlue" : "" ?>">
                                            <a href="view.php?p=4"><i class="icon-tools"></i> Settings</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>

                            <div class="span12">
                                <?php
                                switch ($page) {
                                    case 1:
                                        include_once 'staff_active.php';
										break;
                                    case 11:
                                        include_once 'staff_suspended.php';
										break;
                                    case 12:
                                        include_once 'staff_deleted.php';
										break;
                                    case 13:
                                        include_once 'staff_registration.php';
                                        break;
                                    case 14:
                                        include_once 'enroll.php';
                                        break;
                                    case 2:
                                        include_once 'leave_requests_pending.php';
                                        break;
                                    case 201:
                                        include_once 'leave_requests_approved.php';
                                        break;
                                    case 202:
                                        include_once 'leave_requests_disapproved.php';
                                        break;
                                    case 3:
                                        include_once './attendance.php';
                                        break;
                                    case 31:
                                        include_once './checkin.php';
                                        break;
                                    case 32:
                                        include_once './checkout.php';
                                        break;
                                    case 4:
                                        include_once './settings.php';
                                        break;
                                    default :
                                        include_once 'staff_active.php';
                                        break;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br/>
                <?php require_once '../footer.php'; ?>
            </div>
        </div>
    </body>
</html>
