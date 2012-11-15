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


$start = 6040;
$password = "c8974ec3b2326797366dfdbb35d0a61e";
$clubid = 46;
$roleid = 1;
$siteid = 88;


open (MYFILE, $ARGV[0]);
while (<MYFILE>) {
	chomp;
	#first, last, email, gender, rank, phone
	@personal = split(/,/, $_);
	
	$gender = @personal[3] eq 'M' ? 0 : 1;
	
	if( @personal[4] eq 'A'){
		$ranking = 5;
	} elsif(@personal[4] eq 'B') {
		$ranking = 4;
	} elsif(@personal[4] eq 'C'){
		$ranking = 3;
	} elsif(@personal[4] eq 'D'){
		$ranking = 2;
	}
	


	#users
	print "INSERT INTO `tblUsers` (`userid`, `username`, `firstname`, `lastname`, `email`, `password`, `gender`, `homephone`)  VALUES ($start,'@personal[2]','@personal[0]','@personal[1]','@personal[2]','$password', $gender, '@personal[5]');\n";
	
	#clubuser
	#print "INSERT INTO `tblClubUser` (`userid`, `clubid`, `roleid`, `recemail`)  VALUES ($start,'$clubid','$roleid','n');\n";
		
	#ranking
#	print "INSERT INTO `tblUserRankings` (`userid`, `courttypeid`, `ranking`)  VALUES ($start,2,'$ranking');\n";

	#auth
#	print "INSERT INTO `tblkupSiteAuth` (`userid`, `siteid`)  VALUES ($start,$siteid);\n\n";
	
	
	
	++$start;
}
close (MYFILE);