#!/usr/bin/env python
# coding: UTF-8
import os, sys, re
import string, math
import time, datetime, socket

import pg

from threading import Thread

# Package import
from optparse import OptionParser
import ConfigParser

html_top = """
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <!-- Style -->
    <link rel="stylesheet" type="text/css" href="./css/common.css" >
    <link rel="stylesheet" type="text/css" href="./css/inferno.css" >

    <title>info</title>

</head>
<body>

<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
  <tr>
  <td width="100%" align="left">
		
	<table class="main" cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
	  <tr>
	  <td style="padding-top:20px; padding-left: 10px; padding-bottom:5px;">
	  	<img src="../files/mini_logo.gif" onclick='window.location="http://info"'>
		<!--
		<a href="http://info">Vissza (info)</a>
		-->
	  </td>  
	  </tr>
	
	  <tr>
	  <td valign="top" class="page">

  		<table cellspacing="5" cellpadding="5" border="0" height="100%">
		  <tr>
"""


html_bottom1 = """
		  </tr>
		</table>

	  </td>
	</tr>

	<tr>	  
	  <td align="left" valgin="top">
"""

html_bottom2 = """	  
	  </td>
	  
	</tr>
	
	<tr>
	  <td height="100%">
	  </td>
	</tr>
	</table>

  </td>
</tr>

<tr>
  <td>
  </td>
</tr>
</table>

    </body>
</html>
"""


class testit(Thread):
  def __init__ (self,ip):
	Thread.__init__(self)
	self.ip = ip
	self.status = -1
  # end def
  	
  def run(self):
	pingaling = os.popen("ping -q -c2 "+self.ip,"r")
	while 1:
	  line = pingaling.readline()
	  if not line: break
	  igot = re.findall(testit.lifeline,line)
	  if igot:
		self.status = int(igot[0])
	# end while
  # end def
# end class

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
	print "gen_pingtest.py: Config file error"
	return 1	
	
  print "gen_pingtest.py: Reading config: " + str( cmd_options.config_file )    
  sqlhost  = str( cfg_parser.get( "Common", "sqlhost" ) )
  sqldb    = str( cfg_parser.get( "Common", "sqldb" ) )
  sqlusr   = str( cfg_parser.get( "Common", "sqluser" ) )
  sqlpass  = str( cfg_parser.get( "Common", "sqlpass" ) )
  host_tbl = str( cfg_parser.get( "Common", "host_table" ) )
  user_tbl = str( cfg_parser.get( "Common", "user_table" ) )

  # parse config file end
	
  # TODO: locking!


  pingrange = [ [ "", 1, 255 ], [ "", 1, 253] ]
  
  # get common options from config
  htmldir    = str( cfg_parser.get( "Ping", "ping_html" ) )

  # connect
  try:
	con = pg.connect( dbname=sqldb, host=sqlhost, user=sqlusr, passwd=sqlpass )
  except:
	print "gen_pingtest.py: Connection FAILED"
	return 2


  print time.ctime()
  now = datetime.datetime.now()
  print 'gen_pingtest.py: started at '+str(now)


  htmlout = htmldir + "/pingtest.html"
  try:
	hfp = open( htmlout, "w" )
	hfp.write( html_top )
	today = datetime.date.today()
	hfp.write( '<div class="gendate">Generálva: '+str(now)+'</div>')
	
  except:
	print "ERROR: htmlout"
	con.close()
	return 1



  testit.lifeline = re.compile(r"(\d) received")
  report = ("No response","Partial Response","Alive")


  # machine counter
  mc = 0
  mcmod = 20
  
  # male counter
  male = 0
  # female counter
  female = 0
  
  # room
  room = { 'A': 0, 'B':0, 'C':0, 'O':0 }
  

  hfp.write( '<td valign="top">' )
    
  for pr in pingrange:
	net    = pr[0];
	ipfrom = pr[1];
	ipto   = pr[2];
	
	pinglist = []

	for host in range(ipfrom,ipto):
	  ip = net+"."+str(host)
	  current = testit(ip)
	  pinglist.append(current)
	  current.start()
	# end for
			
	for pingle in pinglist:
	  pingle.join()
	  try:
		(fqdn, alias, ipa) = socket.gethostbyaddr(pingle.ip)
		print "Status from ",pingle.ip,fqdn,"is",report[pingle.status], pingle.status

		if pingle.status == 2:
	
		  
		  query  = 'SELECT ip4,mtype,pid,sex,roomid FROM '+host_tbl
		  query += ' INNER JOIN '+user_tbl+' USING(pid)'
		  query += " WHERE ip4='"+pingle.ip+"'"
		  query += " AND mtype='pid'"
		  
		  try:
			rs = con.query( query )
			# print rs
		  except:
			print "Query failed: "+query

		  retrs = rs.dictresult()
		  if retrs == []:
			print "No query results"
		  else:
			# print retrs
			mc += 1

			rfqdn = fqdn.replace( "", "" )
		  	hfp.write( "%03d %s<br>" % (mc,rfqdn) )
			
			if mc % mcmod == 0:
			  hfp.write( '</td><td valign="top">' )
			# end if
			r = retrs[0]
			if r['sex'] == 'M':
			  male += 1
			else:
			  female += 1
			# end fi

			# roomid
			wing = r['roomid'][0]
			# print wing
			room[wing] += 1
			
		  # end if

		  
		# end if
	  except:
		print "No ",pingle.ip
	# end for				  
  # end for

  # print room
  hfp.write( "</td>" )
  hfp.write( html_bottom1 )

  gchart  = '<img src="http://chart.apis.google.com/chart?cht=p3&chf=bg,s,e6e6e6&chco=42429e,f951f9&chs=400x150'
  gchart += '&chd=t:'+str(male)+','+str(female)+'&chl=Fiúk ('+str(male)+')|Lányok ('+str(female)+')">'

  rchart  = '<img src="http://chart.apis.google.com/chart?cht=p3&chf=bg,s,e6e6e6&chco=ee0000,00ee00,0000ee&chs=400x150'
  rchart += '&chd=t:'+str(room['A'])+','+str(room['B'])+','+str(room['C'])+'&chl=A ('+str(room['A'])+')|B ('+str(room['B'])+')|C ('+str(room['C'])+')">'
  
  hfp.write( gchart )
  hfp.write( rchart )

  hfp.write( html_bottom2 )

  hfp.close()

  print time.ctime()  
  now = datetime.datetime.now()
  print 'gen_pingtest.py: finished at '+str(now)
  con.close()
  
  
  #print male
  #print female
  return 0
# end def

# MAIN FUNCTION END

main()
