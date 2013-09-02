#!/usr/bin/python
# -*- coding: utf-8 -*- 
import feedparser
import codecs
import MySQLdb
import time
import datetime
import sys
import urllib2
from BeautifulSoup import BeautifulSoup 

def addslashes(s):
    l = ["\\", '"', "'", "\0", ]
    for i in l:
        if i in s:
            s = s.replace(i, '\\'+i)
    return s

rss_source = sys.argv[1]
d = feedparser.parse( rss_source )
try:
   db = MySQLdb.connect(host="localhost", user="root", passwd="123456", db="rss", charset='utf8' )
except:
   print "Could not connect to MySQL server."
   exit( 0 )
cur = db.cursor()

#Find Favicon
page = urllib2.urlopen(d.feed.link).read()
soup = BeautifulSoup(page)
icon_link = soup.find("link", rel="shortcut icon")
if(icon_link==None):
	icon_link = d.feed.link + '/favicon.ico'
else:
	icon_link = icon_link['href']

cmd = "INSERT INTO sources(Name,Link,SiteLink,Favicon) values('%s', '%s', '%s', '%s')" % (d.feed.title, rss_source, d.feed.link, icon_link)
cur.execute(cmd)
#db.commit()
cmd = "SELECT ID FROM sources WHERE Link = '%s'" % (rss_source,)
cur.execute(cmd)
ID = cur.fetchone()[0]
for entry in reversed(d.entries):
	if(hasattr(entry,'content')):
		content = entry.content[0]['value']
	else:
		content = entry.description
	dt = datetime.datetime.fromtimestamp(time.mktime(entry.published_parsed))
	cmd = "INSERT INTO entries(Source,Title,Link,Summary,Content,Date) values( '%s', '%s', '%s', '%s', '%s', '%s')" % (ID, addslashes(entry.title), addslashes(entry.link), addslashes(entry.summary), addslashes(content), dt.strftime('%Y-%m-%d %H:%M:%S') ) 
	try:
		cur.execute(cmd)
	except Exception as z:
		f = codecs.open('log.txt','a','utf-8')
		f.write(cmd)
		f.close()
db.commit()