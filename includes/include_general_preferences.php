
<form name="general_preferences_form" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">

   <div class="mb-3">
        <label for="displayrecentactivity" class="form-label">Display Recent Activity</label>
          <select name="displayrecentactivity" id="displayrecentactivity" class="form-select" aria-label="Display Recent Activity">
              <option value="y"
              <? if($generalPreferences["displayrecentactivity"] =='y'){ echo "selected";} ?>>Yes</option>
                <option value="n"
              <? if($generalPreferences["displayrecentactivity"] =='n'){ echo "selected";} ?>>No</option>
            </select>
             <div id="recentactivityHelp" class="form-text">The Recent Activity panel will display the results of the matches that were recently recorded.</div>
      </div>

      <div class="mb-3">
        <label for="allowselfcancel" class="form-label">Allow Players To Cancel Their Own Reservations</label>
          <select name="allowselfcancel" id="allowselfcancel" class="form-select" aria-label="Allow Players To Cancel Their Own Reservations">
              <option value="y"
              <? if($generalPreferences["displayrecentactivity"] =='y'){ echo "selected";} ?>>Yes</option>
                <option value="n"
              <? if($generalPreferences["displayrecentactivity"] =='n'){ echo "selected";} ?>>No</option>
            </select>
             <div id="recentactivityHelp" class="form-text">Pretty much just as what is says. When Allow Player Cancelation is set to 'Yes', players will be allowed to cancel their own reservations. When set to no, all cancelations will need to be made by an Administrator. When set to 'Before 2 hours', players can cancel uptil 2 hours before the reservation.</div>
      </div>

      <div class="mb-3">
        <label for="daysahead" class="form-label">How Far In Advanced Players Can Make a Reservation</label>
          <select name="daysahead" id="daysahead" class="form-select" aria-label="How Far In Advanced Players Can Make a Reservation">
                <? for($i=1; $i<30; ++$i){ ?>
                <?
            //Default to the current daysahead setting.
            if($generalPreferences["daysahead"] == $i){
              $selected = "selected";
            }
            ?>
                <option value="<?=$i?>" <?=$selected?>>
                <?=$i?>
                </option>
                <? unset($selected)?>
                <? } ?>
              </select>
             <div id="displayinadvancedyHelp" class="form-text">Basically this is the number of days in advance a player can view the court schedule in the drop down on the main scheduling page.</div>
      </div>


      <div class="mb-3">
        <label for="allowselfscore" class="form-label">Allow Players To Score Their Own Reservations</label>
          <select name="allowselfscore" id="allowselfscore" class="form-select" aria-label="Allow Players To Score Their Own Reservations">
                <option value="y"
              <? if($generalPreferences["allowselfscore"] =='y'){ echo "selected";} ?>>Yes</option>
                <option value="n"
              <? if($generalPreferences["allowselfscore"] =='n'){ echo "selected";} ?>>No</option>
              </select>
             <div id="displayinadvancedyHelp" class="form-text">Sometimes you may want to
              crack down on how scores are being recorded. This little setting is
              pretty much the best way to do just that. When this is set to 'No'
              players will not be able to record the score of any match they play
              in. Administrations, like yourself, will be able to record scores of
              anyone no matter what.</div>
      </div>

   <div class="mb-3">
    <label for="workphone" class="form-label">Facebook URL</label>
   <input type="text" name="facebookurl" size="50" value="<?=$generalPreferences["facebookurl"]?>" class="form-control"/>
   <div id="displayinadvancedyHelp" class="form-text"> If you have a facebook page for your club, you can put that link here and this will display in the 
              top left corner of every page.</div>
  </div>
           
    <? if(isLadderRankingScheme() ){ ?>

     <div class="mb-3">
        <label for="challengerange" class="form-label">Number of spots ahead people can challenge in the ladder</label>
          <select name="challengerange" id="challengerange" class="form-select" aria-label="Number of spots ahead people can challenge in the ladder">
                <? for($i=1; $i<13; ++$i){ ?>
                <?
            //Default to the current daysahead setting.
            if($generalPreferences["challengerange"] == $i){
              $selected = "selected";
            }
            ?>
                <option value="<?=$i?>" <?=$selected?>>
                <?=$i?>
                </option>
                <? unset($selected)?>
                <? } 
               
               if($generalPreferences["challengerange"] == 500){
                  $selected = "selected";
                }
                
                ?>
                <option value="500"  <?=$selected?> >No Limit</option>
              </select>
             <div id="challengespotsyHelp" class="form-text">The Challenge Range is how many spots ahead a person can challenge someone else in the ladder.</div>
      </div>

      <? } ?>


      <div class="mb-3">
        <label for="reminders" class="form-label">Email Reminders</label>
          <select name="reminders" id="reminders" class="form-select" aria-label="Email Reminders">
                    <option value="none">none</option>
                    <option value="24" <?=$generalPreferences["reminders"]=='24' ? "selected":""?> >24 Hours Ahead</option>
                    <option value="5" <?=$generalPreferences["reminders"]=='5' ? "selected":""?> >5:00 am</option>
                    <option value="6" <?=$generalPreferences["reminders"]=='6' ? "selected":""?> >6:00 am</option>
                    <option value="7" <?=$generalPreferences["reminders"]=='7' ? "selected":""?> >7:00 am</option>
                    <option value="8" <?=$generalPreferences["reminders"]=='8' ? "selected":""?> >8:00 am</option>
                    <option value="9" <?=$generalPreferences["reminders"]=='9' ? "selected":""?> >9:00 am</option>
                    <option value="10" <?=$generalPreferences["reminders"]=='10' ? "selected":""?> >10:00 am</option>
                  </select>
             <div id="emailreminderyHelp" class="form-text">This option is for when the email reminders for any upcoming matches will be sent out to the players. Set it to none to disable this option.</div>
      </div>

      <div class="mb-3">
        <label for="showplayernames" class="form-label">Display Player Names</label>
          <select name="showplayernames" id="showplayernames" class="form-select" aria-label="Display Player Names">
                  <option value="y"
                  <? 

                  if (isDebugEnabled(1)) logMessage(   $generalPreferences["showplayernames"] );
    
                  if($generalPreferences["showplayernames"] =='y'){ echo "selected";} ?>>Yes</option>
                    <option value="n"
                  <? if($generalPreferences["showplayernames"] =='n'){ echo "selected";} ?>>No</option>
              </select>
             <div id="displayplayername" class="form-text">By default, the player's full name is displayed with their reservation on the main reservation page even before logging in. To require the user to login in order to see the players name, set this option to no.</div>
      </div>


       <div class="mb-3">
        <label for="requirelogin" class="form-label">Require Login</label>
          <select name="requirelogin" id="requirelogin" class="form-select" aria-label="Require Login">
                  <option value="y"
                  <? 
                  if($generalPreferences["requirelogin"] =='y'){ echo "selected";} ?>>Yes</option>
                    <option value="n"
                  <? if($generalPreferences["requirelogin"] =='n'){ echo "selected";} ?>>No</option>
              </select>
             <div id="requireloginhelp" class="form-text">Require all users to login before accessing the booking page.</div>
      </div>


      <div class="mb-3">
        <label for="allowmatchlooking" class="form-label">Allow Players to look for singles matches</label>
         <select name="allowmatchlooking" id="allowmatchlooking" class="form-select" aria-label="Allow Players to look for singles matches">
                  <option value="y"
                  <? 

                  if($generalPreferences["allowplayerslooking "] =='y'){ echo "selected";} ?>>Yes</option>
                    <option value="n"
                  <? if($generalPreferences["allowplayerslooking "] =='n'){ echo "selected";} ?>>No</option>
              </select>
             <div id="allowmatchlookinghelp" class="form-text">Allows players to not specify an opppoent and have the system find a suitable match.</div>
      </div>

      <div class="mb-3">
        <label for="autocancelincompletes" class="form-label">Automatically Cancel Incomplete Reservations</label>
         <select name="autocancelincompletes" id="autocancelincompletes" class="form-select" aria-label="Automatically Cancel Incomplete Reservations">
                    <option value="none" <?=$generalPreferences["autocancelincompletes"]=='none'  ? "selected":""?> >none</option>
                    <option value="1" <?=$generalPreferences["autocancelincompletes"]=='1' ? "selected":""?> >1 hour ahead</option>
                    <option value="4" <?=$generalPreferences["autocancelincompletes"]=='4' ? "selected":""?> >4 hours ahead</option>
                    <option value="12" <?=$generalPreferences["autocancelincompletes"]=='12' ? "selected":""?> >12 hours ahead</option>
                  </select>
             <div id="autocancelincompleteshelp" class="form-text">Automatically cancel reservations that don't have all of the players set.  For example, setting this to 12 hours ahead will cancel all reservations that still need players 12 hours ahead of time.</div>
      </div>

    <div class="mt-5">
      <button type="submit" class="btn btn-primary" id="general-submitbutton" onclick="onSubmitButtonClicked()">Update  General Preferences</button>
    </div>
          
  <input type="hidden" name="preferenceType" value="general">
  <input type="hidden" name="submitme" value="submitme">
</form>
