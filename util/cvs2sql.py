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

# Set these variables for the club
index = 8143
clubid = 55
siteid = 98
ranking = 3
courttype = 7
courttype2 = 9
password = '8f7e08f6ea74964506a8cb2c6cf461eb' #(ladue2014)


#### You probably don't need to do anything below this line ##

filename = sys.argv[-1]
with open(filename, 'rb') as f:
    reader = csv.reader(f)
    for row in reader:

    	firstname = row[0]
    	lastname = row[1].replace("'", "\\'")
    	email = row[2]
    	gender = 1 if row[3] =='F' else '0'
    	username = firstname.lower()+'.'+lastname.lower()
    	phone = row[4]
    	memberid = row[5]

    	#users
    	sys.stdout.write(
    		"INSERT INTO `tblUsers` (`userid`, `username`, `firstname`, `lastname`, `email`, `password`, `gender`, `homephone`,`workphone`,`cellphone`, `pager`, `useraddress`)  VALUES ("+str(index)+",'"+username+"','"+firstname+"','"+lastname+"','"+email+"','"+password+"', '"+str(gender)+"', '"+phone+"','','', '','');\n"
    		)

        #clubuser
    	sys.stdout.write( 
    		"INSERT INTO `tblClubUser` (`userid`, `clubid`, `roleid`, `recemail`, `memberid`)  VALUES ("+str(index)+","+str(clubid)+",'1','n', '"+str(memberid)+"');\n"
    		)

    	# rankings 
    	sys.stdout.write( 
    		"INSERT INTO `tblUserRankings` (`userid`, `courttypeid`, `ranking`)  VALUES ("+str(index)+","+str(courttype)+",'"+str(ranking)+"');\n"
    		)
    	sys.stdout.write( 
			"INSERT INTO `tblUserRankings` (`userid`, `courttypeid`, `ranking`)  VALUES ("+str(index)+","+str(courttype2)+",'"+str(ranking)+"');\n"
		)

    	#auth
    	sys.stdout.write(
    		"INSERT INTO `tblkupSiteAuth` (`userid`, `siteid`)  VALUES ("+str(index)+","+str(siteid)+");\n\n"
    		)

        index+=1


