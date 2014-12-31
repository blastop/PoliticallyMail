<?php

    /* This is a PDO singleton. */
    class DB {
        private static $DB_HOST = NULL;
        private static $DB_NAME = NULL;
        private static $DB_USER_R = NULL;
        private static $DB_PASS_R = NULL;
        private static $DB_USER_W = NULL;
        private static $DB_PASS_W = NULL;

        private static $instance_read = NULL;
        private static $instance_write = NULL;

        public static function init($DB = NULL, $login='', $pass='') {

                self::$DB_HOST = 'localhost';
                self::$DB_NAME = $DB;
                self::$DB_USER_W = self::$DB_USER_R = $login;
                self::$DB_PASS_W = self::$DB_PASS_R = $pass;

                self::connect(FALSE);
                self::connect(TRUE);
        }

        private static function connect($isWrite=FALSE) {
            try {
                $username = $isWrite ? self::$DB_USER_W : self::$DB_USER_R;
                $password = $isWrite ? self::$DB_PASS_W : self::$DB_PASS_R;
                $instance = new PDO(
                    'mysql:dbname='.self::$DB_NAME.'; host='.self::$DB_HOST,
                    $username,
                    $password,
                    array(
                        /*PDO::MYSQL_ATTR_INIT_COMMAND*/ 1002 => "SET NAMES utf8",
                        PDO::ATTR_PERSISTENT => false,
                        PDO::ATTR_TIMEOUT => 86400
                    )
                );
                $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                return $isWrite ? (self::$instance_write = $instance) : (self::$instance_read = $instance);
            } catch (PDOException $e) {
                $instance = NULL;
                echo 'Connection failed: '.$e->getMessage()."\n";
            }
            return NULL;
        }

        public static function I($isWrite=FALSE) {
            if ((!$isWrite && self::$instance_read === NULL) || ($isWrite && self::$instance_write === NULL)) {
                return self::connect($isWrite);
            }
            return $isWrite ? self::$instance_write : self::$instance_read;
        }

        /* Execute a query and return all the results if possible. */
        public static function Q(&$sql, &$params=NULL, $isWrite=FALSE) {
            if (self::I($isWrite) && !empty($sql)) {
                $tries = 10;
                do {
                    try {
                        $result = self::I($isWrite)->prepare($sql);

                        if ($result && is_array($params) && !empty($params)) {
                            if (isset($params['bind'])) {
                                foreach ($params['bind'] as $bing_key => $bind_param) {
                                    $result->bindParam($bing_key, $params['args'][$bing_key], $bind_param);
                                }
                                $result->execute();
							} else {
                                $result->execute($params['args']);
                            }
                        } else {
                            $result = self::I($isWrite)->query($sql);
                        }
                        if (!$isWrite) {
                            return $result;
                        } else {
                            return strpos($sql, "INSERT ") !== false ? self::lastInsertId() : $result->rowCount();
                        }

                    } catch (PDOException $e) {
                        #require_once 'Zend/Db/Statement/Exception.php';
                        if (
                            $tries
                            && $e->getMessage() != 'SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction'
                            && strpos($e->getMessage(), 'SQLSTATE[HY000]: General error: 2006 MySQL server has gone away') === FALSE
                        ) {
                            throw new Zend_Db_Statement_Exception("****************************".$e->getMessage());
                        }
                        $tries--;
                    }
                } while ($tries);
            }
            return NULL;
        }

        private static function error_get_last() {
            global $__error_get_last_retval__;
            if( !isset($__error_get_last_retval__) ) {
                return null;
            }
            return $__error_get_last_retval__;
        }

        private static function error_clean() {
            global $__error_get_last_retval__;
            if( isset($__error_get_last_retval__) ) {
                $__error_get_last_retval__ = NULL;
            }
        }

        /* Execute a query and return all the results if possible. */
        public static function QR($sql, $params=NULL) {
            try {
                $result = self::Q($sql, $params);
                if ($result) {
                    return $result->fetchAll(PDO::FETCH_CLASS);
                }
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
            return NULL;
        }

        /* Execute a query and return all the results if possible. */
        public static function QW($sql, $params=NULL) {
            try {
                return self::Q($sql, $params, TRUE);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
            return NULL;
        }

        public static function close() {
            self::$DB_HOST = NULL;
            self::$DB_NAME = NULL;
            self::$DB_USER_R = NULL;
            self::$DB_PASS_R = NULL;
            self::$DB_USER_W = NULL;
            self::$DB_PASS_W = NULL;

            self::$instance_read = NULL;
            self::$instance_write = NULL;
        }

//        /* Get the last error code (for QW only !!!). */
//        public static function errorCode($sql=FALSE) {
//            if ($sql && self::getPrep($sql)) {
//                return self::getPrep($sql)->errorCode();
//            }
//            return FALSE;
//        }

        /* Get the last inserted ID (for QW only !!!). */
        public static function lastInsertId() {
            return self::I(TRUE)->lastInsertId();
        }

        /* Gets the number of all the records without the limit. */
        public static function foundRowsNum() {
            return self::QR('SELECT FOUND_ROWS() AS num');
        }

//        public static function ErrorHandler($errno, $errstr, $errfile, $errline) {
//            if (!(error_reporting() & $errno)) {
//                // This error code is not included in error_reporting
//                return;
//            }
//
//            if ($errno == E_USER_WARNING && strpos($errstr, 'server has gone away')) {
//                self::$reQflag = TRUE;
//            }
//
//            /* Don't execute PHP internal error handler */
//            return true;
//        }

        public static function show_final_query(&$sql, &$params, $log=false) {
			$ret = str_replace(array_keys($params["args"]), $params["args"], $sql);;
			if ($log) {
				error_log($ret);
				return false;
			}
            return $ret;
        }
    }