#!/bin/bash

. ./common.conf

function reinit_host() {
  sqlfile ./sql-host.sql
}

function reload_host() {
  echo "  5.1 Reload VLANs"
  cat ./data-vlans.txt | awk -F\| -f ./awk-vlans.awk 1> /dev/null
#  cat ./vlans.txt | awk -F\| -f ./vlans.awk
  
  echo "  5.2 Reload Devices"
  cat ./data-devs.txt | awk -F\| -f ./awk-devs.awk 1> /dev/null

  echo "  5.3 Reload Ports"  
  cat ./data-ports.txt | awk -F\| -f ./awk-ports.awk 1> /dev/null

  echo "  5.4 Reload nopid Hosts"  
  ./init-hosts.py

}


### MAIN

echo " Reloading host"
echo
reinit_host 1> /dev/null
reload_host
