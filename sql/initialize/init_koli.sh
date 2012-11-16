#!/bin/bash

. ./common.conf

function reinit_koli() {
  sqlfile ./sql-koli.sql
}

function reload_koli() {
  sql_insert T_person_tbl "ptype,descr" "'student','Hallgató'"
  sql_insert T_person_tbl "ptype,descr" "'guest','Vendég'"
  sql_insert T_person_tbl "ptype,descr" "'wifi','Wifi felhasználó'"
}

function reload_users() {
  psql -f ./sql-init-user.sql inferno inferno
}



### MAIN

echo " Reloading koli"
echo
reinit_koli  1> /dev/null
reload_koli  1> /dev/null
reload_users 1> /dev/null
