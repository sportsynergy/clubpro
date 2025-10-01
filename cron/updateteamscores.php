<?php

include ("../application.php");
include ("../lib/ladderlib.php");

$service = new UpdateClubTeamScores();

if (isDebugEnabled(2)) logMessage("UpdateClubTeamScores: Starting...");

$service->resetteamscores();
$service->updateScores();

/**
 * 
 * This script updated
 */

class UpdateClubTeamScores{

    public function resetteamscores(){

        if (isDebugEnabled(2)) logMessage("UpdateClubTeamScores: Reset scores");

        $query = "UPDATE tblClubLadderTeam SET score = 0 WHERE enddate IS NULL";
        $result = db_query($query);

    }

	public function updateScores(){

        
        # 1.) Get all of the teams

        $query = "SELECT tCLT.id, tCLT.name, tCLT.score, tCLT.games, tCLT.lastUpdated FROM tblClubLadderTeam tCLT
                INNER JOIN tblClubSiteLadders tCSL ON tCLT.ladderid = tCSL.id
                WHERE tCLT.enddate IS NULL
                ORDER BY tCLT.score";
        $mresult = db_query($query);

        $teamcount = db_num_rows($mresult);
        if (isDebugEnabled(2)) logMessage("UpdateClubTeamScores: Found $teamcount teams");

        while($team_array = db_fetch_array($mresult) ){

            if (isDebugEnabled(2)) logMessage("\tUpdateClubTeamScores: Processing $team_array[name]");

            $team_score = 0;
            $team_games_won = 0;

            $member_query = "SELECT concat(tU.firstname, ' ', tU.lastname) AS teamplayername, tU.userid, tCLT.id, tBL.score, tBL.games, tBL.gameswon
                FROM tblClubLadderTeam tCLT
                INNER JOIN tblClubLadderTeamMember tCLTm ON tCLT.id = tCLTm.teamid
                INNER JOIN tblUsers tU ON tCLTm.userid = tU.userid
                INNER JOIN tblkpBoxLeagues tBL ON tU.userid = tBL.userid
                WHERe tCLT.id = $team_array[id]
                AND tCLTm.enddate IS NULL";
            $member_result = db_query($member_query);

            # 2.) Get each member
            while($team_member_array = db_fetch_array($member_result) ){

                # 3.) Get their box score
                if (isDebugEnabled(2)) logMessage("\tUpdateClubTeamScores: Adding $team_member_array[score] points and $team_member_array[gameswon] games won for $team_member_array[teamplayername] ");
                $team_score = $team_score + $team_member_array['score'];
                $team_games_won = $team_games_won + $team_member_array['gameswon'];
            }

        # update the team score
        $team_score_query = "UPDATE tblClubLadderTeam SET score = $team_score , lastUpdated = NOW(), games = $team_games_won
        WHERE id = $team_array[id]";
        $team_score_result = db_query($team_score_query);
        if (isDebugEnabled(2)) logMessage("\tUpdateClubTeamScores: Updated $team_array[name] to have a score of $team_score and total games won of $team_games_won");


    }

}

}



?>