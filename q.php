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
		            echo "waiting";
	            } break;

	            case 'CHECKIN':{
		            //case called by web browser
		            //write data to file
		            $fh = fopen("_tmp/{$array[2]}.txt", "w+");
		            fwrite( $fh, "OK:VERIFICATION:".$array[2]);
		            fclose( $fh );
		            echo "waiting";
	            } break;

                case 'STATUS':{
					//case called by java app
                    //Status request for staff id in $array[2]
                    //read data from file, verify and ...
					$status_file = "_tmp/{$array[2]}.txt";
					if(file_exists($status_file)){
						$content = file_get_contents($status_file);
						if($content=="SUCCESS:".$array[2] or $content=="FAILED:".$array[2]){
							unlink($status_file);
						}
						echo $content;
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
	                //Verification or Registration passed

	                //update attendance records in database
	                $today = date("Y-m-d");
					$db_query = "INSERT INTO attendance (day,staff_id) VALUES ('{$today}','{$array[2]}')";
	                $execute = mysqli_query($connection, $db_query);
	                if(mysqli_affected_rows($connection)){
		                //write success status to file
		                $fh = fopen("_tmp/{$array[2]}.txt", "w+");
		                fwrite( $fh, "SUCCESS:".$array[2]);
		                fclose( $fh );
	                }

	                echo 'OK:Query recieved';
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
		            $db_query = "DELETE * FROM staff WHERE staff_id = '{$array[2]}'";
		            $execute = mysqli_query($connection, $db_query);
		            $status_file = "_tmp/{$array[2]}.txt";
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
