<?php

require_once(__DIR__."/DB.php");
require_once("rawSqlPool.php");

class sqlPool extends rawSqlPool {
    public static function load_dictionary() {
        return DB::QR(parent::load_dictionary(), NULL);
    }

    public static function load_full_dictionary() {
        return DB::QR(parent::load_full_dictionary(), NULL);
    }

    /* Remove not accepted words. */
    public static function removeNotAccepted() {
        return DB::QW(parent::removeNotAccepted(), NULL);
    }

    /* Update suggestion acceptance. */
    public static function update($wordID=-1, $word="", $status=1) {
        return DB::QW(parent::update(), array('args' => array(':wordID' => $wordID, ':word' => $word, ':status' => $status)));
    }

    /* Enter Suggestion. */
    public static function suggest($word="") {
        return DB::QW(parent::suggest(), array('args' => array(':word' => preg_replace('/(^\s+)|(\s+$)/', '', $word))));
    }
}