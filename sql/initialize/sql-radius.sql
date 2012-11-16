-- DROP FUNCTION rfi_host() CASCADE;
-- DROP TRIGGER rfi_host_trig ON host_tbl;

-- Cleaning (tables)
DROP TABLE port_tbl		CASCADE;
DROP TABLE vlan_tbl		CASCADE;
DROP TABLE host_tbl		CASCADE;
DROP TABLE ndev_tbl		CASCADE;
DROP TABLE record_tbl	CASCADE;

BEGIN;
CREATE TABLE ndev_tbl (
  name		varchar(128) PRIMARY KEY NOT NULL,
  uport		varchar(128) NOT NULL,
  hostname	varchar(255),
  descr		text
);
COMMIT;

BEGIN;
CREATE TABLE port_tbl(
  port	varchar(128) PRIMARY KEY NOT NULL,
  ndev	varchar(128) NOT NULL REFERENCES ndev_tbl(name) ON DELETE RESTRICT,
  id	integer DEFAULT 0
);
COMMIT;

BEGIN;
CREATE TABLE vlan_tbl (
  vl_id		integer				PRIMARY KEY	NOT NULL,
  vl_name	varchar(255)		NOT NULL,
  vl_net	cidr				NOT NULL,
  vl_mask	inet				NOT NULL,
  vl_bcast	inet				NOT NULL,
  vl_gw		inet				NOT NULL,
  vl_hfrom	inet				NOT NULL,
  vl_hto	inet				NOT NULL,
  vl_dhcp_opts text				DEFAULT ''
);
COMMIT;

BEGIN;
CREATE TABLE host_tbl (
  mid		SERIAL			PRIMARY KEY,
  hostname	varchar(255)	NOT NULL UNIQUE,
  ip4		inet			NOT NULL UNIQUE,
  ip6		inet,
  mac		macaddr			NOT NULL UNIQUE,
  rr_hinfo_os	varchar(128),
  rr_hinfo_cpu	varchar(128),
  rr_hinfo_txt	varchar(128),
  rr_hinfo_ttl	integer,
  rr_hinfo_ptr	varchar(128),
  rr_aaaa		varchar(128),
  rr_aaaa_ptr	varchar(128),
  port		varchar(128)	REFERENCES port_tbl(port) ON DELETE RESTRICT,
  vl_id		integer			REFERENCES vlan_tbl(vl_id) ON DELETE RESTRICT,
  comment	text,
--  pid		integer,
  pid		integer			REFERENCES person_tbl(pid) ON DELETE RESTRICT,
  mtype		varchar(128)	NOT NULL DEFAULT 'pid',
  valid		boolean			NOT NULL DEFAULT 't',
  hidden	boolean			NOT NULL DEFAULT 'f',
  dhcp		boolean			NOT NULL DEFAULT 't',
  dns		boolean			NOT NULL DEFAULT 't',
  wifi		boolean			NOT NULL DEFAULT 'f',
  eap		varchar(128),
  cdate		date			NOT NULL,
  expires	date			NOT NULL,
  lastmtime	timestamp		NOT NULL
);
COMMIT;


BEGIN;
CREATE TABLE record_tbl (
  mid			integer			NOT NULL REFERENCES host_tbl ON DELETE CASCADE,
  rec_type		varchar(25)		NOT NULL,
  rec_hostname	varchar(255)	NOT NULL,
  rec_param		varchar(255),
  CHECK ( rec_type IN ( 'CNAME', 'MX', 'NS' ) ),
  UNIQUE( rec_hostname )
);
COMMIT;

GRANT SELECT ON host_tbl TO infernoro;
GRANT SELECT ON room_tbl TO infernoro;
GRANT SELECT ON person_tbl TO infernoro;
