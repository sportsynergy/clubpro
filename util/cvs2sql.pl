#!/usr/bin/perl
open (MYFILE, 'tblUserRankings.csv');
while (<MYFILE>) {
	chomp;
	#userid, courttypeid, ranking, hot, usertype, lastmodified
	@personal = split(/,/, $_);
	print "INSERT INTO `tblUserRankings` VALUES (@personal[0],@personal[1],@personal[2],@personal[3],@personal[4],@personal[5]);\n";
}
close (MYFILE);