
<script type="text/javascript">

function removeCourtEvent(eventid)
{

      document.removecourtEventForm.eventid.value = eventid;
      document.removecourtEventForm.submit();


}

function manageCourtEvent(eventid)
{

      document.manageCourtEventForm.eventid.value = eventid;
      document.manageCourtEventForm.submit();


}

</script>

<form name="removecourtEventForm" action="<?=$ME?>" method="post">
  <input type="hidden" name="eventid" value="">
  <input type="hidden" name="preferenceType" value="court_events">
  <input type="hidden" name="action" value="removeCourtEvent">
</form>
<form name="manageCourtEventForm" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_court_event.php" method="POST">
  <input type="hidden" name="eventid" value="">
</form>


<form name="court_events_form" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">

<div class="mb-3">
    <div id="courteventHelp" class="form-text"> Not to be confused with Club Events, Court Events provide a way to use the court for a special purpose.
                Examples might be "Junior Clinic", "Ladies Night", "Tournament Play", etc.  When creating a Court Event you can specify a player limit, and when
                users go to make a court reservation they will have the option to select the Court Event for that reservation.  If the player limit has been reached
                for that event, the user will not be able to select that event for their reservation.
                <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_court_event.php">Add</a> 
    </div>
</div>



<table  class="table table-striped table-bordered">
    <?  if( mysqli_num_rows($courtEvents) < 1){   ?>
    <tr>
      <td class="normal" colspan="2">No Court Events Defined.  Why don't you add some.</td>
    </tr>
    <?   } else {    ?>
    <tr >
      <th>Event Name</th>
      <th>Player Limit</th>
      <th></th>
    </tr>
    <? 
              while($courtEvent = mysqli_fetch_array($courtEvents)){
                  $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
              ?>
    <tr> 
      <td>
        <?=$courtEvent['eventname'] ?>
        </td>
      <td>
        <?=$courtEvent['playerlimit'] ?>
      </td>
      <td>
        <form name="manageCourtEventsForm-<?=$courtEvent['eventid']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_court_event.php" method="post">
          <input type="hidden" name="eventid" value="<?=$courtEvent['eventid'] ?>">
        </form>
        <a href="javascript:manageCourtEvent(<?=$courtEvent['eventid'] ?>);">Edit </a> | <a href="javascript:removeCourtEvent(<?=$courtEvent['eventid'] ?>);">Delete</a>
    </td>
  </tr>
<?  } ?>
<? } ?>
</table>


<input type="hidden" name="preferenceType" value="court_events">
</form>
