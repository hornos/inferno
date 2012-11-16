#!/usr/bin/env python
# coding=UTF8

import os, sys
import string
import time, datetime
import md5

import pg

# Package import
from optparse import OptionParser
import ConfigParser

def conv2eng( s ):
  cdict = { 'ö' : 'o', 'ü' : 'u', 'ó' : 'o', 'ő' : 'o', 'ú' : 'u', 'é' : 'e', 'á' : 'a', 'ű' : 'u', 'í' : 'i',
			'Ö' : 'o', 'Ü' : 'u', 'Ó' : 'o', 'Ő' : 'o', 'Ú' : 'u', 'É' : 'e', 'Á' : 'a', 'Ű' : 'u', 'Í' : 'i' }
    
  for k, v in cdict.iteritems():
	s = s.replace( k, v )
	s = s.replace( ' ', '_' )
  # end for
  
  return s
# end def

def conv2eng2( s ):
  cdict = { 'ö' : 'o', 'ü' : 'u', 'ó' : 'o', 'ő' : 'o', 'ú' : 'u', 'é' : 'e', 'á' : 'a', 'ű' : 'u', 'í' : 'i',
			'Ö' : 'O', 'Ü' : 'U', 'Ó' : 'O', 'Ő' : 'O', 'Ú' : 'U', 'É' : 'E', 'Á' : 'A', 'Ű' : 'U', 'Í' : 'I' }
    
  for k, v in cdict.iteritems():
	s = s.replace( k, v )
  # end for
  
  return s
# end def



def gen_wid( con, usrtbl, widtbl ):
# clean up the shit
# WARNING
#
# query = 'DELETE FROM '+widtbl
# try:
#	rs = con.query( query )
#	# print rs
#  except:
#	print "gen_wid.py: Delete FAILED: "
#	return 3


  # start query	
  query  = 'SELECT pid, (name).forname, (name).surname,'
  query += ' isvalid, id, etrid, faculty, class, roomid FROM '+usrtbl
  query += ' WHERE ptype=\'student\''
  query += ' ORDER BY (name).forname, (name).surname'

  try:
	rs = con.query( query )
	# print rs
  except:
	print "gen_wid.py: Users Query FAILED: "
	return 3

  retrs = rs.dictresult()
  if retrs == None:
	return 3

  c_invalid = 0
  c_ok = 0
  c_up = 0


  for r in retrs:
  # TODO: valid dhcp expires test

  	field = r['isvalid']
	valid = field

  	field = r['etrid']
	etrid = field

  	field = r['id']
	persid = field

  	field = r['pid']
	pid = str(field)

  	field = r['faculty']
	faculty = field
	
  	field = r['class']
	fclass = field
	
	field = r['forname']
	fnname  = field
	field = r['surname']
	fnname += ' ' + field	

	wid = 'wid-'+etrid.lower()
	password = md5.md5( etrid.upper() ).hexdigest()
	identpass = password.lower()
	identpass = identpass[0:6]
	wpass = md5.md5(persid.upper()).hexdigest()


	# idents
	# 1. printer
	idents = "printer:"+identpass+"|samba:"+identpass

	query  = "INSERT INTO "+widtbl+" (wid,wpass,pid,idents)"
	query += ' VALUES(\''+wid+'\',\''+wpass+'\',\''+pid+'\',\''+idents+'\')'

#	print query


	try:
	  rs2 = con.query( query )
#	  print rs2
	except:
#	  print "insert failed: "+wid+" "+fnname
	  
	  query2  = "UPDATE "+widtbl+" SET "
#	  query2 += 'wid = \''+wid+'\','
	  query2 += 'wpass = \''+wpass+'\','
	  query2 += 'idents = \''+idents+'\''
	  query2 += ' WHERE pid = \''+pid+'\' AND wid = \''+wid+'\''
	  
	  try:
		rs3 = con.query( query2 )
	  except:
		print query2
		print "update failed: "+wid+" "+fnname
	  else:
		c_up += 1		
	  
	else:
	  c_ok += 1
  
  print "Update: "+str(c_up)
  print "Insert: "+str(c_ok)
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
	print "gen_wid.py: Config file error"
	return 1	
	
  print "gen_wid.py: Reading config: " + str( cmd_options.config_file )    
  # parse config file end
	
  # get common options from config
  sqlhost  = str( cfg_parser.get( "Common", "sqlhost" ) )
  sqldb    = str( cfg_parser.get( "Common", "sqldb" ) )
  sqlusr   = str( cfg_parser.get( "Common", "sqluser" ) )
  sqlpass  = str( cfg_parser.get( "Common", "sqlpass" ) )
  user_tbl = str( cfg_parser.get( "Common", "user_table" ) )
  wid_tbl = str( cfg_parser.get( "Common", "wid_table" ) )


  # connect
  try:
	con = pg.connect( dbname=sqldb, host=sqlhost, user=sqlusr, passwd=sqlpass )
  except:
	print "gen_wid.py: Connection FAILED"
	return 2

  now = datetime.datetime.now()


  print 'gen_wid.py: ID Generation Started at '+str(now)

  gen_wid( con, user_tbl, wid_tbl )

  # close connection  
  con.close()
    
  now = datetime.datetime.now()  
  print 'gen_wid.py: ID Generation Finished at '+str(now)  
  
  return 0
# end def

# MAIN FUNCTION END

main()
