<?php

/*
 * Copyright 2015 NACOSS UNN Developers Group (NDG).
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class Utility {

    /**
     * 
     * @return connection to default database
     */
    public static function getDefaultDBConnection() {
        $link = Utility::getConnection();
        if ($link) {
            $successful = mysqli_select_db($link, DEFAULT_DB_NAME);
            if (!$successful) {
                die('Unable to select database: ' . mysql_error());
            }
        } else {
            die('Could not connect to database: ' . mysql_error());
        }
        return $link;
    }

    /**
     * creates a connection to the default database
     * @return connection
     */
    private static function getConnection() {
        $link = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
        return $link;
    }

    /**
     * Writes exception to log file
     * @param type $exc exception
     */
    public static function writeToLog(Exception $exc) {
        $link = Utility::getDefaultDBConnection();
        $line = $exc->getLine();
        $file = mysqli_escape_string($link, $exc->getFile());
        $message = mysqli_escape_string($link, $exc->getMessage());
        //Log error to file
       
    }

    public static function getHashCost() {
        return 10;
    }

    /**
     * Log database error
     * @param type $link
     */
    public static function logMySQLError($link) {
        $error = mysqli_error($link);
        if (!empty($error)) {
            Utility::writeToLog(new Exception($error));
        }
    }

    public static function getRow($table, $search_column, $search_value) {
        $link = Utility::getDefaultDBConnection();
		$query = "select * from ".$table." where ".$search_column."='".$search_value."'";
		$result = mysqli_query($link, $query);
		if($result){
			$row = mysqli_fetch_array($result);
			return $row;
		}
    }

    public static function rowExists($table, $search_column, $search_value) {
        $link = Utility::getDefaultDBConnection();
		$query = "select * from ".$table." where ".$search_column."='".$search_value."'";
		$result = mysqli_query($link, $query);
		if($result){
			$rows = mysqli_num_rows($result);
			return $rows;
		}
		return false;
    }
}