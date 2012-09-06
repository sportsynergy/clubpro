#!/usr/bin/perl

# Example sql to export as CSV
#
#
# SELECT rankings . * 
#FROM tblUserRankings rankings, tblClubUser clubuser
#WHERE rankings.userid = clubuser.userid
#AND rankings.courttypeid =3
#AND rankings.usertype =0
#AND clubuser.clubid =17
#
# usage: perl cvs2sql.pl ~/Downloads/tblUserRankings.sql > output.sql


$start = 5425;
$password = "8a48bca7b89dc6450afd4dcee81a35b4";
$clubid = 47;
$roleid = 1;
$siteid = 89;
$ranking = 3;

open (MYFILE, $ARGV[0]);
while (<MYFILE>) {
	chomp;
	#first, last, email, gender
	@personal = split(/,/, $_);
	
	$gender = @personal[3] eq 'M' ? 0 : 1;

	#users
	print "INSERT INTO `tblUsers` (`userid`, `username`, `firstname`, `lastname`, `email`, `password`, `gender`)  VALUES ($start,'@personal[2]','@personal[1]','@personal[0]','@personal[2]','$password', $gender);\n";
	
	#clubuser
	#print "INSERT INTO `tblClubUser` (`userid`, `clubid`, `roleid`, `recemail`)  VALUES ($start,'$clubid','$roleid','n');\n";
		
	#ranking
	#print "INSERT INTO `tblUserRankings` (`userid`, `courttypeid`, `ranking`)  VALUES ($start,2,'$ranking');\n";
	#print "INSERT INTO `tblUserRankings` (`userid`, `courttypeid`, `ranking`)  VALUES ($start,3,'$ranking');\n";
	
	#auth
#	print "INSERT INTO `tblkupSiteAuth` (`userid`, `siteid`)  VALUES ($start,$siteid);\n\n";
	
	
	
	++$start;
}
close (MYFILE);