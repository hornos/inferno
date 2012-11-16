#!/bin/bash

. ./common.conf

function reinit_room_tbl() {
  sqlfile ./sql-room.sql
}

function reload_room_tbl() {
  sql_insert T_room_tbl "rtype,descr" "'room','Szoba'"

  sql_insert room_tbl "roomid,number,floor,wing,cpcty,descr" "'A000','0','0','A','1000','Nem beosztott'"
  sql_insert room_tbl "roomid,number,floor,wing,cpcty,descr" "'C000','0','0','C','1000','Nem beosztott'"
  sql_insert room_tbl "roomid,number,floor,wing,cpcty,descr" "'B000','0','0','B','1000','Nem beosztott'"


  # !!!!
  for(( i=1; $i<5 ; ++i )) ; do
	for(( j=1; $j<15; ++j )); do
	  rn=$(($i*100+$j))
	  sql_insert room_tbl "roomid,wing,floor,number,descr" "'A${rn}','A','${i}','${j}',''"
	done
  done

  for(( i=1; $i<5 ; ++i )) ; do
	for(( j=1; $j<15; ++j )); do
	  rn=$(($i*100+$j))
	  sql_insert room_tbl "roomid,wing,floor,number,descr" "'B${rn}','B','${i}','${j}',''"
	done
  done

  for(( i=1; $i<5 ; ++i )) ; do
	for(( j=1; $j<15; ++j )); do
	  rn=$(($i*100+$j))
	  sql_insert room_tbl "roomid,wing,floor,number,descr" "'C${rn}','C','${i}','${j}',''"
	done
  done
}


### MAIN

echo " Reloading room_tbl"
echo
reinit_room_tbl 1> /dev/null
reload_room_tbl	1> /dev/null
