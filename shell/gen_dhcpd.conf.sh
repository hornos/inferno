#!/bin/bash

scname="gen_dhcpd.conf.sh"

if ! test -r /home/inferno/shell/common.conf; then
  echo "${scname}: common.conf not found"
  exit 1
fi
. /home/inferno/shell/common.conf

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Build dhcpd.conf Started at ${now}"

dhcp_dir="${INFERNO_DIR}/dhcp"
conf="${dhcp_dir}/dhcpd.conf"

echo -n "${scname}: "
rm -vf "${conf}"

ok=1

if ! test -r "${dhcp_dir}/dhcp_header.conf"; then
  ok=0
fi

if ! test -r "${dhcp_dir}/dhcp_vlan.conf"; then
  ok=0
fi

if ! test -r "${dhcp_dir}/dhcp_user.conf"; then
  ok=0
fi

if ! test -r "${dhcp_dir}/dhcp_nopid.conf"; then
  ok=0
fi

if ! test $ok -eq 1; then
  echo "${scname}: dhcp build files not found"
  exit 1
fi

echo "#"							> "${conf}"
echo "# Generated at ${now}"	   >> "${conf}"
echo "#"						   >> "${conf}"
cat "${dhcp_dir}/dhcp_header.conf" >> "${conf}"
cat "${dhcp_dir}/dhcp_vlan.conf"   >> "${conf}"
cat "${dhcp_dir}/dhcp_user.conf"   >> "${conf}"
cat "${dhcp_dir}/dhcp_nopid.conf"  >> "${conf}"

if test -r "${dhcp_dir}/dhcp_footer.conf"; then
  cat "${dhcp_dir}/dhcp_footer.conf"  >> "${conf}"
fi

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Build dhcpd.conf Finished at ${now}"

exit 0
