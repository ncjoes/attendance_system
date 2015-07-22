<?php
require_once("../User.php");
class Staff extends User {
    
    public function __construct(){
        parent::User();
    }

    public function isStaffDeleted() {
        if (isset($this->userData)) {
            return $this->userData['is_deleted'] == 1;
        }
        return true; //Restrict access if record does not exist
    }

    public function isStaffSuspended() {
        if (isset($this->userData)) {
            return $this->userData['is_suspended'] == 1;
        }
        return false; //Restrict access if record does not exist
    }

    /**
     * Validates user details and set cookies
     * @param type $ID user's registration number
     * @param type $password user's password
     * @return boolean true if user was successfully validated and cookies was sucessfully set, false otherwise
     */
    public function login($ID, $password) {
        if (!(empty($ID) | empty($password))) {
            $query = "select password from staff where staff_id = '$ID'";
            $link = Utility::getDefaultDBConnection();
            $result = mysqli_query($link, $query);
            if ($result) {
                $row = mysqli_fetch_array($result);
                $hash = $row['password'];
                // Verify stored hash against plain-text password
                if (password_verify($password, $hash)) {
                    $options = array('cost' => Utility::getHashCost());
                    // Check if a newer hashing algorithm is available
                    // or the cost has changed
                    if (password_needs_rehash($hash, PASSWORD_DEFAULT, $options)) {
                        // If so, create a new hash, and replace the old one
                        $newHash = password_hash($password, PASSWORD_DEFAULT, $options);
                        $hash = mysqli_escape_string($link, $newHash);
                        $query = "update staff set password = '$hash' where staff_id = '$ID'";
                        mysqli_query($link, $query);
                    }
                    //Log error
                    Utility::logMySQLError($link);
                    //update data
                    $ok = $this->setUserCookies($ID, $hash);
                    $this->userData = $this->getUserDataFromDatabase();
                    return $ok;
                }
            }
            //Log error
            Utility::logMySQLError($link);
        }
        return false;
    }

    /**
     * Update the data of user having the given ID with given values 
     * @param type $array array of fields mapped to values
     * @return boolean returns true if user's data was successfully updated, false otherwise
     */
    public function updateUserInfo(array $array) {
        $link = Utility::getDefaultDBConnection();
        foreach ($array as $key => $value) {
            $array[$key] = mysqli_escape_string($link, $value);
        }
        $ok = $this->validateInfo($array["staff_id"],
                $array["email"],
                $array["first_name"],
                $array["last_name"],
                $array["department"],
                $array["Designation"],
                $array["phone"]);

        if ($ok) {
            if (!empty($_FILES["pic_url"]['name'])) {
                $url = $this->uploadUserImage("pic_url"); //Throws exception if not successful
                $array['pic_url'] = $url;
            } else {
                $array['pic_url'] = $this->userData['pic_url'];
            }

            $query = $this->getUpdateQuery($array);
            $ok = mysqli_query($link, $query);
            //Log error
            Utility::logMySQLError($link);

            //Reload
            $this->userData = $this->getUserDataFromDatabase();
        } else {
            throw new Exception("Oops! Something went wrong, please try again");
        }
    }

    private function getUpdateQuery(array $array) {
        return "update staff set email='" . $array["email"] . "',"
                . "first_name='" . $array["first_name"] . "',last_name='" . $array["last_name"] . "',"
                . "other_names='" . $array["other_names"] . "',department='" . $array["department"] . "',"
                . "Designation='" . $array["Designation"] . "',phone='" . $array["phone"] . "',"
                . "address='" . $array["address"] . "',sex='" . $array["sex"] . "',"
                . "dob='" . $array["dob"] . "',"
                . "pic_url='" . $array["pic_url"] . "' "
                //Add more field as needed
                . "where staff_id='" . $array["staff_id"] . "'";
    }

    private function uploadUserImage($filename) {

        switch ($_FILES[$filename]["type"]) {
            case "image/gif":
                $file_ext = ".gif";
                break;
            case "image/jpeg":
                $file_ext = ".jpeg";
                break;
            case "image/pjpeg":
                $file_ext = ".jpeg";
                break;
            case "image/png":
                $file_ext = ".png";
                break;
            default:
                $file_ext = "";
                break;
        }

        if (empty($file_ext)) {
            throw new Exception("Unknown file format");
        }

        if (($_FILES[$filename]["size"] / 1024) > 250) { //250kb
            throw new Exception("File too large");
        }

        if ($_FILES[$filename]["error"] > 0) {
            throw new Exception("Oops! Error occurred while uploading file");
//            echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
        }

        //Delete old profile picture
        if (file_exists($this->userData['pic_url'])) {
            unlink($this->userData['pic_url']);
        }

        $prefix = str_replace("/", "", $this->getID());
        $url = "uploads/userpics/" . uniqid($prefix) . $file_ext;
        $moved = move_uploaded_file($_FILES[$filename]["tmp_name"], $url);

        if ($moved) {
            return $url;
        } else {
            throw new Exception("Oops! Error occurred while uploading file");
        }
    }

    private function validateInfo($ID, $email, $first_name, $last_name, $dept, $Designation, $phone, $password = NULL) {
        if (isset($ID) && isset($email) && isset($first_name) && isset($last_name) && isset($phone)) {
            //Check ID
            if (!preg_match("#S-\d{4}#", $ID)) {
                throw new Exception("Invalid ID");
            }
            //Check email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email");
            }
            //Check first name
            if (!preg_match("#[[:alpha:]]{2,}#", $first_name)) {
                throw new Exception("Invalid first name");
            }
            //Check last name
            if (!preg_match("#[[:alpha:]]{2,}#", $last_name)) {
                throw new Exception("Invalid last name");
            }
            //Check department
            if (!preg_match("#[[:alpha:]]{2,}#", $dept)) {
                throw new Exception("Invalid department name");
            }
            //Check Designation
            if (!preg_match("#[[:alpha:]]{2,}#", $Designation)) {
                throw new Exception("Invalid Designation");
            }
            //Check phone
            if (!preg_match("#\d{11}|(\+234{10})#", $phone)) {
                throw new Exception("Invalid phone number");
            }
            //Check password
            if (!is_null($password)) {
                if($this->validatePassword($password)){
					
				}
            }
            return TRUE;
        }
        else{ throw new Exception("Field not set");}
    }

    private function addNewStaff($first_name, $last_name, $ID, $password, $retypedPassword, $department, $Designation, $phone, $email) {
        $link = Utility::getDefaultDBConnection();
        $options = array('cost' => Utility::getHashCost());
        $hash = password_hash($password, PASSWORD_DEFAULT, $options);
        $fname = mysqli_escape_string($link, $first_name);
        $lname = mysqli_escape_string($link, $last_name);
        $staff_id = mysqli_escape_string($link, $ID);
        $pwd = mysqli_escape_string($link, $hash);
        $dept = mysqli_escape_string($link, $department);
		$Designation = mysqli_escape_string($link, $Designation);
        $phone_no = mysqli_escape_string($link, $phone);
        $email_address = mysqli_escape_string($link, $email);
        $query = "insert into staff set staff_id = '$staff_id',"
                . "password='$pwd',"
                . "email='$email_address',"
                . "first_name='$fname',"
                . "last_name='$lname',"
				. "dob='".strftime("%Y-%m-%d", mktime())."',"
                . "department='$dept',"
                . "designation='$Designation',"
                . "phone='$phone_no'";
        $ok = mysqli_query($link, $query);
        return $ok;
    }

    /**
     * Creates a new user with the given infomation
     * @param type $ID user's registration number
     * @param type $password user's password
     * @param type $email user's email address
     * @param type $first_name user's first name
     * @param type $last_name user's last name
     * @param type $phone user's phone number
     * @return boolean returns true if user's data was successfully registered, false otherwise
     */
    public function registerStaff($first_name, $last_name, $ID, $password, $retypedPassword, $department, $Designation, $phone, $email) {
        // Validate details
        $ok = strcmp($password, $retypedPassword) === 0;
        if ($ok) {
            $ok = $this->validateInfo($ID, $email, $first_name, $last_name, $department, $Designation, $phone, $password);
        } else {
            throw new Exception("Passwords do not match");
        }
        // Add to database
        if ($ok) {
            $ok = $this->addNewStaff($first_name,$last_name,$ID,$password,$retypedPassword,$department,$Designation,$phone,$email);
        }
        return $ok;
    }

    public function getUserDataFromDatabase() {
        $query = "select * from staff where staff_id = '" . $this->getCookiesID() . "'";
        $link = Utility::getDefaultDBConnection();
        $result = mysqli_query($link, $query);
        if ($result) {
            $array = mysqli_fetch_array($result);
            return $array;
        } else {
            //Log error
            Utility::logMySQLError($link);
        }
        return array();
    }
    
    /**
     * @returns user display name
     */
    public function getDisplayName() {
        if ($this->isLoggedIn()) {
            if ($this->userData) {
                return $this->userData['first_name'] . " " . $this->userData['last_name'];
            }
        }
        return "";
    }
    
    
    public function getID() {
        if (isset($this->userData)) {
            return $this->userData['staff_id'];
        }
        return "";
    }

    public function getPassword() {
        if (isset($this->userData)) {
            return $this->userData['password'];
        }
        return "";
    }

    public function changePassword($oldPassword, $newPassword1, $newPassword2) {
        if (password_verify($oldPassword, $this->getPassword())) {
            //Check password
            $this->validatePassword($newPassword1);
            $ok = strcmp($newPassword1, $newPassword2) === 0;
            if ($ok) {
                $link = Utility::getDefaultDBConnection();
                $options = array('cost' => Utility::getHashCost());
                $pwd = password_hash($newPassword1, PASSWORD_DEFAULT, $options);
                $query = "update staff set password='" . $pwd . "' where staff_id='" . $this->getID() . "'";
                mysqli_query($link, $query);
                //Log error
                Utility::logMySQLError($link);

                //Reload
                $this->userData = $this->getUserDataFromDatabase();
                $ok = $this->setUserCookies($this->userData['staff_id'], $this->userData['password']);
                return $ok;
            } else {
                throw new Exception("Passwords do not match");
            }
        } else {
            throw new Exception("Wrong password");
        }
    }

	public static function getDeptOptions(){
		return array("OPERATIONS", "PENSION", "ACCOUNTANCY", "ENGINEERS", "HSE");
	}

	public static function getDesignationOptions(){
		return array("MANAGER", "SENIOR OFFICER", "CHIEF ENGINEER", "HEAD ACCOUNTANT");
	}
}
