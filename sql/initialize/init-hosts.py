#!/usr/bin/env python
import os, sys
import string, re
import time, datetime

import pg

# Package import
from optparse import OptionParser
import ConfigParser

def sqlfmt( s ):
  return "'"+s+"'"
# end def


def init_nopid_host( con, htbl, hfile ):
  try:
	fp = open( hfile, "r" )
  except:
	print 'init-hosts.py: Input FAILED'
	return 3
  
  for line in fp:
	if not re.compile('^#').match( line ):
	  arr = line.split('|')
	  
	  ok = True
	  for i in range(0,4):
		if arr[i].strip() == '':
		  ok = False
		# end if
	  # end for
	  
	  if not ok:
		print 'init-hosts.py: '+line+' ERROR'
		continue
	  
	  # ip | mac | hostname	| port | vlan | responsible | comment
	  ip		= arr[0].strip()
	  mac		= arr[1].strip()
	  hostname	= arr[2].strip()
	  port		= arr[3].strip()
	  vlan		= arr[4].strip()
	  resp		= arr[5].strip()
	  comment	= arr[6].strip()
	  cdate		= datetime.date.today().strftime( "%Y-%m-%d" )
	  lastmtime = datetime.date.today().strftime( "%Y-%m-%d %H:%M:%S" )
	  is_dhcp = 'f'
	  if vlan == '112':
		is_dhcp = 't'
	
	  # !!!
	  is_dhcp = 't'

	  query  = 'INSERT INTO '+htbl+' (hostname,ip4,mac,port,vl_id,mtype,'
	  query += 'valid,hidden,dhcp,dns,wifi,eap,expires,cdate,lastmtime,rr_hinfo_txt,comment)'
	  query += ' VALUES ('
	  query += sqlfmt(hostname)+','
	  query += sqlfmt(ip)+','
	  query += sqlfmt(string.upper(mac))+','
	  query += sqlfmt(port)+','
	  query += sqlfmt(vlan)+','
	  query += sqlfmt('nopid')+','
	  query += sqlfmt('t')+','
	  query += sqlfmt('t')+','
	  query += sqlfmt(is_dhcp)+','
	  query += sqlfmt('t')+','
	  query += sqlfmt('f')+','
	  query += sqlfmt('notset')+','
	  query += sqlfmt('2038-01-01')+','
	  query += sqlfmt(cdate)+','
	  query += sqlfmt(lastmtime)+','
	  query += sqlfmt(resp)+','	  
	  query += sqlfmt(comment)
	  query += ')'

	  try:
		con.query( query )
		
  	  except:
		print "init-hosts.py: insert ERROR: "+query
  
	# end if
  # end for
# end def


def init_pid_host( con, htbl, hfile ):
  try:
	fp = open( hfile, "r" )
  except:
	print 'init-hosts.py: Input FAILED'
	return 3
  
  for line in fp:
	if not re.compile('^#').match( line ):
	  arr = line.split('|')
	  
	  ok = True
	  for i in range(0,4):
		if arr[i].strip() == '':
		  ok = False
		# end if
	  # end for
	  
	  if not ok:
		print 'init-hosts.py: '+line+' ERROR'
		continue
	  
	  # ip | mac | hostname	| port | vlan | responsible | comment
	  ip		= arr[0].strip()
	  mac		= arr[1].strip()
	  hostname	= arr[2].strip()
	  port		= arr[3].strip()
	  vlan		= arr[4].strip()
	  pid		= arr[5].strip()
	  comment	= arr[6].strip()
	  cdate		= datetime.date.today().strftime( "%Y-%m-%d" )
	  lastmtime = datetime.date.today().strftime( "%Y-%m-%d %H:%M:%S" )
	  is_dhcp = 'f'
	  if vlan == '112':
		is_dhcp = 't'
	  
	  is_dhcp = 't'

	  query  = 'INSERT INTO '+htbl+' (hostname,ip4,mac,port,vl_id,pid,mtype,'
	  query += 'valid,hidden,dhcp,dns,wifi,eap,expires,cdate,lastmtime,comment)'
	  query += ' VALUES ('
	  query += sqlfmt(hostname)+','
	  query += sqlfmt(ip)+','
	  query += sqlfmt(string.upper(mac))+','
	  query += sqlfmt(port)+','
	  query += sqlfmt(vlan)+','
	  query += sqlfmt(pid)+','	  
	  query += sqlfmt('pid')+','
	  query += sqlfmt('t')+','
	  query += sqlfmt('t')+','
	  query += sqlfmt(is_dhcp)+','
	  query += sqlfmt('t')+','
	  query += sqlfmt('f')+','
	  query += sqlfmt('notset')+','
	  query += sqlfmt('2038-01-01')+','
	  query += sqlfmt(cdate)+','
	  query += sqlfmt(lastmtime)+','
	  query += sqlfmt(comment)
	  query += ')'

	  try:
		con.query( query )
		
  	  except:
		print "init-hosts.py: insert ERROR: "+query
  
	# end if
  # end for
# end def



def init_temp_host( con, htbl, hfile ):
  try:
	fp = open( hfile, "r" )
  except:
	print 'init-hosts.py: Input FAILED'
	return 3
  
  for line in fp:
	if not re.compile('^#').match( line ):
	  arr = line.split('|')
	  
	  ok = True
	  for i in range(0,4):
		if arr[i].strip() == '':
		  ok = False
		# end if
	  # end for
	  
	  if not ok:
		print 'init-hosts.py: '+line+' ERROR'
		continue
	  
	  # name | ip | mac	| hostname | port | expires
	  name		= arr[0].strip()
	  ip		= arr[1].strip()
	  mac		= arr[2].strip()
	  hostname	= arr[3].strip()
	  port		= arr[4].strip()
	  vlan		= '112'
	  rr_hinfo_txt = 'TEMP - '+name
	  comment	= 'TEMP'
	  cdate		= datetime.date.today().strftime( "%Y-%m-%d" )
	  lastmtime = datetime.date.today().strftime( "%Y-%m-%d %H:%M:%S" )
	  expires   = '2007-09-02'

	  query  = 'INSERT INTO '+htbl+' (hostname,ip4,mac,port,vl_id,mtype,'
	  query += 'valid,hidden,dhcp,dns,wifi,eap,expires,cdate,lastmtime,rr_hinfo_txt,comment)'
	  query += ' VALUES ('
	  query += sqlfmt(hostname)+','
	  query += sqlfmt(ip)+','
	  query += sqlfmt(string.upper(mac))+','
	  query += sqlfmt(port)+','
	  query += sqlfmt(vlan)+','	  
	  query += sqlfmt('nopid')+','
	  query += sqlfmt('t')+','
	  query += sqlfmt('t')+','
	  query += sqlfmt('t')+','
	  query += sqlfmt('t')+','
	  query += sqlfmt('f')+','
	  query += sqlfmt('notset')+','
	  query += sqlfmt(expires)+','
	  query += sqlfmt(cdate)+','
	  query += sqlfmt(lastmtime)+','
	  query += sqlfmt(rr_hinfo_txt)+','	  
	  query += sqlfmt(comment)
	  query += ')'

	  try:
		con.query( query )		
  	  except:
		print "init-hosts.py: insert ERROR: "+query
  
	# end if
  # end for
# end def



def init_cname( con, htbl, rtbl, hfile ):
  try:
	fp = open( hfile, "r" )
  except:
	print 'init-hosts.py: Input FAILED'
	return 3
  
  for line in fp:
	if not re.compile('^#').match( line ):
	  arr = line.split('|')
	  
	  ok = True
	  for i in range(0,2):
		if arr[i].strip() == '':
		  ok = False
		# end if
	  # end for
	  
	  if not ok:
		print 'init-hosts.py: '+line+' ERROR'
		continue
	  # end if
	
	  hostname = arr[0].strip()
	  cnames   = arr[1].strip()
	  cnarr = cnames.split(',')
	
	  for cn in cnarr:
		cnok = cn.strip()
		hnok = hostname
	  
		# serach host mid
		query  = 'SELECT mid, hostname FROM '+htbl
		query += ' WHERE hostname = '+sqlfmt(hnok)
	  
		try:
		  rs = con.query( query )
		except:
		  print "init-hosts.py: Host query failed for "+hnok
		  continue
		
		drs = rs.dictresult()
		mid = drs[0]['mid']
	  
		query  = 'INSERT INTO '+rtbl+' (mid,rec_type,rec_hostname)'
		query += ' VALUES ('+sqlfmt(str(mid))+','+sqlfmt('CNAME')+','+sqlfmt(cnok)+')'

		try:
		  con.query( query )		
  		except:
		  print "init-hosts.py: cname insert ERROR: "+query
	
	  # end for
	# end if
  # end for
# end def




#-------------------------------------------------#
# MAIN FUNCTION BEGIN                             #
#-------------------------------------------------#
def main():
  # parse command line begin
  cmd_parser = OptionParser()
  cfg_parser = ConfigParser.ConfigParser()

  cmd_parser.add_option( "-c", "--config",
			action = "store", type = "string", dest = "config_file", default = "/home/inferno/python/config.ini",
			help = "set configuration file (default: config.ini)", metavar="CONFIG" )

  (cmd_options, args) = cmd_parser.parse_args()
    
	
    # parse config file
  try:
	cfg_parser.read( cmd_options.config_file )
  except:
	print "init-hosts.py: Config file error"
	return 1	
	
  print "init-hosts.py: Reading config: " + str( cmd_options.config_file )    
  # parse config file end
	
  # get common options from config
  sqlhost  = str( cfg_parser.get( "Common", "sqlhost" ) )
  sqldb    = str( cfg_parser.get( "Common", "sqldb" ) )
  sqlusr   = str( cfg_parser.get( "Common", "sqluser" ) )
  sqlpass  = str( cfg_parser.get( "Common", "sqlpass" ) )
  vlan_tbl = str( cfg_parser.get( "Common", "vlan_table" ) )
  host_tbl = str( cfg_parser.get( "Common", "host_table" ) )
  user_tbl = str( cfg_parser.get( "Common", "user_table" ) )
  record_tbl = str( cfg_parser.get( "Common", "record_table" ) )

  #
  nopid_host_file = str( cfg_parser.get( "HOSTS", "nopid_host_file" ) )
  pid_host_file   = str( cfg_parser.get( "HOSTS", "pid_host_file" ) )
  temp_host_file  = str( cfg_parser.get( "HOSTS", "temp_host_file" ) )
  cname_file      = str( cfg_parser.get( "HOSTS", "cname_file" ) )
  
  # connect
  try:
	con = pg.connect( dbname=sqldb, host=sqlhost, user=sqlusr, passwd=sqlpass )
  except:
	print "init-hosts.py: Connection FAILED"
	return 2

  now = datetime.datetime.now()
  # print 'gen_dhcp.py: '+now.ctime()
  print 'init-hosts.py: start '+str(now)

  init_nopid_host( con, host_tbl, nopid_host_file )
  init_pid_host( con, host_tbl, pid_host_file )  
  init_temp_host( con, host_tbl, temp_host_file )
  init_cname( con, host_tbl, record_tbl, cname_file )

  # close connection  
  con.close()
  
  now = datetime.datetime.now()  
  print 'init-hosts.py: and '+str(now)  
  
  return 0
# end def

# MAIN FUNCTION END

main()
