DROP DATABASE IF EXISTS gutenberg;
CREATE DATABASE gutenberg;
USE gutenberg;

CREATE TABLE inverted (
	word	VARCHAR(25),
	id		INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (id)
);

CREATE TABLE document (
	id			INT NOT NULL AUTO_INCREMENT,
	book_num	INT,
	pos			INT,
	term_id		INT,
	FOREIGN KEY (term_id) REFERENCES inverted(id),
	PRIMARY KEY (id)
);
INSERT INTO inverted (word) VALUES ("hello");
INSERT INTO document (book_num, pos, term_id) VALUES (1,1,1);

SELECT * FROM document;