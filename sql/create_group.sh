#!/bin/bash

. ./common.conf

echo
echo "Create new group for Inferno"
echo
echo -n "Group name: "
read gn

echo -n "Group Description: "
read gd

echo $gn $gd
sql_insert group_tbl "groupid,descr" "'$gn', '$gd'"
