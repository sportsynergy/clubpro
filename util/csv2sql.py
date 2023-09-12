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
index = 16276
clubid = 68
siteid = 111
courttype = 2
password = '2c77ea8f42f69a7c3dbada4417cf3cf5' 


#### You probably don't need to do anything below this line ##

filename = sys.argv[-1]
with open(filename, 'r') as f:
    reader = csv.reader(f)
    for row in reader:

        ranking = '3'
        lastname = row[7].replace("'", "\\'")
        firstname = row[6].replace("'", "\\'")
        homephone = ''
        workphone = ''
        cellphone = row[3]
        email = row[2]
        gender = 0 if row[4] =='Female' else '1'
        username = row[5].zfill(6) # pad with zeros
        memberid = row[5].zfill(6)


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


