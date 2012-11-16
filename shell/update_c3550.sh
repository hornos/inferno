#!/bin/bash

if ! test -r /home/inferno/shell/common.conf; then
  exit 1
fi
. /home/inferno/shell/common.conf

scname="update_c3550.sh"
lockname="update_c3550"
# python_gen="${INFERNO_PYTHON}/gen_dns.py"
# shell_gen="${INFERNO_SHELL}/gen_bind_conf.sh"
dns_dir="${INFERNO_DIR}/c3550"

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Started at ${now}"
echo "DHCP binding, no config generation"
#create_lock ${lockname}
#if test $? -eq 0; then
#  echo "${scname}: UNDER LOCK -- bind update will be skipped"
#  exit 1
#fi


#${python_gen}
#if ! test $? -eq 0; then
#  echo "${scname}: SQL FAILED"
#  delete_lock ${lockname}
#  exit 1
#fi
#echo "${scname}: SQL OK"


#${shell_gen}
#if ! test $? -eq 0; then
#  echo "${scname}: Build bind configuration FAILED"
#  delete_lock ${lockname}
#  exit 1
#fi
#echo "${scname}: Build bind configuration OK"


# if ! test -r "${dhcp_dir}/dhcpd.conf"; then
#  echo "update_dhcpd.sh: backup and restart dhcpd server FAILED"
#  delete_lock ${lockname}
#  exit 1
# fi
# echo "update_dhcpd.sh: backup and restart dhcpd server OK"


# echo -n "update_dhcpd.sh: "
# cp -fv /etc/dhcpd.conf "${BACKUP_DHCP}/dhcpd.conf.${now}"
# if ! test $? -eq 0; then
#  delete_lock ${lockname}
#  exit 1
# fi


# echo -n "update_dhcpd.sh: "
# cp -fv "${dhcp_dir}/dhcpd.conf" /etc/dhcpd.conf
# if ! test $? -eq 0; then
#  delete_lock ${lockname}
#  exit 1
# fi

# suse specific
#echo "${scname}: Restarting the bind server"
#/etc/rc.d/named restart
#if ! test $? -eq 0; then
#  delete_lock ${lockname}
#  exit 1
#fi

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Finished at ${now}"

delete_lock ${lockname}

exit 0
