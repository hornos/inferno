#!/usr/bin/env python
import os, sys
import string
import time, datetime

import pg

# Package import
from optparse import OptionParser
import ConfigParser


def logout_users( con, utbl ):
  query  = 'SELECT * FROM '+utbl
  
  try:
	# con.query( 'SET DateStyle TO \'SQL\'' )
	rs = con.query( query )
	# print rs
  except:
	print "clean_users.py: Query users FAILED"
	return 3

  retrs = rs.dictresult()
  if retrs == None:
	print "clean_users.py: No results"
	return 3

  for r in retrs:
  	usrid = r['userid']
	lact  = r['lastactiontime']
	grace = r['gracetime']
	isonline = r['isonline']
	
	# print usrid, lact, grace
	
	tnow = datetime.datetime.now()
	tnowstr = str( tnow )[0:19]
	# print 'now '+str(tnow)
	# print "Epoch Seconds:", time.mktime( tnow.timetuple() )
	
	tact = datetime.datetime.strptime( lact[0:19], "%Y-%m-%d %H:%M:%S" )
	# print 'act '+str(tact)
	# print "Act Epoch Seconds:", time.mktime( tact.timetuple() )

	tdelta = tnow - tact	
	tgrace = datetime.timedelta(0,grace)

	# print 'delta '+str(tdelta)
	# print 'grace '+str(tgrace)
	
	if isonline == 't':
	  if tdelta > tgrace:
		print "%s%8.8s%s%s" % ( 'FORCE LOGOUT ', usrid, '    Grace: ', str( tdelta ) )
		query  = 'UPDATE '+utbl
		query += ' SET isonline = \'f\''
		query += ', logouttime = \''+str( tnowstr )+'\''
		query += ' WHERE userid = \''+usrid+'\''
		# print query
	  try:
		# con.query( 'SET DateStyle TO \'SQL\'' )
		rs = con.query( query )
		# print rs
	  except:
		print "clean_users.py: Update users FAILED"
		# return 3
	  else:
		print "%s%8.8s%s%s" % ( 'KEEP LOGIN   ', usrid, '    Grace: ', str( tdelta ) )	
	  # end if
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
	print "clean_users.py: Config file error"
	return 1	
	
  print "clean_users.py: Reading config: " + str( cmd_options.config_file )    
  # parse config file end
	
  # TODO: locking!

  # get common options from config
  sqlhost  = str( cfg_parser.get( "Common", "sqlhost" ) )
  sqldb    = str( cfg_parser.get( "Common", "sqldb" ) )
  sqlusr   = str( cfg_parser.get( "Common", "sqluser" ) )
  sqlpass  = str( cfg_parser.get( "Common", "sqlpass" ) )
  user_tbl = str( cfg_parser.get( "Common", "webapp_user_table" ) )


  # connect
  try:
	con = pg.connect( dbname=sqldb, host=sqlhost, user=sqlusr, passwd=sqlpass )
  except:
	print "clean_users.py: Connection FAILED"
	return 2

  now = datetime.datetime.now()
  print 'clean_users.py: User clean started at '+str(now)

  logout_users( con, user_tbl )
  
  # close connection  
  con.close()

  now = datetime.datetime.now()
  print 'clean_users.py: User clean finished at '+str(now)
  
  return 0
# end def

# MAIN FUNCTION END

main()
