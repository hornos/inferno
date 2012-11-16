#!/usr/bin/env python
import os, sys
import string
import time, datetime

import pg

# Package import
from optparse import OptionParser
import ConfigParser


def query_user_host( con, usrtbl, htbl, output ):
  # start query	
  query  = 'SELECT pid, (name).forname, (name).surname,'
  query += ' hostname, ip4, mac, port, vl_id, mtype,'
  query += ' valid, wifi, eap, expires FROM '+usrtbl
  query += ' INNER JOIN '+htbl+' USING(pid) WHERE mtype=\'pid\''
  query += ' ORDER BY ip4'
  
  try:
	rs = con.query( query )
	# print rs
  except:
	print "gen_radius.py: Query users: Query FAILED"
	return 3

  retrs = rs.dictresult()
  if retrs == None:
	return 3


  c_expired = 0
  c_invalid = 0
  c_nowifi  = 0
  c_ok = 0

  for r in retrs:
  # TODO: valid dhcp expires test
  	field = r['expires']
	expires = field
	exdate = time.strptime( expires, "%Y-%m-%d" )
	todate = time.localtime()

  	field = r['valid']
	valid = field

  	field = r['wifi']
	wifi = field

  	field = r['eap']
	eap = field
	
	field = r['hostname']
	if field == '': return 4
	hostname = str(field)

	field = r['ip4']
	if field == '': return 4
	ip = str(field)
	
	field = r['mac']
	if field == '': return 4
	mac = string.lower(str(field))
	mac = mac.replace(':','')
	
	field = r['forname']
	name  = field
	field = r['surname']
	name += ' '+field	

	field = r['vl_id']
	vl_id = field	
		
	field = r['port']
	port = field	

	# create the wid TODO: move it from here

	if exdate < todate:
	  print "gen_radius.py: EXPIRED: "+expires+" "+hostname+" ("+ip+") -- "+name
	  c_expired += 1
	  continue
	  
	if valid == 'f':
	  print "gen_radius.py: NOT VALID: "+expires+" "+hostname+" ("+ip+") -- "+name	  
	  c_invalid += 1
	  continue
	  
	if wifi == 'f':
	  # print "gen_radius.py: NO Wifi: "+expires+" "+hostname+" ("+ip+") -- "+name
	  c_nowifi += 1	  
	  continue

	subnstr  = '\n# Responsible: '+name
	subnstr += '\n# VLAN '+str(vl_id)+' -- '+port+' expires: '+expires
	subnstr += '\n# '+hostname+' ('+ip+')'
	subnstr += '\n"'+hostname+'" Auth-Type = PAP,Cleartext-Password := "'+eap+'",'
	subnstr += ' Calling-Station-Id == "'+mac+'"\n'
	
	c_ok += 1
	output.write( subnstr )
  # end for
  
  info  = "gen_radius.py: pid host ok: "+str(c_ok)+"  expired: "+str(c_expired)
  info += "  not valid: "+str(c_invalid)+"  no wifi: "+str(c_nowifi)
  print info
# end def


def query_nopid_host( con, usrtbl, htbl, output ):
  # start query	
  query  = 'SELECT  hostname, ip4, port, vl_id, mtype, rr_hinfo_txt,'
  query += ' valid, wifi, eap, expires FROM '+htbl+' WHERE mtype=\'nopid\''
  query += ' ORDER BY ip4'
  
  try:
	rs = con.query( query )
	# print rs
  except:
	print "gen_dhcp.py: Query users: Query FAILED"
	return 3

  retrs = rs.dictresult()
  if retrs == None:
	return 3

  c_expired = 0
  c_invalid = 0
  c_nowifi  = 0
  c_ok = 0

  for r in retrs:
  # TODO: valid dhcp expires test
  	field = r['expires']
	expires = field
	exdate = time.strptime( expires, "%Y-%m-%d" )
	todate = time.localtime()

  	field = r['valid']
	valid = field

  	field = r['wifi']
	wifi = field

  	field = r['eap']
	eap = field
	
	field = r['hostname']
	if field == '': return 4
	hostname = str(field)

	field = r['ip4']
	if field == '': return 4
	ip = str(field)
	
	field = r['vl_id']
	vl_id = field	

	field = r['rr_hinfo_txt']
	htxt = str(field)	
		
	field = r['port']
	port = field	

	if exdate < todate:
	  print "gen_dhcp.py: EXPIRED: "+expires+" "+hostname+" ("+ip+") -- "+htxt
	  c_expired += 1
	  continue
	  
	if valid == 'f':
	  print "gen_dhcp.py: NOT VALID: "+expires+" "+hostname+" ("+ip+") -- "+htxt
	  c_invalid += 1
	  continue
	  
	if wifi == 'f':
	  c_nowifi += 1
	  # print "gen_radius.py: NO Wifi: "+expires+" "+hostname+" ("+ip+") -- "+htxt
	  continue

	subnstr  = '\n# Responsible: '+htxt
	subnstr += '\n# VLAN '+str(vl_id)+' -- '+port+' expires: '+expires
	subnstr += '\n# '+hostname+' ('+ip+')'
	subnstr += '\n"'+hostname+'" Auth-Type = PAP,Cleartext-Password := "'+eap+'"\n'

	c_ok += 1
	output.write( subnstr )
  # end for

  info  = "gen_radius.py: nopid host ok: "+str(c_ok)+"  expired: "+str(c_expired)
  info += "  not valid: "+str(c_invalid)+"  no wifi: "+str(c_nowifi)
  print info
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
	print "gen_radius.py: Config file error"
	return 1	
	
  print "gen_radius.py: Reading config: " + str( cmd_options.config_file )    
  # parse config file end
	
  # get common options from config
  sqlhost  = str( cfg_parser.get( "Common", "sqlhost" ) )
  sqldb    = str( cfg_parser.get( "Common", "sqldb" ) )
  sqlusr   = str( cfg_parser.get( "Common", "sqluser" ) )
  sqlpass  = str( cfg_parser.get( "Common", "sqlpass" ) )
  host_tbl = str( cfg_parser.get( "Common", "host_table" ) )
  user_tbl = str( cfg_parser.get( "Common", "user_table" ) )

  # get hdcp options from config
  radius_users = str( cfg_parser.get( "RADIUS", "radius_users" ) )

  # connect
  try:
	con = pg.connect( dbname=sqldb, host=sqlhost, user=sqlusr, passwd=sqlpass )
  except:
	print "gen_radius.py: Connection FAILED"
	return 2

  now = datetime.datetime.now()


  try:
	fp = open( radius_users, "w+" )
  except:
	print 'gen_radius.py: Output FAILED'
	return 3


  print 'gen_radius.py: Radius Config Generation Started at '+str(now)

  query_user_host( con, user_tbl, host_tbl, fp )
  query_nopid_host( con, user_tbl, host_tbl, fp )

  # close connection  
  con.close()
  fp.close()
    
  now = datetime.datetime.now()  
  print 'gen_radius.py: Radius Config Generation Finished at '+str(now)  
  
  return 0
# end def

# MAIN FUNCTION END

main()
