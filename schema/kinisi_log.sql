CREATE TABLE `kinisi_log` (
	`kodikos`	INTEGER(10) NOT NULL COMMENT 'Primary key',
	`dianomi`	INTEGER(10) NOT NULL COMMENT 'Κωδικός διανομής',
	`pektis`	INTEGER(1) NOT NULL COMMENT 'Παίκτης που εκτελεί την κίνηση',
	`idos`	ENUM (
		'ΔΙΑΝΟΜΗ',
		'ΔΗΛΩΣΗ',
		'ΤΖΟΓΟΣ',
		'ΣΟΛΟ',
		'ΑΓΟΡΑ',
		'ΣΥΜΜΕΤΟΧΗ',
		'ΦΥΛΛΟ',
		'ΜΠΑΖΑ',
		'CLAIM',
		'ΑΚΥΡΩΣΗ',
		'ΠΛΗΡΩΜΗ'
	)		NOT NULL COMMENT 'Είδος κίνησης',
	`data`		VARCHAR(256) NOT NULL COMMENT 'Δεδομένα κίνησης',
	`pote`		TIMESTAMP NOT NULL COMMENT 'Χρονική στιγμή',

	PRIMARY KEY (
		`kodikos`
	) USING BTREE,

	FOREIGN KEY (
		`dianomi`
	) REFERENCES `dianomi_log` (
		`kodikos`
	) ON DELETE CASCADE
) ENGINE=InnoDB CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8 COMMENT='Πίνακας κρατημένων κινήσεων'
