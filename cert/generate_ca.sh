#!/bin/bash

scname="generate_ca.sh"
cert_temp="./openssl.template"
cert_cnf="./openssl.cnf"

if ! test -r /home/inferno/shell/common.conf; then
  echo "${scname}: common.conf not found"
  exit 1
fi
. /home/inferno/shell/common.conf

cd "${INFERNO_CERTS}"

echo $1
echo $2
subs="CN	= $1" 
cat "${cert_temp}" | sed s/__CN__/${subs}/g > "${cert_cnf}"

./CA.client $1 $2 2> ./error
