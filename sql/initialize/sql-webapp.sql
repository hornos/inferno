-- Cleaning (functions)
-- DROP FUNCTION ins_occup()	CASCADE;

-- Cleaning (tables)
DROP TABLE group_tbl	CASCADE;
DROP TABLE module_tbl	CASCADE;
DROP TABLE mauth_tbl	CASCADE;
DROP TABLE user_tbl		CASCADE;
-- DROP TABLE log_tbl		CASCADE;

BEGIN;
CREATE TABLE group_tbl (
  groupid	varchar(128)	PRIMARY KEY NOT NULL,
  descr		varchar(255)
);
COMMIT;

BEGIN;
CREATE TABLE module_tbl (
  moduleid	varchar(128)	PRIMARY KEY NOT NULL,
  descr		varchar(255)
);
COMMIT;

BEGIN;
CREATE TABLE user_tbl (
  userid	varchar(128)	PRIMARY KEY NOT NULL,
  groupid	varchar(128)	NOT NULL REFERENCES group_tbl(groupid) ON DELETE RESTRICT,
  pid		int				REFERENCES person_tbl(pid) ON DELETE RESTRICT,
  passwd	varchar(128),
  valid		boolean			NOT NULL DEFAULT 't',
  gracetime int				NOT NULL DEFAULT '300',
  isonline  boolean			NOT NULL DEFAULT 'f',
  logintime  timestamp		NOT NULL DEFAULT TIMESTAMP 'now',
  lastactiontime timestamp	NOT NULL DEFAULT TIMESTAMP 'now',
  logouttime timestamp		NOT NULL DEFAULT TIMESTAMP 'now',
  logoutmsg varchar(128)	NOT NULL DEFAULT 'Grace time reached'
);
COMMIT;

BEGIN;
CREATE TABLE mauth_tbl (
  moduleid varchar(128)	NOT NULL REFERENCES module_tbl(moduleid) ON DELETE RESTRICT,
  groupid  varchar(128)	NOT NULL REFERENCES group_tbl(groupid) ON DELETE RESTRICT,
  vname	   varchar(128) DEFAULT 'all',
  vvalue   varchar(128) DEFAULT 'all',
  UNIQUE (moduleid, groupid, vname, vvalue)
);
COMMIT;

-- removed due to not full reload
-- BEGIN;
-- CREATE TABLE log_tbl (
--   userid	varchar(128),
--   logtxt	text,
--   query		text,
--   logtime	timestamp NOT NULL
-- );
-- COMMIT;
