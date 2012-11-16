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



def gen_papercut( con, usrtbl, widtbl, output ):
  # start query	
  query  = 'SELECT (name).forname, (name).surname,'
  query += ' wid, pid, isvalid, idents, faculty, class, roomid FROM '+usrtbl
  query += ' INNER JOIN '+widtbl+' USING(pid)'
  query += ' WHERE ptype=\'student\''
  query += ' ORDER BY (name).forname, (name).surname'

  try:
	rs = con.query( query )
	# print rs
  except:
	print "gen_papercut_users.py: Query users: Query FAILED"
	return 3

  retrs = rs.dictresult()
  if retrs == None:
	return 3

  c_invalid = 0
  c_ok = 0

  userlist = ''
  isfirst = 1
  
  output.write('[User]\n')
  
  for r in retrs:
  # TODO: valid dhcp expires test

  	field = r['isvalid']
	valid = field

	if valid != 't':
	  continue

  	field = r['faculty']
	faculty = field

  	field = r['wid']
	wid = field

  	field = r['idents']
	idents = field

	isprinter = 0
	for i in idents.split( '|' ):
	  iarr = i.split(':')
	  if iarr[0] == "printer":
		password = iarr[1]
		isprinter = 1
		break
	  # end if
	# end for
	
	if not isprinter:
	  continue
	
  	field = r['class']
	fclass = field
	
  	field = r['roomid']
	roomid = field
	
	field = r['forname']
	name  = field
	field = r['surname']
	name += ' ' + field	

	
	c_ok += 1

	desc = faculty + " " + fclass + " ("+roomid+")"
	name = conv2eng2( name )
  
	ustr = wid + ',' + name + ',' + password + ',' + desc + ',,,,\n'
	output.write( ustr )
	
	if not isfirst:
	  userlist += ','
	userlist += wid
	isfirst = 0
	
  # end for

  output.write( '\n' )
  output.write( '[Local]\n' )
  output.write( 'Guests,,'+userlist+'\n' )
  output.write( 'W,,'+userlist+'\n' )
  output.write( '\n' )
  
  
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
	print "gen_papercut_users.py: Config file error"
	return 1	
	
  print "gen_papercut_users.py: Reading config: " + str( cmd_options.config_file )    
  # parse config file end
	
  # get common options from config
  sqlhost  = str( cfg_parser.get( "Common", "sqlhost" ) )
  sqldb    = str( cfg_parser.get( "Common", "sqldb" ) )
  sqlusr   = str( cfg_parser.get( "Common", "sqluser" ) )
  sqlpass  = str( cfg_parser.get( "Common", "sqlpass" ) )
  user_tbl = str( cfg_parser.get( "Common", "user_table" ) )
  wid_tbl = str( cfg_parser.get( "Common", "wid_table" ) )

  # get hdcp options from config
  papercut_users = str( cfg_parser.get( "PRINT", "papercut_users" ) )

  # connect
  try:
	con = pg.connect( dbname=sqldb, host=sqlhost, user=sqlusr, passwd=sqlpass )
  except:
	print "gen_papercut_users.py: Connection FAILED"
	return 2

  now = datetime.datetime.now()
  today = datetime.date.today()
  output = papercut_users+"-"+str(today)+".txt"
  print output

  try:
	fp = open( output, "w+" )
  except:
	print 'gen_papercut_users.py: Output FAILED'
	return 3


  print 'gen_papercut_users.py: Papercut Config Generation Started at '+str(now)

  gen_papercut( con, user_tbl, wid_tbl, fp )

  # close connection  
  con.close()
  fp.close()
    
  now = datetime.datetime.now()  
  print 'gen_papercut_users.py: Papercut Config Generation Finished at '+str(now)  
  
  os.system( 'unix2dos '+output )
  
  return 0
# end def

# MAIN FUNCTION END

main()
