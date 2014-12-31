-- -----------------------------------------------------------
-- ----------------------- DATABASE --------------------------
-- -----------------------------------------------------------

CREATE DATABASE IF NOT EXISTS PoliticallyMail;

USE PoliticallyMail;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS PoliticallyMail.Dictionary;

CREATE TABLE PoliticallyMail.Dictionary (
	`wordID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `accepted` tinyint(1) unsigned DEFAULT 0,
  `suggestedNumber` tinyint(2) unsigned DEFAULT 0,
  `word` varchar(128) DEFAULT 0,
	PRIMARY KEY(`wordID`),
  UNIQUE (`word`),
  INDEX (`accepted`),
  INDEX (`suggestedNumber`)
) ENGINE=INNODB DEFAULT CHARSET="utf8";

INSERT INTO PoliticallyMail.Dictionary (`word`, `accepted`) VALUES
('a@b.c',1),
('b@c.d',1),
('c@d.e',1)
;

-- -----------------------------------------------------------
-- ----------------------- PROCEDURES ------------------------
-- -----------------------------------------------------------

DELIMITER $$

DROP PROCEDURE IF EXISTS cleanup$$

CREATE PROCEDURE cleanup()
	BEGIN
		DELETE FROM PoliticallyMail.Dictionary WHERE accepted = 2;
	END $$

DELIMITER ;
/* */

## mysql --user=your_login --password=your_password -e 'USE PoliticallyMail; CALL cleanup();'