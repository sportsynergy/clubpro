#!/usr/bin/perl
################################
##
##  The script does two things.  The first thing it does is it adjusts players rankings downward if they haven't recorded 
##  a score within the last month.  Just how much their ranking  will be affected is stored in the tblClubSites
##  table as rankingadjustment as a percentage.  Upon adjusting their ranking, an email is sent out to the user
## 
##  The second part of this script sends out a notification to players who haven't recrod in the past 21 days.
##
##
## Sportsynery Clubpro
##
#################################


use DBI;

##################################################
# Configure

# Rankings will not fall below this

$rankingfloor = 2.5;


####################################################


# Connect
$user = "sportsynergy";
$password = "clubpro0279";
$database = "clubpro_main";
$hostname = "localhost";
$port = 3306;




# Get the current timestamp
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime(time);

# This will format the time as 2008-11-04 10:10:02.22.  The reason that this is a month
# ago is because the localtime call above will set the month in a 0-11. 
$amonthago = sprintf "%4d-%02d-%02d %02d:%02d:%02d\n",$year+1900,$mon,$mday,$hour,$min,$sec;
$rightnow = sprintf "%4d-%02d-%02d %02d:%02d:%02d\n",$year+1900,$mon+1,$mday,$hour,$min,$sec;
chomp($rightnow);

$dsn = "DBI:mysql:database=$database;host=$hostname;port=$port";

$dbh = DBI->connect($dsn, $user, $password);

# Get all sites with ranking adjustments set

 
$sth = $dbh->prepare("SELECT sites.rankingadjustment, sites.siteid, clubs.clubname, sites.sitename FROM tblClubSites sites, tblClubs clubs WHERE sites.rankingadjustment > 0 AND sites.clubid = clubs.clubid");

$sth->execute;

if( $sth->rows eq 0 ){

	print "$rightnow - No sites are configured to use the ranking adjustment \n";
}

while ( @site = $sth->fetchrow_array ) {
 
	$rankingadjustment = @site[0];
	$siteid = @site[1];
	$clubname = @site[2];
	$sitename = @site[3];

  # Now get the players at the site with the ranking adjustment

  $query = "SELECT users.userid, rankings.ranking, rankings.courttypeid, rankings.usertype, site.sitename, users.firstname, users.lastname, users.email, courttype.courttypename
			 FROM  tblUsers users, tblUserRankings rankings, tblkupSiteAuth siteauth, tblClubSites site, tblCourtType courttype
			WHERE users.userid = rankings.userid
			AND courttype.courttypeid = rankings.courttypeid
			AND users.userid = siteauth.userid
			AND site.siteid = siteauth.siteid
			AND siteauth.siteid = $siteid
			AND rankings.usertype = 0
			AND  rankings.lastModified < '$amonthago'";

	$sth1 = $dbh->prepare( $query );
	$sth1->execute;

	
	if( $sth1->rows eq 0 ){

		print "$rightnow - No rankings are up for adjustment at this time. \n";
	}
	else{ 
		print "$rightnow - Adjusting the following rankings in $clubname: $sitename   \n";
	}

	$emailssent = 0;
	while ( @user = $sth1->fetchrow_array ) {
		
		$userid = @user[0];
		$oldranking = @user[1];
		$courttypeid = @user[2];
		$usertypeid = @user[3];
		$sitename = @user[4];
		$firstname = @user[5];
		$lastname = @user[6];
		$email = @user[7];
		$courttypename = @user[8];


		# update ranking
		$newranking = $oldranking * (1 - $rankingadjustment/100);
		
		# only go down as far as the floor
		if($newranking < $rankingfloor){
			$newranking = $newranking;
		}


		$adjustmentquery =  "UPDATE tblUserRankings SET ranking = $newranking WHERE userid = $userid and courttypeid = $courttypeid and usertype = $usertypeid \n";
		#print "$oldranking is now $newranking for user: $firstname $lastname and courtytype: $courttypename\n";
		
		#If a ranking is below 2 forget it.
		if($oldranking >= $rankingfloor){
			$sth2 = $dbh->prepare( $adjustmentquery );
			$sth2->execute;	
			
			#Send the email
			notifyPeopleOfRankingAdjustment($firstname, $email, $oldranking, $newranking, $clubname, $rankingadjustment, $courttypename);

			print "$firstname $lastname for $courttypename\n";

			#Increment the counter
			++$emailssent;
		}
			
			
	}
  
  





  #
  # Now send out an email to warn people that they are within two weeks of having their rankings adjusted.
  #
  

   #Define the window.  Any ranking that was updated 21 days ago, send an email.
  ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime(time-(60*60*24*22));
   $windowStart =sprintf "%4d-%02d-%02d %02d:%02d:%02d\n",$year+1900,$mon+1,$mday,$hour,$min,$sec;
   
   ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime(time-(60*60*24*21));
   $windowEnd =sprintf "%4d-%02d-%02d %02d:%02d:%02d\n",$year+1900,$mon+1,$mday,$hour,$min,$sec;

   #print "This is my window start: $windowStart and my window end: $windowEnd\n";


  $query = "SELECT users.userid, rankings.ranking, rankings.courttypeid, rankings.usertype, site.sitename, users.firstname, users.lastname, users.email, courttype.courttypename, rankings.lastmodified
			 FROM  tblUsers users, tblUserRankings rankings, tblkupSiteAuth siteauth, tblClubSites site, tblCourtType courttype
			WHERE users.userid = rankings.userid
			AND courttype.courttypeid = rankings.courttypeid
			AND users.userid = siteauth.userid
			AND site.siteid = siteauth.siteid
			AND siteauth.siteid = $siteid
			AND rankings.usertype = 0
			AND  rankings.lastModified > '$windowStart'
			AND rankings.lastModified < '$windowEnd'";	


	$sth1 = $dbh->prepare( $query );
	$sth1->execute;


	if( $sth1->rows eq 0 ){
		print "$rightnow - No rankings are a week away from being adjusted at this time. \n";
	}
	else{
		print "$rightnow - Notified the following users in $clubname: $sitename:  \n";
	}

	$emailssent = 0;
	while ( @user = $sth1->fetchrow_array ) {

		$userid = @user[0];
		$oldranking = @user[1];
		$courttypeid = @user[2];
		$usertypeid = @user[3];
		$sitename = @user[4];
		$firstname = @user[5];
		$lastname = @user[6];
		$email = @user[7];
		$courttypename = @user[8];
		
		#Send the email
		warnPeopleOfRankingAdjustment($firstname, $email, $oldranking, $clubname, $rankingadjustment, $courttypename);
		
		print "$firstname $lastname\n";	

		#Increment the counter
		++$emailssent;


	}
	
 
  
}




# Disconnect
$dbh->disconnect();







#
#   Sends out an email to let the player know that their ranking was adjusted
#
sub notifyPeopleOfRankingAdjustment{


$firstname = $_[0];
$email = $_[1];
$oldranking = $_[2];
$newranking = $_[3];
$clubname = $_[4];
$adjustment = $_[5];
$courttypename = $_[6];

#print "First Name: $firstname\nEmail: $email\nOld Ranking: $oldranking\nNew Ranking: $newranking\nClub Name: $clubname\nAdjustment: $adjustment\nCourt Type Name: $courttypename\n";

my $sendmail = "/usr/sbin/sendmail -t"; 
my $reply_to = "Reply-to: rankings\@sportsynergy.net\n"; 
my $from = "From: rankings\@sportsynergy.net\n"; 
my $subject = "Subject: $clubname -- Ranking Adjustment\n";

my $content = "Hi $firstname,\n\n"
."Listen, we know it and you know it.  You haven't been playing much $courttypename lately.  Its not a big deal, but we are going to decrease your ranking a little bit.  "
."We are doing this because when you actually do start playing again your probably going to be worse.  Plus, your club has a policy that will decrease"
."your ranking by $adjustment% if you haven't recorded a score within 30 days.  So.... Anyway, your ranking fell from $oldranking to $newranking.\n\n"
."Sincerely,\n\nThe Sportsynergy Ranking Oversight Committee"; 

my $send_to = "To: $email\n"; 


open(SENDMAIL, "|$sendmail") or die "Cannot open $sendmail: $!"; 
print SENDMAIL $reply_to; 
print SENDMAIL $from; 
print SENDMAIL $subject; 
print SENDMAIL $send_to; 
print SENDMAIL "Content-type: text/plain\n\n"; 
print SENDMAIL $content; 
close(SENDMAIL); 

}


#
#   Sends out an email to let the player know that their ranking will be adjusted
#
sub warnPeopleOfRankingAdjustment{

	$firstname = $_[0];
	$email = $_[1];
	$oldranking = $_[2];
	$clubname = $_[3];
	$adjustment = $_[4];
	$courttypename = $_[5];

	#print "First Name: $firstname\nEmail: $email\nOld Ranking: $oldranking\nClub Name: $clubname\nAdjustment: $adjustment\nCourt Type Name: $courttypename\n";

	my $sendmail = "/usr/sbin/sendmail -t"; 
	my $reply_to = "Reply-to: rankings\@sportsynergy.net\n"; 
	my $from = "From: rankings\@sportsynergy.net\n"; 
	my $subject = "Subject: $clubname -- Ranking Adjustment\n";

	my $content = "Hi $firstname,\n\nIt looks like you are haven't recorded a $courttypename score in a while.  "
		."If you don't record a score within the next week your ranking will be decreased by $adjustment%.  We're just saying...\n\n"
	
	."Sincerely,\n\nThe Sportsynergy Ranking Oversight Committee"; 

	my $send_to = "To: $email\n"; 


	open(SENDMAIL, "|$sendmail") or die "Cannot open $sendmail: $!"; 
	print SENDMAIL $reply_to; 
	print SENDMAIL $from; 
	print SENDMAIL $subject; 
	print SENDMAIL $send_to; 
	print SENDMAIL "Content-type: text/plain\n\n"; 
	print SENDMAIL $content; 
	close(SENDMAIL); 



}
