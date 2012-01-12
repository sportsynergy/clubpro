<?
/*
 * $LastChangedRevision: 857 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:08:03 -0500 (Mon, 14 Mar 2011) $
 */
?>

<div align="center">
  <table width="650" cellpadding="20" cellspacing="0" class="generictable">
    <tr>
      <td class="clubid<?=get_clubid()?>th" ><span class=whiteh1>
        <div align="center">
          <? pv($DOC_TITLE) ?>
        </div>
        </span></td>
    </tr>
    <tr>
      <td><table cellspacing="5" cellpadding="1" width="650" class="borderless">
          <tr>
            <td class=label>First Name:</td>
            <td class="normal"><?=$frm["firstname"] ?></td>
            <td rowspan="4" valign="top"><div align="center"> <img src="<?=get_gravatar($frm["email"],120 )?>" /> </div></td>
          </tr>
          <tr>
            <td class=label>Last Name:</td>
            <td class="normal"><?=$frm["lastname"] ?></td>
          </tr>
          <tr>
            <td class=label>Email:</td>
            <td class="normal"><a href="mailto:<?=$frm["email"] ?>">
              <?=$frm["email"] ?>
              </a></td>
          </tr>
          <tr>
            <td class=label>Home Phone:</td>
            <td class="normal"><? if($frm["homephone"]==0)
				echo "Not Specified";
				else
				pv($frm["homephone"]);
				?></td>
          </tr>
          <? if(!empty($frm["workphone"])){?>
          <tr>
            <td class=label>Work Phone:</td>
            <td class="normal"><? pv($frm["workphone"]);?></td>
          </tr>
          <? } ?>
          <? if(!empty($frm["cellphone"])){?>
          <tr>
            <td class=label>Mobile Phone:</td>
            <td class="normal"><?  pv($frm["cellphone"]); ?></td>
          </tr>
          <? } ?>
          <? if(!empty($frm["pager"])){?>
          <tr>
            <td class=label>Pager:</td>
            <td class="normal"><? pv($frm["pager"]);?></td>
          </tr>
          <? } ?>
          <? if(!empty($frm["msince"])){?>
          <tr>
            <td class="label">Member Since:</td>
            <td class="normal"><?=$frm["msince"] ?></td>
          </tr>
          <? } ?>
          <tr>
            <td class="label" valign="top">Address:</td>
            <td class="normal" colspan="2"><textarea name="useraddress" cols="60" rows="5" disabled="disabled"><? pv($frm["useraddress"]) ?>
</textarea></td>
          </tr>
          <tr>
            <?
		   // Get the Custom Parameters
		  while( $parameterArray = db_fetch_array($extraParametersResult)){ 
			
			$parameterValue = load_site_parameter($parameterArray['parameterid'], $userid );

			
			if($parameterArray['parametertypename'] == "text" && !empty($parameterValue) ){ ?>
          <tr>
            <td class="label"><? pv($parameterArray['parameterlabel'])?>
              :</td>
            <td><? pv($parameterValue) ?></td>
          </tr>
          <? } elseif($parameterArray['parametertypename'] == "select" && !empty($parameterValue) ) { ?>
          <tr>
            <td class="label"><? pv($parameterArray['parameterlabel'])?>
              :</td>
            <td><? pv( load_parameter_option_name($parameterArray['parameterid'], $parameterValue)  ) ?></td>
          </tr>
          <? } ?>
          <? } ?>
          <? if( $frm["lastlogin"] != null){ ?>
          <tr>
            <td class="label">Last Login:</td>
            <td class="normal"><?=determineLastLoginText($frm["lastlogin"], get_clubid()) ?></td>
          </tr>
          <? } ?>
          <tr>
            <td class="label" valign="top">Rankings:</td>
            <td colspan="2"><table width="300">
                <?  while ($registeredArray = db_fetch_array($registeredSports)){ ?>
                <tr>
                  <td style="padding: 0px"><?=$registeredArray['courttypename']?></td>
                  <td style="padding: 0px"><?=$registeredArray['ranking']?></td>
                </tr>
                <?  }
							?>
              </table></td>
          </tr>
          <tr height="15">
            <td colspan="3"></td>
          </tr>
        </table></td>
    </tr>
  </table>
  <div style="height: 2em;"></div>
  <div style="text-align: left;">
    <?php

// This page may be loaded from either the rankings page or the player lookup.  In either case, we should 
// return the user from where they came from

// Update 1-11-2012
// Now that the navigation to this page has been converted from POST to get, this below can likely be removed
// Users can instead use the standard Back button in their browser.  As the GET method is stored in the history of the URL.

/*
if($origin=="lookup"){ ?>
    <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_lookup.php">
      <a href="javascript:submitForm('backtolistform');"><< Back to List</a>
      <input type="hidden" name="searchname" value="<?=$searchname?>">
    </form>
    <? } elseif($origin=="ladder"){ ?>
    <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_ladder.php">
      <a href="javascript:submitForm('backtolistform');"><< Back to Ladder</a>
    </form>
    <? }elseif($origin=="rankings") {?>
    <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">
      <a href="javascript:submitForm('backtolistform');"><< Back to Rankings</a>
      <input type="hidden" name="courttypeid" value="<?=$courttypeid?>">
      <input type="hidden" name="sortoption" value="<?=$sortoption?>">
      <input type="hidden" name="displayoption" value="<?=$displayoption?>">
      <input type="hidden" name="origin" value="<?=$origin?>">
    </form>
    <? }*/ 
?>
  </div>
</div>
