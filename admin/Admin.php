<?php
require_once("../User.php");
class Admin extends User{
    
    public function __construct() {
        parent::User();
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
                $query = "update admin set password='" . $pwd . "' where username='" . $this->getID() . "'";
                mysqli_query($link, $query);
                //Log error
                Utility::logMySQLError($link);

                //Reload
                $this->userData = $this->getUserDataFromDatabase();
                $ok = $this->setUserCookies($this->userData['username'], $this->userData['password']);
                return $ok;
            } else {
                throw new Exception("Passwords do not match");
            }
        } else {
            throw new Exception("Wrong password");
        }
    }

    
    public function getID() {
        if (isset($this->userData)) {
            return $this->userData['username'];
        }
        return "";
    }

    public function getPassword() {
        if (isset($this->userData)) {
            return $this->userData['password'];
        }
        return "";
    }

    public function getUserDataFromDatabase() {
        $query = "select * from admin where username = '" . $this->getCookiesID() . "'";
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

    public function login($ID, $password) {
        if (!(empty($ID) | empty($password))) {
            $query = "select password from admin where username = '$ID'";
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
                        $query = "update admin set password = '$hash' where username = '$ID'";
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

    public function updateUserInfo(array $array) {
        return;
    }

    public function deleteStaff(array $staff_id) {
        $link = Utility::getDefaultDBConnection();
        mysqli_autocommit($link, false);
        foreach ($staff_id as $value) {
            $query = "update staff set is_deleted = 1, is_suspended = 0 where staff_id = '$value'";
            $ok = mysqli_query($link, $query);
            if (!$ok) {
                //Log error
                Utility::logMySQLError($link);
                return FALSE;
            }
        }
        return mysqli_commit($link);
    }

    public function suspendStaff(array $staff_id) {
        $link = Utility::getDefaultDBConnection();
        mysqli_autocommit($link, false);
        foreach ($staff_id as $value) {
            $query = "update staff set is_suspended = 1, is_deleted = 0 where staff_id = '$value'";
            $ok = mysqli_query($link, $query);
            if (!$ok) {
                //Log error
                Utility::logMySQLError($link);
                return FALSE;
            }
        }
        return mysqli_commit($link);
    }

    public function activateStaff(array $staff_id) {
        $link = Utility::getDefaultDBConnection();
        mysqli_autocommit($link, false);
        foreach ($staff_id as $value) {
            $query = "update staff set is_suspended = 0, is_deleted = 0  where staff_id = '$value'";
            $ok = mysqli_query($link, $query) or die(mysqli_error($link));
            if (!$ok) {
                //Log error
                Utility::logMySQLError($link);
                return FALSE;
            }
        }
        return mysqli_commit($link);
    }

    public function approveLeaveRequest(array $IDs) {
        $link = Utility::getDefaultDBConnection();
        mysqli_autocommit($link, false);
        foreach ($IDs as $value) {
            $query = "update leave_requests set is_approved = 'APPROVED' where id = $value";
            $ok = mysqli_query($link, $query) or die(mysqli_error($link));
            if (!$ok) {
                //Log error
                Utility::logMySQLError($link);
                return FALSE;
            }
        }
        return mysqli_commit($link);
    }

    public function disapproveLeaveRequest(array $IDs) {
        $link = Utility::getDefaultDBConnection();
        mysqli_autocommit($link, false);
        foreach ($IDs as $value) {
            $query = "update leave_requests set is_approved = 'DISAPPROVED' where id = $value";
            $ok = mysqli_query($link, $query) or die(mysqli_error($link));
            if (!$ok) {
                //Log error
                Utility::logMySQLError($link);
                return FALSE;
            }
        }
        return mysqli_commit($link);
    }

}