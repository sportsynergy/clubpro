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
index = 11364
clubid = 64
siteid = 107
courttype = 2
password = '57e6a100b25ef0eb8159b064ed5ba7a5' 


#### You probably don't need to do anything below this line ##

filename = sys.argv[-1]
with open(filename, 'r') as f:
    reader = csv.reader(f)
    for row in reader:

        ranking = '3'
        lastname = row[2].replace("'", "\\'")
        firstname = row[1].replace("'", "\\'")
        homephone = ''
        workphone = ''
        cellphone = ''
        email = row[3]
        gender = 0 if row[4] =='F' else '1'
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


