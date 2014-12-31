<?php

class rawSqlPool {

    public static function load_dictionary() {
        return 'SELECT D.word, D.email, LENGTH(D.word) AS L FROM Dictionary AS D WHERE D.accepted=1 ORDER BY L DESC, D.word ASC';
    }

    public static function load_full_dictionary() {
        return 'SELECT D.wordID, D.word, D.email, D.accepted FROM Dictionary AS D ORDER BY D.accepted ASC';
    }

	/* Remove not accepted words. */
	public static function removeNotAccepted() {
		return 'CALL cleanup()';
	}

	/* Update suggestion acceptance. */
	public static function update() {
		return 'UPDATE Dictionary AS D SET D.word = :word, D.email = :email, D.accepted = :status WHERE D.wordID = :wordID';
	}

    /* Enter Suggestion. */
	public static function suggest() {
		return 'INSERT INTO Dictionary (`word`, `email`) VALUES (:word, :email)
		        ON DUPLICATE KEY UPDATE
		        email = :email,
		        suggestedNumber = suggestedNumber + 1,
		        accepted = IF(suggestedNumber > 777 AND accepted = 0, 1, accepted)';
	}
}