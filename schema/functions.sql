DELIMITER //

DROP FUNCTION IF EXISTS `paleotita`//

CREATE FUNCTION `paleotita`(`ts` TIMESTAMP)
	RETURNS INTEGER
	NO SQL
BEGIN
	RETURN (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`ts`));
END//

DELIMITER ;
