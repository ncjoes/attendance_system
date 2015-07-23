<?php
$query = strtoupper($_REQUEST['q']);
//format of $query is::  REQUEST:STATUS:staffID
if (preg_match("#\\w+:\\w+:\\w+#", $query)) {

	require_once("class_lib.php");
	$connection = Utility::getDefaultDBConnection();
    $array = explode(":", $query);
    switch ($array[0]) {
        case 'REQUEST':
            switch ($array[1]) {
	            case 'ENROLL': {
		            //case called by web browser
					//write data to file
		            $fh = fopen("_tmp/{$array[2]}.txt", "w+");
		            fwrite( $fh, "OK:REGISTRATION:".$array[2]);
		            fclose( $fh );
		            echo "waiting_enrollment";
	            } break;

	            case 'CHECKIN':{
		            //case called by web browser
		            //check if staff has checked in already
		            $today = date("Y-m-d");
		            $check = "SELECT * FROM attendance WHERE login_time=logout_time AND staff_id='{$array[2]}'";
		            $result = mysqli_query($connection, $check);
		            if(!mysqli_num_rows($result)){
			            //write data to file
			            $fh = fopen("_tmp/{$array[2]}.txt", "w+");
			            fwrite( $fh, "OK:VERIFICATION:".$array[2]);
			            fclose( $fh );
			            echo "waiting_checkin";
		            }else{
			            echo "Staff {$array[2]} already checked-in.\nPlease checkout from current work session.";
		            }
	            } break;

	            case 'CHECKOUT':{
		            //case called by web browser
		            //check if staff has checked in already
		            $today = date("Y-m-d");
		            $check = "SELECT * FROM attendance WHERE login_time=logout_time AND staff_id='{$array[2]}'";
		            $result = mysqli_query($connection, $check);
		            if(mysqli_num_rows($result)){
			            //write data to file
			            $fh = fopen("_tmp/{$array[2]}.txt", "w+");
			            fwrite( $fh, "OK:VERIFICATION:".$array[2]);
			            fclose( $fh );
			            echo "waiting_checkout";
		            }else{
			            echo "Staff {$array[2]} is not checked-in.";
		            }
	            } break;

	            case 'STATUS':{
					//case called by java app & web app
                    //Status request for staff id in $array[2]
                    //read data from file, verify and ...
					$status_file = "_tmp/{$array[2]}.txt";
					if(file_exists($status_file)){
						$content = file_get_contents($status_file);
						echo $content;
						if($content == "SUCCESS:".$array[2] or $content=="FAILED:".$array[2]){
							unlink($status_file); //end verification/enrollment process
						}
					}else{
						echo "Failed";
					}
                } break;

                default:{
	                echo "ERROR:Invalid Request '" . $array[1] . "'";
                } break;
            }
            break;
        case 'REPLY':
            switch ($array[1]) {

                case 'PASSED':{
	                $status_file = "_tmp/{$array[2]}.txt";
	                if(file_exists($status_file)){
		                $content = file_get_contents($status_file);
		                if($content=="OK:VERIFICATION:".$array[2]){
			                //Verification passed
			                $today = date("Y-m-d");
			                //update attendance records in database
			                $check = "SELECT * FROM attendance WHERE login_time=logout_time AND staff_id='{$array[2]}'";
			                $result = mysqli_query($connection, $check);
			                if(mysqli_num_rows($result)){
				                //checkout staff
				                $time = date("Y-m-d H:i:s");
				                $db_query = "UPDATE attendance SET logout_time='{$time}'
											WHERE login_time=logout_time AND staff_id='{$array[2]}'";
				                $execute = mysqli_query($connection, $db_query);
			                }else {
				                //checkin staff
				                $db_query = "INSERT INTO attendance (day,staff_id) VALUES ('{$today}','{$array[2]}')";
				                $execute = mysqli_query($connection, $db_query);
			                }
			                //write success status to file
			                $fh = fopen("_tmp/{$array[2]}.txt", "w+");
			                fwrite($fh, "SUCCESS:" . $array[2]);
			                fclose($fh);
			                echo 'OK:Query recieved';
		                }
		                if($content=="OK:REGISTRATION:".$array[2]){
			                //fingerprint enrollment successful
			                //write success status to file
			                $fh = fopen("_tmp/{$array[2]}.txt", "w+");
			                fwrite( $fh, "SUCCESS:".$array[2]);
			                fclose( $fh );
			                echo 'OK:Query recieved';
		                }
	                }
                }break;

                case 'FAILED':{
	                //Verification or Registration failed
	                //write fail status to file
	                $fh = fopen("_tmp/{$array[2]}.txt", "w+");
	                fwrite( $fh, "FAILED:".$array[2]);
	                fclose( $fh );

	                echo 'OK:Query recieved';
                }break;

	            case 'ABORT':{
		            $status_file = "_tmp/{$array[2]}.txt";
		            if(file_exists($status_file)){
			            unlink($status_file);
		            }
	            } break;
	            case 'ABORT_ENROLLMENT':{
/*
		            $db_query = "DELETE * FROM staff WHERE staff_id = '{$array[2]}'";
		            $execute = mysqli_query($connection, $db_query);
		            $status_file = "_tmp/{$array[2]}.txt";
*/
		            if(file_exists($status_file)){
			            unlink($status_file);
		            }
	            } break;
                default:
                    echo "ERROR:Invalid Reply '" . $array[1] . "'";
                    break;
            }
            break;
        default:
            echo "ERROR:Invalid command '" . $array[0] . "'";
            break;
    }
} else {
    echo "ERROR:Invalid query '$query'";
}