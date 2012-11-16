#!/usr/bin/env python
import os, sys
import string
import time, datetime

import pg

# Package import
from optparse import OptionParser
import ConfigParser

def query_vlan( con, vltbl, output, snname ):
  # start query	
  query = 'SELECT * FROM '+vltbl+' ORDER BY vl_id'
  
  try:
	rs = con.query( query )
  except:
	fp.close()
	print "gen_dhcp.py: Query vlan FAILED"
	return 3

  retrs = rs.dictresult()
  if retrs == None:
	return 3

  try:
	fp = open( output, "w+" )
  except:
	print 'gen_dhcp.py: Output FAILED'
	return 3

  
  subnstr  = 'shared-network '+snname+' {\n'
  fp.write( subnstr )
  # print subnstr
  
  c_ok = 0

  for r in retrs:
	field = r['vl_net']
	if field == '': return 4
	vl_net = field.split('/')

	field = r['vl_mask']
	if field == '': return 4
	vl_mask = field

	field = r['vl_gw']
	if field == '': return 4
	vl_gw = field

	field = r['vl_hfrom']
	if field == '': return 4
	vl_hfrom = field

	field = r['vl_hto']
	if field == '': return 4
	vl_hto = field

	field = r['vl_id']
	if field == '': return 4
	vl_id = str(field)
	
	field = r['vl_name']
	vl_name = field	
	
	# print r
	subnstr  = '  # VLAN '+vl_id+' -- '+vl_name+'\n'
	subnstr += '  subnet '+vl_net[0]+' netmask '+vl_mask
	subnstr += ' {\n'
	subnstr += '\toption routers '+vl_gw+';\n'

# commented out by cseka, bad line.
# means dynamic range, not subnet range define
#	subnstr += '\trange '+vl_hfrom+' '+vl_hto+';\n'
	
	field = r['vl_dhcp_opts']
	if field != '':
	  farr = field.split(';')
	  for fa in farr:
		subnstr += '\t'+fa.strip()+';\n'
	  #end for
	# end if
	
	subnstr += '  }\n'
	c_ok +=1
	# print subnstr
	fp.write( subnstr )

  # end for
  subnstr = '}\n'
  # print subnstr
  fp.write( subnstr )

  info  = "gen_dhcp.py: vlan ok: "+str(c_ok)
  print info

  fp.close()
# end def


def query_user_host( con, usrtbl, htbl, output, nxsrv, bimg ):
  # start query	
  query  = 'SELECT pid, (name).forname, (name).surname,'
  query += ' hostname, ip4, mac, port, vl_id, mtype,'
  query += ' valid, hidden, dhcp, dns, expires FROM '+usrtbl
  query += ' INNER JOIN '+htbl+' USING(pid) WHERE mtype=\'pid\''
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

  try:
	fp = open( output, "w+" )
  except:
	print 'gen_dhcp.py: Output FAILED'
	return 3

  c_expired = 0
  c_invalid = 0
  c_nodhcp  = 0
  c_ok = 0

  for r in retrs:
  # TODO: valid dhcp expires test
  	field = r['expires']
	expires = field
	exdate = time.strptime( expires, "%Y-%m-%d" )
	todate = time.localtime()

  	field = r['valid']
	valid = field

  	field = r['dhcp']
	dhcp = field
	
	field = r['hostname']
	if field == '': return 4
	hostname = str(field)

	field = r['mac']
	if field == '': return 4
	mac = string.upper(str(field))

	field = r['ip4']
	if field == '': return 4
	ip = str(field)
	
	field = r['forname']
	name  = field
	field = r['surname']
	name += ' '+field	

	field = r['vl_id']
	vl_id = field	
		
	field = r['port']
	port = field	

	if exdate < todate:
	  print "gen_dhcp.py: EXPIRED: "+expires+" "+hostname+" ("+ip+") -- "+name
	  c_expired += 1
	  continue
	  
	if valid == 'f':
	  print "gen_dhcp.py: NOT VALID: "+expires+" "+hostname+" ("+ip+") -- "+name	  
	  c_invalid += 1
	  continue
	  
	if dhcp == 'f':
	  print "gen_dhcp.py: NO DHCP: "+expires+" "+hostname+" ("+ip+") -- "+name
	  c_nodhcp += 1	  
	  continue

	subnstr  = '\n# Responsible: '+name
	subnstr += '\n# VLAN '+str(vl_id)+' -- '+port+' expires: '+expires
	subnstr += '\nhost '+hostname+' {\n'
	subnstr += '\thardware ethernet '+mac+';\n'

	if nxsrv != '':
	  subnstr += '\tnext-server '+nxsrv+';\n'

	if bimg != '':
	  subnstr += '\tfilename "'+bimg+'";\n'
	  
	subnstr += '\tfixed-address '+ip+';\n'
	subnstr += '}\n\n'
	c_ok += 1
	fp.write( subnstr )
  # end for
  
  info  = "gen_dhcp.py: pid host ok: "+str(c_ok)+"  expired: "+str(c_expired)
  info += "  not valid: "+str(c_invalid)+"  no dhcp: "+str(c_nodhcp)
  print info
  
  fp.close()
# end def


def query_nopid_host( con, usrtbl, htbl, output, nxsrv, bimg ):
  # start query	
  query  = 'SELECT  hostname, ip4, mac, port, vl_id, mtype, rr_hinfo_txt,'
  query += ' valid, hidden, dhcp, dns, expires FROM '+htbl+' WHERE mtype=\'nopid\''
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

  try:
	fp = open( output, "w+" )
  except:
	print 'gen_dhcp.py: Output FAILED'
	return 3

  c_expired = 0
  c_invalid = 0
  c_nodhcp  = 0
  c_ok = 0

  for r in retrs:
  # TODO: valid dhcp expires test
  	field = r['expires']
	expires = field
	exdate = time.strptime( expires, "%Y-%m-%d" )
	todate = time.localtime()

  	field = r['valid']
	valid = field

  	field = r['dhcp']
	dhcp = field
	
	field = r['hostname']
	if field == '': return 4
	hostname = str(field)

	field = r['mac']
	if field == '': return 4
	mac = string.upper(str(field))

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
	  
	if dhcp == 'f':
	  c_nodhcp += 1
	  print "gen_dhcp.py: NO DHCP: "+expires+" "+hostname+" ("+ip+") -- "+htxt
	  continue

	subnstr  = '\n# Responsible: '+htxt
	subnstr += '\n# VLAN '+str(vl_id)+' -- '+port+' expires: '+expires
	subnstr += '\nhost '+hostname+' {\n'
	subnstr += '\thardware ethernet '+mac+';\n'

	if nxsrv != '':
	  subnstr += '\tnext-server '+nxsrv+';\n'

	if bimg != '':
	  subnstr += '\tfilename "'+bimg+'";\n'
	  
	subnstr += '\tfixed-address '+ip+';\n'
	subnstr += '}\n'
	c_ok += 1
	fp.write( subnstr )
  # end for

  info  = "gen_dhcp.py: nopid host ok: "+str(c_ok)+"  expired: "+str(c_expired)
  info += "  not valid: "+str(c_invalid)+"  no dhcp: "+str(c_nodhcp)
  print info
  
  fp.close()
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
	print "gen_dhcp.py: Config file error"
	return 1	
	
  print "gen_dhcp.py: Reading config: " + str( cmd_options.config_file )    
  # parse config file end
	
  # get common options from config
  sqlhost  = str( cfg_parser.get( "Common", "sqlhost" ) )
  sqldb    = str( cfg_parser.get( "Common", "sqldb" ) )
  sqlusr   = str( cfg_parser.get( "Common", "sqluser" ) )
  sqlpass  = str( cfg_parser.get( "Common", "sqlpass" ) )
  vlan_tbl = str( cfg_parser.get( "Common", "vlan_table" ) )
  host_tbl = str( cfg_parser.get( "Common", "host_table" ) )
  user_tbl = str( cfg_parser.get( "Common", "user_table" ) )

  # get hdcp options from config
  snname   = str( cfg_parser.get( "DHCP", "shared_network_name" ) )
  dhcp_vlan_out = str( cfg_parser.get( "DHCP", "vlan_output" ) )
  dhcp_user_out = str( cfg_parser.get( "DHCP", "user_output" ) )
  dhcp_nopid_out = str( cfg_parser.get( "DHCP", "nopid_output" ) )  
  dhcp_next_srv = str( cfg_parser.get( "DHCP", "next_server" ) )
  dhcp_boot_img = str( cfg_parser.get( "DHCP", "boot_file" ) )

  # connect
  try:
	con = pg.connect( dbname=sqldb, host=sqlhost, user=sqlusr, passwd=sqlpass )
  except:
	print "gen_dhcp.py: Connection FAILED"
	return 2

  now = datetime.datetime.now()
  # print 'gen_dhcp.py: '+now.ctime()
  print 'gen_dhcp.py: DHCP Config Generation Started at '+str(now)

  query_vlan( con, vlan_tbl, dhcp_vlan_out, snname )
  query_user_host( con, user_tbl, host_tbl, dhcp_user_out, dhcp_next_srv, dhcp_boot_img )
  query_nopid_host( con, user_tbl, host_tbl, dhcp_nopid_out, dhcp_next_srv, dhcp_boot_img )

  # close connection  
  con.close()
  
  now = datetime.datetime.now()  
  print 'gen_dhcp.py: DHCP Config Generation Finished at '+str(now)  
  
  return 0
# end def

# MAIN FUNCTION END

main()
