<?php
/*
 * $LastChangedRevision: 672 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-02-17 13:53:52 -0800 (Tue, 17 Feb 2009) $

*/
?>
<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<table width="600" cellpadding="20" cellspacing="0">
    <tr>
    <td class=clubid<?=get_clubid()?>th><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class="generictable">

       <form name="entryform" method="post" action="<?=$ME?>">
       <table cellspacing="5" cellpadding="1" width="600" >
       
  <?if(!isSiteAutoLogin()){ ?>

        <tr>
 			<td class=label><font color="Red" class=normalsm>* </font>Username:</td>
            <td><input type="text" name="username" size=25 value="<? pv($frm["username"]) ?>">
                <?err($errors->username)?>
            </td>
        </tr>
     
       <tr>
 			<td class=label>Password:</td>
            <td><input type="text" name="password" size=25 value="">
            <br>
            <span class="normalsm" > By leaving this field blank, the password will not be updated.</span>
            </td>
        </tr>
         <? } ?>
        <tr>
            <td class=label><font color="Red" class=normalsm>* </font>First Name:</td>
            <td><input type="text" name="firstname" size=25 value="<? pv($frm["firstname"]) ?>">
                <?err($errors->firstname)?>
            </td>
        </tr>
        
        <tr>
            <td class=label><font color="Red" class=normalsm>* </font>Last Name:</td>
            <td><input type="text" name="lastname" size=25 value="<? pv($frm["lastname"]) ?>">
                <?err($errors->lastname)?>
                </td>
        </tr>

        <tr>
            <td class=label></font>Email:</td>
            <td><input type="text" name="email" size=25 value="<? pv($frm["email"]) ?>">
                <?err($errors->email)?>
                </td>
        </tr>

        <tr>
            <td class=label></font> Home Phone:</td>
            <td><input type="text" name="homephone" size=25 value="<? pv($frm["homephone"]) ?>">
                <?err($errors->homephone)?>
                </td>
        </tr>

        <tr>
            <td class=label></font> Work Phone:</td>
            <td><input type="text" name="workphone" size=25 value="<? pv($frm["workphone"]) ?>">
                <?err($errors->workphone)?>
            </td>
        </tr>
         <tr>
            <td class=label>Cell Phone:</td>
            <td><input type="text" name="cellphone" size=25 value="<? pv($frm["cellphone"]) ?>">
                <?err($errors->cellphone)?>
            </td>
        </tr>
         <tr>
            <td class=label>Pager:</td>
            <td><input type="text" name="pager" size=25 value="<? pv($frm["pager"]) ?>">
                <?err($errors->pager)?>
            </td>
        </tr>

        <tr>
            <td class=label>Address:</td>
            <td><textarea name="useraddress" cols=50 rows=5><? pv($frm["useraddress"]) ?></textarea>
                <?err($errors->address)?>
            </td>
        </tr>
        <tr>
            <td class=label>
            <?if(isSiteAutoLogin()){ ?>
            	<font color="Red" class=normalsm>* 
             <? } ?>	
            Membership ID:</td>
            <td><input type="text" name="memberid" size=35 value="<? pv($frm["memberid"]) ?>">
                <?err($errors->memberid)?>
            </td>
        </tr>
        <tr>
            <td class=label>Gender:</td>
            <td><select name="gender">
            	<option value="1">Male</option>
            	<option value="0" <? if($frm["gender"]==0) print "selected" ?> >Female</option>
            </select>
                <?err($errors->gender)?>
            </td>
        </tr>
       <tr>
            <td colspan="2" height="20"><hr></td>
        </tr>
        <tr>
            <td class="label">Receive Players Wanted Notifications:</td>
            <td><select name="recemail"> 
                 <option value="y">Yes</option>
                <option value="n" <? if($frm["recemail"]=='n') print "selected" ?> >No</option>
                <?err($errors->recemail)?>
                </td>
        </tr>

		<tr>
            <td class="label">Role:</td>
            <td>
            		<select name="roleid"> 
                        <option value="1" <? if($frm["roleid"]==1) print "selected" ?> >Player</option>
                        <option value="5" <? if($frm["roleid"]==5) print "selected" ?>>Limited Access Player</option>
                        <option value="4" <? if($frm["roleid"]==4) print "selected" ?>>Desk User</option>
                        <option value="2" <? if($frm["roleid"]==2) print "selected" ?> >Club Admin</option>
                      </select>
                </td>
        </tr>
        <tr>
            <td colspan="2" height="20"><hr></td>
        </tr>
          <tr>
            <td class=label valign="top">Authorized Sites:</td>
            <td class="normal">

             <? //Select only the authorized sites

             $autSitesStack = array();

             for($i=0; $i<mysql_num_rows($authSites); ++$i){
             $authSitesArray = mysql_fetch_array($authSites);

                   array_push($autSitesStack, $authSitesArray['siteid']);

                   if($i==0){
                       $mysites =  $authSitesArray['siteid'];
                   }
                  else{
                       $mysites .=  ",$authSitesArray[siteid]";
                  }

             }

                 for($j=0; $j<mysql_num_rows($availableSites); ++$j){
                 $row = mysql_fetch_array($availableSites);


                        if(in_array($row['siteid'],$autSitesStack)){
                          $selected = "checked";
                        }
                        else{
                            $selected = "";
                        }



                        print "<input type=\"checkbox\" name=\"clubsite$row[siteid]\" value=\"$row[siteid]\" $selected> $row[sitename] <br>\n";

                     unset($selected);
                 }

              //Done with Authorized Sites
             ?>

            </td>
          </tr>
        <tr>
            <td colspan="2" height="20"></td>
        </tr>

        <?



        for ( $i=0; $i<mysql_num_rows($availbleSports); ++$i){
             $availbleSportsArray = db_fetch_array($availbleSports);

                  //Match up the users ranking with the availbe court types
                 for ($j=0; $j<mysql_num_rows($registeredSports); ++$j){

                   $registeredArray = db_fetch_array($registeredSports);
                       //Put the results in a nice little string to be passed up in the post vars.

                       if($j==0){
                           $mycourtTypes =  $registeredArray['courttypeid'];
                       }
                       else{
                           $mycourtTypes .=  ",$registeredArray[courttypeid]";
                       }

					   if($availbleSportsArray['courttypeid'] == $registeredArray['courttypeid']){
					   	 $ranking = $registeredArray['ranking'];
					   }
                         
                 }


               if(mysql_num_rows($registeredSports)>0){
                    mysql_data_seek($registeredSports,0);
               }

         ?>
         <tr valign=top>
                <td class=label><? echo "$availbleSportsArray[courttypename] Ranking" ?>:</td>
                <td><input type="text" name="<? echo "courttype$availbleSportsArray[courttypeid]" ?>" size=25 value="<?=$ranking ?>">

                </td>
        </tr>
         <?
           unset($ranking);
           //While closing bracket - DO NOT remove
           }
         ?>
       <tr>
            <td colspan="2" height="20"></td>
        </tr>
          <tr>
            <td class=label>Enable:</td>

            <?

            if  ( $frm["enable"]=='y' ){
            echo "<td><input type=\"checkbox\" name=\"enable\" value=\"y\" checked></td>";
            }
            else{
            echo "<td><input type=\"checkbox\" name=\"enable\" value=\"1\" ></td>";
            }
            ?>
                <?err($errors->enable)?>

        </tr>

        <tr>
            <td colspan="2" height="20"></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="submit" value="Update Settings">
            <input type="hidden" name="userid" value="<?pv($userid) ?>"></td>
            <input type="hidden" name="mycourttypes" value="<? pv($mycourtTypes) ?>">
            <input type="hidden" name="mysites" value="<? pv($mysites) ?>">
            <input type="hidden" name="searchname" value="<? pv($searchname)?>">
            </td>
        </tr>
        <tr>
            <td colspan="2"><font color="Red" class="normalsm">* </font><font class="normalsm">indicates a required field</font></td>
        </tr>
        </table>
       </form>

    </td>
</tr>

   <tr>
       <td colspan="6" align="right" class="normal">

         <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">
         <a href="javascript:submitForm('backtolistform');"><< Back to List</a></td>&nbsp;&nbsp;&nbsp;
         <input type="hidden" name="searchname" value="<? pv($searchname)?>">
         </form>

      </td>
   </tr>
</table>


</td>
</tr>
</table>