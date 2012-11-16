DROP TABLE address_tbl	CASCADE;

-- Table creation (address_tbl)
BEGIN;
CREATE TABLE address_tbl (
	addrid	varchar(128) PRIMARY KEY,
	street	varchar(255) NOT NULL,
	city	varchar(255) NOT NULL,
	state	varchar(255),
	zip		varchar(25)  NOT NULL,
	country varchar(255),
	islett  boolean NOT NULL DEFAULT true,
	descr	varchar(255),
	
	UNIQUE (street,city,state,zip,country)
);
COMMIT;
