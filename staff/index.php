<?php
require_once '../class_lib.php';
require_once 'Staff.php';

$user = new Staff();

//If logged in, redirect to view
if ($user->isLoggedIn()) {
    //Set page number
    $page = filter_input(INPUT_GET, "p");
    if (empty($page)) {
        $page = 1;
    }
    header("Location: view.php?p=$page");
}


$type = isset($array['type']) ? $array['type'] : filter_input(INPUT_GET, "type");
if(empty($type) || $type == "1") {
    $showLoginPage = true;
} else {
    $showLoginPage = false;
}

//Process form inputs here

$isFormRequest = false; //true if a request was sent from form
$success = true; //true if no error in processing
$error_message = ""; //error message if processing was unsuccessful

if(isset($array['submit'])){
	$isFormRequest = true;
	switch($array['submit']){
		case 'Login': {
					$staff = new Staff();
					if($staff->login($array['staff_id'], $array['password'])){
						header('Location:index.php');
					}
		}break;
		default : {}
	}
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="description" content="Staff attendance website">
        <meta name="author" content="Daw Ayebaboumobara">
        <link rel="icon" href="favicon.ico" type="image/x-icon" />
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

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
        <script src="../js/load-metro.js"></script>

        <!-- Local JavaScript -->
        <script src="../js/docs.js"></script>
        <script src="../js/github.info.js"></script>

        <!-- Page Title -->
        <title><?= COMPANY_NAME ?> : Home</title>        
    </head>
    <body class="metro">
        <div class="container" style="background-image: url(../img/dust.png); background-repeat: repeat;">             
            <?php require_once '../header.php'; ?>
            <div class="padding20">
                <h1 class="text-center">
                    Welcome to <?= COMPANY_NAME ?> 
                </h1>
                <div class="grid">
                    <div class="row">
                        <div style="margin-left: 300px; margin-right: auto;" class="span7 panel bg-white">
                            <h2 class="panel-header bg-grayDark fg-white">
                                <?= $showLoginPage ? "Login" : "Register" ?>
                            </h2>
                            <?php if ($isFormRequest && !$success) { ?>
                                <div class="panel-content">
                                    <p class="fg-red"><?= $error_message ?></p>
                                </div>
                            <?php } ?>
                            <div  class="panel-content">                                
                                <form method='post' action='index.php'>
                                    <?php if ($showLoginPage) { ?>
                                        <!--Login form-->
                                        <div class="grid">
                                            <input name="type" value="1" hidden=""/>
                                            <div class="row ntm">
                                                <label class="span2">ID <i title="Staff ID" class="icon-help fg-blue"></i></label>
                                                <div class="span4">
                                                    <input class="text" placeholder="Staff ID" name='staff_id' maxlength="6" style="width: inherit" required type='text' tabindex='1' value="<?= isset($array['id']) ? $array['id'] : '';?>"/>
                                                </div>
                                            </div>
                                            <div class="row" >
                                                <label class="span2">Password</label>
                                                <div class="span4">
                                                    <input class="password" name='password' required style="width: inherit" type='password' tabindex='2' />
                                                </div>
                                            </div>
                                            <div class="no-phone" style="padding-left: 160px">
                                                <input class="button default  bg-hover-dark" style="width: 300px" type='submit'
                                                       name='submit' value='Login' tabindex='3'/>
                                                <br/>
                                                <!--<a href="?type=2" class=""> &nbsp;&nbsp;create account?</a>-->
                                                <a href="reset_password.php" class=""> &nbsp;&nbsp;forgot password?</a>
                                            </div>
                                            <div class="on-phone no-tablet no-desktop padding20 ntp nbp">
                                                <input class="button default  bg-hover-dark" type='submit'
                                                       name='submit' value='Login' tabindex='3'/>
                                                <br/>
                                                <!--<a href="?s=2" class="">create account?</a>-->
                                                <a href="reset_password.php" class=""> &nbsp;&nbsp;forgot password?</a>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <!--Registration form-->
                                        
                                    <?php } ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <br/>
            <!-- This breaks can be removed -->
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <?php require_once '../footer.php'; ?>
        </div>
    </body>
</html>
