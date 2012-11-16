#!/bin/bash

scname="gen_dhcpd.conf.sh"

if ! test -r /home/inferno/shell/common.conf; then
  echo "${scname}: common.conf not found"
  exit 1
fi
. /home/inferno/shell/common.conf

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Build Started at ${now}"

dns_dir="${INFERNO_DIR}/dns"
conf=("")
serial_file="${dns_dir}/dns_serial"
mask="__SERIAL__"

if ! test -r "${serial_file}"; then
  echo "${scname}: config serial file is missing for WARNING"
  csnum="01"
else
  csnum=`cat "${serial_file}" | awk '{printf("%02.2d", $1%100)}'`
  nsnum=`cat "${serial_file}" | awk '{printf("%02.2d", ($1+1)%100)}'`	
fi
  
echo ${nsnum} > "${serial_file}"
serial_date=`date "+%Y%m%d"`
serial_number=${serial_date}${csnum}


for i in ${conf[*]} ; do
  hdr="${dns_dir}/${i}.header"
  cfg="${dns_dir}/${i}.conf"
  zone="${dns_dir}/${i}"
  
  if ! test -r "${hdr}"; then
	echo "${scname}: header file missing for ${i} ERROR"
	exit 1
  fi
  
  if ! test -r "${cfg}"; then
	echo "${scname}: config file missing for ${i} ERROR"
	exit 1
  fi
  
  echo -n "${scname}: "
  rm -vf "${zone}"
    
  echo "${scname}: generating zone for ${i}"

  cat "${hdr}" | sed s/${mask}/${serial_number}/g   > "${zone}"
  cat "${cfg}" 									   >> "${zone}"
done

now=`date +"%Y-%m-%d[%H:%M:%S]"`
echo "${scname}: Build Finished at ${now}"

exit 0
