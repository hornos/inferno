-- Cleaning (types)
DROP TYPE  T_email		CASCADE;
DROP TYPE  T_fullname	CASCADE;
DROP TYPE  T_telnum		CASCADE;

DROP TABLE person_tbl	CASCADE;
DROP TABLE T_person_tbl	CASCADE;


-- Type definitions
CREATE TYPE T_email AS (
  email	varchar(255),
  ispub	boolean
); 

CREATE TYPE T_fullname AS (
  forname	varchar(255),
  surname	varchar(255)
); 

CREATE TYPE T_telnum AS (
  athome	varchar(255),
  atwork	varchar(255),
  atmobile	varchar(255)
); 


BEGIN;
CREATE TABLE T_person_tbl (
  ptype	varchar(8)	PRIMARY KEY,
  descr	varchar(255)
);
COMMIT;


BEGIN;
CREATE TABLE person_tbl (
-- Personal details
	pid		SERIAL PRIMARY KEY,
    id	   	varchar(128)	UNIQUE,
	name	T_fullname		NOT NULL,
	sex		char(1)			NOT NULL DEFAULT 'M', CHECK( sex in ('M', 'F') ),
	ptype	varchar(8)		NOT NULL REFERENCES T_person_tbl(ptype) ON DELETE RESTRICT,
-- Addresses
    permaddr	varchar(128)	REFERENCES address_tbl(addrid) ON DELETE RESTRICT,
    tempaddr	varchar(128)	REFERENCES address_tbl(addrid) ON DELETE RESTRICT,
--
    tel		T_telnum,
	email	T_email NOT NULL,
	msn		T_email,
	skype	T_email,
--	
	descr	varchar(512),
--
	isvalid		boolean,
	exprydate	date,
	lastmtime	timestamp,
-- guest
--    roomid	  	  varchar(25)  NOT NULL REFERENCES room_tbl(roomid) ON DELETE RESTRICT,
    roomid	  	  varchar(25) REFERENCES room_tbl(roomid) ON DELETE RESTRICT DEFAULT 'A000',
    occupdate	  date,
    leavedate	  date,
-- student
-- Personal details
    etrid		varchar(128)  UNIQUE,
	tiris		varchar(512),
    faculty		varchar(32) DEFAULT 'EXT',
    class		varchar(32) DEFAULT 'EXT',
    enrollyear	integer,

--
    birthplace	varchar(255),
    birthdate	date,
--    
    mothername	T_fullname,
    fathername	T_fullname,
    motheraddr  varchar(128) REFERENCES address_tbl(addrid) ON DELETE RESTRICT,
    fatheraddr  varchar(128) REFERENCES address_tbl(addrid) ON DELETE RESTRICT,
--
    leaveplace	  varchar(255)
);
COMMIT;
