#!/usr/bin/python

#
# usage:  ./cvs2sql.py ~/Downloads/tblUserRankings.sql > output.sql
# #first, last, email, gender, phone
#
# to rollback
# delete from tblUsers where userid > 8143; delete from tblClubUser where userid > 8143;delete from tblUserRankings where usertype = 0 and userid > 8143;delete from tblkupSiteAuth where userid > 8143

import fileinput
import sys
import csv

#   Last    First   Home    Work    Cell    E-mail RANK  Gender

# Set these variables for the club
index = 8587
clubid = 57
siteid = 100
courttype = 9
password = '55fc5d4ae696bbd974f4401508d0aad2' 


#### You probably don't need to do anything below this line ##

filename = sys.argv[-1]
with open(filename, 'rb') as f:
    reader = csv.reader(f)
    for row in reader:

        ranking = row[0]
        lastname = row[1].replace("'", "\\'")
    	firstname = row[2]
    	homephone = ''
        workphone = row[4]
        cellphone = row[5]
    	email = row[6]

    	gender = 0 if row[7] =='F' else '1'
    	username = firstname.lower()+'.'+lastname.lower()
        memberid = ''


    	#users
    	sys.stdout.write(
    		"INSERT INTO `tblUsers` (`userid`, `username`, `firstname`, `lastname`, `email`, `password`, `gender`, `homephone`,`workphone`,`cellphone`, `pager`, `useraddress`)  VALUES ("+str(index)+",'"+username+"','"+firstname+"','"+lastname+"','"+email+"','"+password+"', '"+str(gender)+"', '"+homephone+"','"+workphone+"','"+cellphone+"', '','');\n"
    		)

        #clubuser
    	sys.stdout.write( 
    		"INSERT INTO `tblClubUser` (`userid`, `clubid`, `roleid`, `recemail`, `memberid`)  VALUES ("+str(index)+","+str(clubid)+",'1','n', '"+str(memberid)+"');\n"
    		)

    	# rankings 
    	sys.stdout.write( 
    		"INSERT INTO `tblUserRankings` (`userid`, `courttypeid`, `ranking`)  VALUES ("+str(index)+","+str(courttype)+",'"+str(ranking)+"');\n"
    		)

    	#auth
    	sys.stdout.write(
    		"INSERT INTO `tblkupSiteAuth` (`userid`, `siteid`)  VALUES ("+str(index)+","+str(siteid)+");\n\n"
    		)

        index+=1


