#!/usr/bin/env python
import os, sys
import string
import time, datetime

import pg

# Package import
from optparse import OptionParser
import ConfigParser


def gen_revers_dns( con, net, domain, htbl, outdir ):
  query  = 'SELECT hostname, ip4,'
  query += ' valid, dns, expires FROM '+htbl+' WHERE ip4::varchar ~ \''+net+'\''
  query += ' ORDER BY ip4'
  
  try:
	rs = con.query( query )
	# print rs
  except:
	print "gen_dns.py: Query hosts FAILED"
	return 3

  retrs = rs.dictresult()
  if retrs == None:
	print "gen_dns.py: No results for "+net
	return 3

  output = outdir+"/"+net+".conf"
  try:
	fp = open( output, "w+" )
  except:
	print 'gen_dns.py: Output '+output+' FAILED'
	return 3

  c_expired = 0
  c_invalid = 0
  c_nodns   = 0
  c_ok = 0

  for r in retrs:
  # TODO: valid dhcp expires test
  	field = r['expires']
	expires = field
	exdate = time.strptime( expires, "%Y-%m-%d" )
	todate = time.localtime()

  	field = r['valid']
	valid = field

  	field = r['dns']
	dns = field
	
	field = r['hostname']
	if field == '': return 4
	hostname = str(field)

	field = r['ip4']
	if field == '': return 4
	
	farr = str(field).split(".")
	ip = str(farr[3])
	ip4 = field

	if exdate < todate:
	  print "gen_dns.py: EXPIRED: "+expires+" "+hostname+" ("+ip4+")"
	  c_expired += 1
	  continue
	  
	if valid == 'f':
	  print "gen_dns.py: NOT VALID: "+expires+" "+hostname+" ("+ip4+")"
	  c_invalid += 1
	  continue
	  
	if dns == 'f':
	  print "gen_dns.py: NO DNS: "+expires+" "+hostname+" ("+ip4+")"
	  c_nodns += 1
	  continue

	fqdn = hostname+'.'+domain+'.'
	
	subnstr  = "%-20.20s" % ip
	subnstr += '   IN  PTR   '+fqdn+'\n'
	c_ok += 1
	fp.write( subnstr )
  # end for
  
  info  = "gen_dns.py: net: "+net+".*  ok: "+str(c_ok)+"  | expired: "+str(c_expired)
  info += "  | not valid: "+str(c_invalid)+"  | no dns: "+str(c_nodns)
  print info

  fp.close()
# end def


def get_cnames( hn, hnl, rrs ):
  mid = hnl[hn]
  cnl = []
  
  for r in rrs:
	if mid == r['mid']:
	  cname = r['rec_hostname']
	  try:
		hnl[cname]
	  except:
		print "gen_dns.py: cname "+cname+" for "+hn
		cnl.append( cname )
	  else:
		print "gen_dns.py: duplicated name: "+cname
	# end if
  # end for
  return cnl
# end def


def gen_dns( con, domain, htbl, rtbl, utbl, outdir ):
  # cnames
  query  = 'SELECT * FROM '+rtbl
  
  try:
	rrs = con.query( query )
  except:
	print "gen_dns.py: Query records FAILED"
	return 3

  recrs = rrs.dictresult()


  # normal dns
  query  = 'SELECT pid, mid, mtype, hostname, ip4, rr_hinfo_txt,'
  query += ' valid, dns, expires FROM '+htbl
  query += ' ORDER BY hostname'

  try:
	rs = con.query( query )
	# print rs
  except:
	print "gen_dns.py: Query hosts FAILED"
	return 3

  retrs = rs.dictresult()
  if retrs == None:
	print "gen_dns.py: No results for "+domain
	return 3

  # bild hostname list
  hnl = {}
  for r in retrs:
	hnl[r['hostname']] = r['mid']

  # output
  output = outdir+"/"+domain+".conf"
  try:
	fp = open( output, "w+" )
  except:
	print 'gen_dns.py: Output '+output+' FAILED'
	return 3

  c_expired = 0
  c_invalid = 0
  c_nodns   = 0
  c_ok = 0

  for r in retrs:
  # TODO: valid dhcp expires test
  	field = r['expires']
	expires = field
	exdate = time.strptime( expires, "%Y-%m-%d" )
	todate = time.localtime()

  	field = r['valid']
	valid = field

  	field = r['dns']
	dns = field

  	field = r['mtype']
	mtype = field

  	field = r['rr_hinfo_txt']
	hinfo = field	
	
	field = r['hostname']
	if field == '': return 4
	hostname = str(field)

	field = r['ip4']
	if field == '': return 4
	ip = str(field)

	if exdate < todate:
	  print "gen_dns.py: EXPIRED: "+expires+" "+hostname+" ("+ip+")"
	  c_expired += 1
	  continue
	  
	if valid == 'f':
	  print "gen_dns.py: NOT VALID: "+expires+" "+hostname+" ("+ip+")"
	  c_invalid += 1
	  continue
	  
	if dns == 'f':
	  print "gen_dns.py: NO DNS: "+expires+" "+hostname+" ("+ip+")"
	  c_nodns += 1
	  continue

	fqdn = hostname+'.'+domain+'.'
	
	subnstr  = "%-25.25s" % hostname
	subnstr += '   IN  A       '+ip+'\n'
	fp.write( subnstr )

	if mtype == 'nopid':
	  if hinfo != None:
		subnstr  = "%-25.25s" % ''
		subnstr += '   IN  TXT     "'+hinfo+'"\n'
		fp.write( subnstr )
	  # end if
	else:
	  pid = r['pid']
	  uquery  = "SELECT pid, (name).forname, (name).surname, roomid FROM ";
	  uquery += utbl+" WHERE pid = '"+str(pid)+"'"
	  
	  try:
		urs = con.query( uquery )
  		# print rs
	  except:
		print "gen_dns.py: Query user FAILED"
	  else:
		durs = urs.dictresult()
		utxt = durs[0]['forname']+' '+durs[0]['surname']+' '+durs[0]['roomid']
		if hinfo != None:
		  utxt += ' -- '+hinfo

		subnstr  = "%-25.25s" % ''
		subnstr += '   IN  TXT     "'+utxt+'"\n'
		fp.write( subnstr )		
	# end if
	c_ok += 1

	# cnames	
	cnl = get_cnames( hostname, hnl, recrs )
	if len( cnl ) > 0:
	  for cn in cnl:
		subnstr  = "%-25.25s" % cn
		subnstr += '   IN  CNAME   '+hostname+'\n'
		fp.write( subnstr )
	  # end for
	# end if
	
	
  # end for
  
  info  = "gen_dns.py: net: "+domain+"  ok: "+str(c_ok)+"  | expired: "+str(c_expired)
  info += "  | not valid: "+str(c_invalid)+"  | no dns: "+str(c_nodns)
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
	print "gen_dns.py: Config file error"
	return 1	
	
  print "gen_dns.py: Reading config: " + str( cmd_options.config_file )    
  # parse config file end
	
  # TODO: locking!

  # get common options from config
  sqlhost  = str( cfg_parser.get( "Common", "sqlhost" ) )
  sqldb    = str( cfg_parser.get( "Common", "sqldb" ) )
  sqlusr   = str( cfg_parser.get( "Common", "sqluser" ) )
  sqlpass  = str( cfg_parser.get( "Common", "sqlpass" ) )
  vlan_tbl = str( cfg_parser.get( "Common", "vlan_table" ) )
  host_tbl = str( cfg_parser.get( "Common", "host_table" ) )
  user_tbl = str( cfg_parser.get( "Common", "user_table" ) )
  reco_tbl = str( cfg_parser.get( "Common", "record_table" ) )


  # get dns options from config
  dns_outdir = str( cfg_parser.get( "DNS", "output_dir" ) )
  dns_domain = str( cfg_parser.get( "DNS", "domain" ) )

  # connect
  try:
	con = pg.connect( dbname=sqldb, host=sqlhost, user=sqlusr, passwd=sqlpass )
  except:
	print "gen_dns.py: Connection FAILED"
	return 2

  now = datetime.datetime.now()
  print 'gen_dns.py: DNS Config Generation Started at '+str(now)

  gen_revers_dns( con, '', dns_domain, host_tbl, dns_outdir )

  gen_dns( con, dns_domain, host_tbl, reco_tbl, user_tbl, dns_outdir )
  
  # close connection  
  con.close()

  now = datetime.datetime.now()
  print 'gen_dns.py: DNS Config Generation Finished at '+str(now)
  
  return 0
# end def

# MAIN FUNCTION END

main()
