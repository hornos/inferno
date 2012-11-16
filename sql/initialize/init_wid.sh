#!/bin/bash

. ./common.conf

function reinit_wid() {
  sqlfile ./sql-wid.sql
}

function reload_wid() {
  echo "not imp"
}

### MAIN

echo " Reloading wid"
echo
reinit_wid #1> /dev/null
reload_wid #1> /dev/null
