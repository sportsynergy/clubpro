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


$start = 6162;
$password = "33f2ee11f1130c5c6e11061cc91d9b9a";
$clubid = 49;
$roleid = 1;
$siteid = 91;


open (MYFILE, $ARGV[0]);
while (<MYFILE>) {
	chomp;
	#last, first, gender, rank
	 
	@personal = split(/,/, $_);
	$firstName = @personal[1];
	chomp($firstName);
	$lastName = @personal[0];
	$email = "";
	$ranking = @personal[3];
	$userName = $firstName.".".$lastName;
	
	
	$gender = @personal[2] eq 'M' ? 0 : 1;

	#users
#	print "INSERT INTO `tblUsers` (`userid`, `username`, `firstname`, `lastname`, `email`, `password`, `gender`, `homephone`)  VALUES ($start,'$userName','$firstName','$lastName','','$password', $gender, '');\n";
	
	#clubuser
	#print "INSERT INTO `tblClubUser` (`userid`, `clubid`, `roleid`, `recemail`)  VALUES ($start,'$clubid','$roleid','n');\n";
		
	#ranking
	print "INSERT INTO `tblUserRankings` (`userid`, `courttypeid`, `ranking`)  VALUES ($start,2,'$ranking');\n";

	#auth
	#print "INSERT INTO `tblkupSiteAuth` (`userid`, `siteid`)  VALUES ($start,$siteid);\n\n";
	
	
	
	++$start;
}
close (MYFILE);