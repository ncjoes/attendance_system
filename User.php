<?php
abstract class User {

    protected $userData;

    function User() {
        $this->userData = $this->getUserDataFromDatabase();
    }

    /**
     * Validates user cookies against user details in database
     * @return boolean true if user cookies match user details in database, else false
     */
    public function isLoggedIn() {
        if (isset($this->userData)) {
            $match = strcasecmp($this->userData['password'], $this->getCookiesPassword());
            return empty($this->userData['password']) ? false : $match === 0;
        }
        return false;
    }

    /**
     * Validates user details and set cookies
     * @param type $ID user's registration number
     * @param type $password user's password
     * @return boolean true if user was successfully validated and cookies was sucessfully set, false otherwise
     */
    public abstract function login($ID, $password);

    /**
     * Update the data of user having the given ID with given values 
     * @param type $array array of fields mapped to values
     * @return boolean returns true if user's data was successfully updated, false otherwise
     */
    public abstract function updateUserInfo(array $array);

    /**
     * Returns user's information from database
     */
    public abstract function getUserDataFromDatabase();
    
    
    public function getUserData(){
        return $this->userData;
    }

    protected function validatePassword($password) {
        if (strlen($password) >= 8) {
            $regex = "#([A-Z]+[a-z]*[0-9]*\S*)([A-Z]*[a-z]+[0-9]*\S*)([A-Z]*[a-z]*[0-9]*\S*)#";
            if(preg_match($regex, $password)){ return true; }
			else{
            throw new Exception("Invalid password: try switching letter cases, adding numbers and special characters");
			}
        } else {
            throw new Exception("Password should be up to 8 characters long");
        }
    }

    /**
     * @returns students registration number from cookies
     */
    protected function getCookiesID() {
        return filter_input(INPUT_COOKIE, "user_id");
    }

    /**
     * @returns students password from cookies
     */
    protected function getCookiesPassword() {
        return filter_input(INPUT_COOKIE, "user_pwd");
    }

    public abstract function getID();

    public abstract function getPassword();

    /**
     * Sets cookies
     * @param type $id
     * @param type $password
     * @return type
     */
    protected function setUserCookies($id, $password) {
        $expire = time() + (60 * 60 * 24 * 7); //1 week i.e 60secs * 60mins * 2hhrs * 7days
        $ok = setcookie("user_id", $id, $expire);
        if ($ok) {
            $ok = setcookie("user_pwd", $password, $expire);
        }
        return $ok;
    }

    public function logout() {
        return $this->clearUserCookies();
    }

    /**
     * Clears all cookies
     * @return type true if all cookies were removed, false otherwise
     */
    protected function clearUserCookies() {
        $clearIDOk = setcookie("user_id", "", time() - 3600);
        $clearPwdOk = setcookie("user_pwd", "", time() - 3600);
        return $clearIDOk && $clearPwdOk;
    }

    public abstract function changePassword($oldPassword, $newPassword1, $newPassword2);
}
