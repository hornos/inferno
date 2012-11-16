#!/bin/bash

. ./common.conf

# echo
# echo insert
# echo ${*}
# echo
sql_insert "${1}" "${2}" "${3}"
