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


open (MYFILE, $ARGV[0]);
while (<MYFILE>) {
	chomp;
	#userid, courttypeid, ranking, hot, usertype, lastmodified
	@personal = split(/,/, $_);
	print "INSERT INTO `tblUserRankings` VALUES (@personal[0],@personal[1],@personal[2],@personal[3],@personal[4],@personal[5]);\n";
}
close (MYFILE);