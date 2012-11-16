DROP TABLE room_tbl		CASCADE;
DROP TABLE T_room_tbl	CASCADE;

BEGIN;
CREATE TABLE T_room_tbl (
  rtype varchar(8)	PRIMARY KEY,
  descr	varchar(255)
);
COMMIT;


BEGIN;
CREATE TABLE room_tbl (
	roomid	varchar(25) PRIMARY KEY,
	wing	varchar(25)	NOT NULL,
	floor	integer		NOT NULL,
	number	integer		NOT NULL,
	cpcty	integer		NOT NULL DEFAULT 3 CHECK ( cpcty >= 0 ),
	occup	integer		NOT NULL DEFAULT 0 CHECK ( occup <= cpcty AND occup >= 0 ),
	isavail boolean		NOT NULL DEFAULT true,
	rtype	varchar(8)	NOT NULL REFERENCES T_room_tbl(rtype) ON DELETE RESTRICT DEFAULT 'room',
	tel		varchar(25),
	descr	varchar(255),
	UNIQUE (number,floor,wing)
);
COMMIT;
