#!/bin/bash

. ./common.conf

function reinit_address_tbl() {
  sqlfile ./sql-address.sql
}

function reload_address_tbl() {
  psql -f ./sql-init-addr.sql inferno inferno;
}


### MAIN

echo " Reloading address_tbl"
echo
reinit_address_tbl 1> /dev/null
reload_address_tbl 1> /dev/null
