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



def mass_redate( con, hosttbl, start, end, new ):

  # start query	
  query  = 'SELECT mid, expires, valid FROM '+hosttbl
  query += ' WHERE expires>\''+start+'\''
  query += ' AND expires<\''+end+'\''
  query += ' AND valid=\'t\''
   

  try:
	rs = con.query( query )
	# print rs
  except:
	print "mass_redate.py: Users Query FAILED: "
	return 3

  retrs = rs.dictresult()
  if retrs == None:
	return 3

  c_ok = 0

  for r in retrs:
  # TODO: valid dhcp expires test

  	field = r['mid']
	mid = field
#	print str(mid)

	query2  = "UPDATE "+hosttbl+" SET "
	query2 += 'expires = \''+new+'\''
	query2 += ' WHERE mid = \''+str(mid)+'\''
	
#	print query2  
	try:
	  rs3 = con.query( query2 )
	except:
	  print query2
	  print "update failed: "+str(mid)
	else:
	  c_ok += 1		
#  
  print "Update: "+str(c_ok)
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

  cmd_parser.add_option( "-s", "--start",
			action = "store", type = "string", dest = "start_date",
			help = "Start date", metavar="START_DATE" )

  cmd_parser.add_option( "-e", "--end",
			action = "store", type = "string", dest = "end_date",
			help = "End date", metavar="END_DATE" )

  cmd_parser.add_option( "-n", "--new",
			action = "store", type = "string", dest = "new_date",
			help = "New date", metavar="NEW_DATE" )

  (cmd_options, args) = cmd_parser.parse_args()
    
	
    # parse config file
  try:
	cfg_parser.read( cmd_options.config_file )
  except:
	print "mass_redate.py: Config file error"
	return 1	
	
  print "mass_redate.py: Reading config: " + str( cmd_options.config_file )    
  # parse config file end
	
  # get common options from config
  sqlhost  = str( cfg_parser.get( "Common", "sqlhost" ) )
  sqldb    = str( cfg_parser.get( "Common", "sqldb" ) )
  sqlusr   = str( cfg_parser.get( "Common", "sqluser" ) )
  sqlpass  = str( cfg_parser.get( "Common", "sqlpass" ) )
  host_tbl = str( cfg_parser.get( "Common", "host_table" ) )


  # connect
  try:
	con = pg.connect( dbname=sqldb, host=sqlhost, user=sqlusr, passwd=sqlpass )
  except:
	print "mass_redate.py: Connection FAILED"
	return 2

  now = datetime.datetime.now()


  print 'mass_redate.py: Redate Started at '+str(now)

  mass_redate( con, host_tbl, cmd_options.start_date, cmd_options.end_date, cmd_options.new_date )

  # close connection  
  con.close()
    
  now = datetime.datetime.now()  
  print 'mass_redate.py: Redate Finished at '+str(now)  
  
  return 0
# end def

# MAIN FUNCTION END

main()
