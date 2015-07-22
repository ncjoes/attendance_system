<?php
require_once '../class_lib.php';
require_once 'Staff.php';
require_once 'functions.php';

$user = new Staff();
if (!$user->isLoggedIn()) {
    header("Location:index.php");
}

//Set page number
$page = filter_input(INPUT_GET, "p");
if (empty($page)) {
    $page = 1;
}

//Check for post request
$array = filter_input_array(INPUT_POST);
if ($array !== FALSE && $array !== NULL) {
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

        <!-- Page Title -->
        <title>CPanel</title>     

    </head>
    <body class="metro">
        <div class="">
            <div class="bg-white">            
                <?php require_once '../header.php'; ?>
                <div class="padding20">
                    <h2><?= $user->getDisplayName() ?></h2>
                    <div class="grid">
                        <div class="row">
                            <div class="span3">
                                <nav class="sidebar dark">
                                    <ul class="">
                                        <li class="<?= $page == 1 || $page == 11 ? "stick bg-darkBlue" : "" ?>">
                                            <a class="dropdown-toggle" href="#"><i class="icon-user-2"></i> Profile</a>
                                            <ul class="dropdown-menu" data-role="dropdown">
                                                <li><a href="view.php?p=1">View</a></li>
                                                <li><a href="view.php?p=11">Update</a></li>
                                            </ul>
                                        </li>
                                        <li class="<?=
                                        $page == 2 || $page == 201 || $page == 202 || $page == 21 ?
                                                "stick bg-darkBlue" :
                                                ""
                                        ?>">
                                            <a class="dropdown-toggle" href="#"><i class="icon-shipping"></i> Leave</a>
                                            <ul class="dropdown-menu" data-role="dropdown">
                                                <li><a href="view.php?p=2">History</a></li>
                                                <li><a href="view.php?p=21">Apply</a></li>
                                            </ul>
                                        </li>
                                        <li class="<?= $page == 3 ? "stick bg-darkBlue" : "" ?>">
                                            <a href="view.php?p=3"><i class="icon-clock"></i> My Attendance</a>
                                        </li>
                                        <li class="<?= $page == 4 ? "stick bg-darkBlue" : "" ?>">
                                            <a href="view.php?p=4"><i class="icon-tools"></i> Settings</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>

                            <div class="span12">
                                <?php if ($user->isStaffDeleted()) { ?>
                                    <h2>This account no longer exist, please contact site admin if this is an error</h2>
                                <?php } else if ($user->isStaffSuspended()) {
                                    ?>
                                    <h2>This account has been suspended, contact site admin to resolve this</h2>
                                    <?php
                                } else {
                                    switch ($page) {
                                        case 1:
                                            include_once 'profile_view.php';
											break;
                                        case 11:
                                            include_once 'profile_update.php';
                                            break;
                                        case 2:
                                            include_once 'leave_history_pending.php';
                                            break;
                                        case 201:
                                            include_once 'leave_history_approved.php';
                                            break;
                                        case 202:
                                            include_once 'leave_history_disapproved.php';
                                            break;
                                        case 21:
                                            include_once 'leave_application.php';
                                            break;
                                        case 3:
                                            include_once './attendance.php';
                                            break;
                                        case 4:
                                            include_once './settings.php';
                                            break;
                                        default :
                                            include_once 'profile_view.php';
                                            break;
                                    }
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
