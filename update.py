#!/usr/bin/python
# -*- coding: utf-8 -*- 
import feedparser
import codecs
import MySQLdb
import time
import datetime
import socket
    
def addslashes(s):
    l = ["\\", '"', "'", "\0", ]
    for i in l:
        if i in s:
            s = s.replace(i, '\\'+i)
    return s

try:
	db = MySQLdb.connect(host="localhost", user="root", passwd="123456", db="rss", charset='utf8' )
except:
	print "Could not connect to MySQL server."
	exit( 0 )
cur = db.cursor()
socket.setdefaulttimeout(15)
count = 0
while 1:
	cmd = "SELECT Link FROM sources"
	cur.execute(cmd)
	sources=[]
	for item in cur.fetchall():
		sources.append(item[0]) 
	for source in sources:
		cmd = "SELECT ID FROM sources WHERE Link = '%s'" % (source,)
		cur.execute(cmd)
		ID = cur.fetchone()[0]

		d = feedparser.parse(source)
		entries_added = 0
		add_unread_arr = [];
		for entry in reversed(d.entries):
			cmd = "SELECT * FROM entries WHERE Link = '%s'" % (entry.link,)
			cur.execute(cmd)
			if(cur.fetchall()):
				pass
			else:
				if(hasattr(entry,'content')):
					content = entry.content[0]['value']
				else:
					content = entry.description
				if(hasattr(entry,'published_parsed')):
					dt = datetime.datetime.fromtimestamp(time.mktime(entry.published_parsed))
				else:
					dt=datetime.datetime.utcnow()
				a = "INSERT INTO entries(Source,Title,Link,Summary,Content,Date) values( '%s', '%s', '%s', '%s', '%s', '%s')" % (ID, addslashes(entry.title), addslashes(entry.link), addslashes(entry.summary), addslashes(content), dt.strftime('%Y-%m-%d %H:%M:%S') ) 
				cur.execute(a)
				db.commit()
				getID = "SELECT ID from entries WHERE Link='%s'" % addslashes(entry.link)
				cur.execute(getID)
				add_unread_arr.append(str(cur.fetchone()[0]))
				entries_added += 1

		fetchunread = "SELECT * from userdata WHERE Source='%s'" % ID
		cur.execute(fetchunread)
		for item in cur.fetchall():
			userid = item[0];
			unread = item[2];
			if cmp(unread,'')==0:
				unread_arr = [];
			else:
				unread_arr = unread.split(",")
			unread_arr = unread_arr + add_unread_arr
			if len(unread_arr)>1:
				unread = ','.join(unread_arr)
			if len(unread_arr)==1:
				unread = str(unread_arr[0])
			if len(unread_arr)==0:
				unread = '';
			update_unread = "UPDATE userdata set Unread='%s' where User='%s' and Source='%s'" % (unread, userid, ID)
			cur.execute(update_unread)
			db.commit()

		if entries_added > 0:
			print str(entries_added) + ' Entries Added ' + 'for Source:' + str(ID)
	count +=1
	print 'Update Succeeded. Sleep 300 seconds. Count:' +str(count)
	time.sleep(300)