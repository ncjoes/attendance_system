<?php
require_once '../class_lib.php';
require_once 'Admin.php';

$user = new Admin();
//If logged in, redirect to view
if ($user->isLoggedIn()) {
    //Set page number
    $page = filter_input(INPUT_GET, "p");
    if (empty($page)) {
        $page = 1;
    }
    header("Location:view.php?p=$page");
}

//Process form inputs here
$isFormRequest = false; //true if a request was sent from form
$success = true; //true if no error in processing
$error_message = ""; //error message if processing was unsuccessful

if(isset($array['submit'])){
	$isFormRequest = true;
	if(!empty($array['username']) and !empty($array['password'])){
		$admin = new Admin();
		if($admin->login($array['username'], $array['password'])){
			header("location:index.php");
		}else{
			$success = false;
			$error_message = "Invalid username/password";
		}
	}else{
		$error_message = "please set username and password";
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
                            <h2 class="panel-header bg-grayDark fg-white">Login</h2>
                            <?php if ($isFormRequest && !$success) { ?>
                                <div class="panel-content">
                                    <p class="fg-red"><?= $error_message ?></p>
                                </div>
                            <?php } ?>
                            <div  class="panel-content">                                
                                <form method='post' action='index.php'>
                                    <!--Login form-->
                                    <div class="grid">
                                        <input name="type" value="1" hidden=""/>
                                        <div class="row ntm">
                                            <label class="span2">Username <i title="Admin's username" class="icon-help fg-blue"></i></label>
                                            <div class="span4">
                                                <input class="text" name='username' maxlength="6" style="width: inherit" required type='text' tabindex='1' />
                                            </div>
                                        </div>
                                        <div class="row" >
                                            <label class="span2">Password</label>
                                            <div class="span4">
                                                <input class="password" name='password' style="width: inherit" required type='password' tabindex='2' />
                                            </div>
                                        </div>
                                        <div class="no-phone" style="padding-left: 160px">
                                            <input class="button default  bg-hover-dark" style="width: 300px" type='submit'
                                                   name='submit' value='Login' tabindex='3'/>
                                            <br/>
                                            <a href="reset_password.php" class=""> &nbsp;&nbsp;forgot password?</a>
                                        </div>
                                        <div class="on-phone no-tablet no-desktop padding20 ntp nbp">
                                            <input class="button default  bg-hover-dark" type='submit'
                                                   name='submit' value='Login' tabindex='3'/>
                                            <br/>
                                            <a href="reset_password.php" class=""> &nbsp;&nbsp;forgot password?</a>
                                        </div>
                                    </div>
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
