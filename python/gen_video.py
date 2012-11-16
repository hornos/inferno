#!/usr/bin/env python
# coding: UTF-8
import os, sys, re
import string, math
import time, datetime

# Package import
from optparse import OptionParser
import ConfigParser

extensions = [ ".avi", ".mpg", ".mpeg", ".vob", ".mp4", ".qt" ]

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

  		<table cellspacing="0" cellpadding="0" border="0" height="100%">
		  <tr>
		  <td valign="top" height='100%' width="100%">
"""


html_bottom = """
		  </td>
		  </tr>
		</table>

	  </td>
	</tr>
	</table>

  </td>
</tr>
</table>

    </body>
</html>
"""


def flcmp( a, b ):
  if a[2] > b[2]:
	return 1
  if a[2] == b[2]:
	return 0
  if a[2] < b[2]:
	return -1
# end def

def gen_videodir( vd, dt, h ):
  now = time.time()
  for root, dirs, files in os.walk( vd ):
	if files != []:
	  filelist = []
	  for f in files:
		fpath = root+'/'+f
		fstat = os.stat( fpath )
		
		dtctime = now - fstat.st_ctime
		dtdt = dt * 24 * 3600
		if dtctime <= dtdt:
		  for ext in extensions:
			if re.search( ext, f ):
			  filelist.append( [fpath, f, dtctime ] )
			  # print fpath + ' : ' + str( now - fstat.st_ctime )
			  break;
		  # end for
 	  # end for
	  if filelist != []:
		filelist.sort( flcmp )
		
		newroot = root.replace( "/mnt/multiplex/", "" );
		
		header = '<div class="dir">'+newroot+"</div>"
		h.write( header + "\n" )
		h.write( "<table>\n" )
		
		for fli in filelist:
		  h.write( "<tr>\n" )
		
		  days = "%d" % math.floor( fli[2] / (24*3600) )
	  	  # h.write( fli[1] + " " + days + "<br>\n" )
		  h.write( '<td class="days">' + days + " napja</td>" )
	  	  h.write( '<td class="title"> ' + fli[1] + "</td>\n" )
		  
		  h.write( "</tr>\n" )		  
		# end for
		
		h.write( "</table>\n" )		
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
	print "gen_media.py: Config file error"
	return 1	
	
  print "gen_media.py: Reading config: " + str( cmd_options.config_file )    
  # parse config file end
	
  # TODO: locking!

  # get common options from config
  videohost  = str( cfg_parser.get( "Media", "video_host" ) )
  videodirs  = str( cfg_parser.get( "Media", "video_dirs" ) )
  videodt    = string.atoi( cfg_parser.get( "Media", "video_dt" ) ) 
  htmldir    = str( cfg_parser.get( "Media", "video_html" ) )


  now = datetime.datetime.now()
  print 'gen_media.py: started at '+str(now)

  try:
	fp = open( videodirs, "r" )
  except:
	print "ERROR: videodirs"
	return 1  

  htmlout = htmldir + "/" + videohost + "." + str( videodt ) + ".html"
  try:
	hfp = open( htmlout, "w" )
	hfp.write( html_top )
	today = datetime.date.today()
	hfp.write( '<div class="gendate">Generálva: '+str(today)+'</div>')
	
  except:
	print "ERROR: htmlout"
	return 1

  for line in fp:
	vdir = line.strip()
	if os.path.isdir( vdir ):
	  
	  gen_videodir( vdir, videodt, hfp )
	  print "Generating: "+vdir
	else:
	  print "Skip: "+vdir
	# end if
  # end for

  hfp.write( html_bottom )
  hfp.close()
  fp.close()
  
  now = datetime.datetime.now()
  print 'gen_media.py: finished at '+str(now)
  
  return 0
# end def

# MAIN FUNCTION END

main()
