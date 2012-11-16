#!/bin/bash

. ./common.conf

echo
echo "Create new user for Inferno"
echo
sql_select "groupid,descr" group_tbl

echo -n "groupid: "
read wgid

echo -n "userid: "
read wuid

echo -n "password: "
read cpw

epw=`echo $cpw | md5sum | awk '{print $1}'`
# epw=`php -r "echo md5('$1');"`
# echo $epw
sql_insert user_tbl "userid,groupid,passwd" "'$wuid','$wgid', '$epw'"
