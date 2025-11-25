<script type="text/javascript">


</script>


<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

<div class="mb-3" style="width: 70%;">
    <label for="message" class="form-label">Footer Message</label>
    <input class="form-control" name="message" id="message" type="text" aria-label="message" value="<? pv($_SESSION["footermessage"]) ?>">
    <div id="messageHelp" class="form-text">This is the message that will appear at the footer of all pages. One thing to keep in mind with putting links in here is to include a target="_blank" so that the link is opened up in a new window.</div>
</div>

  <div class="mt-5">
    <button type="submit" class="btn btn-primary" id="submitbutton" onclick="onSubmitButtonClicked()">Update Footer Message</button>
    <input type="hidden" name="submitme" value="submitme">
  </div>


</form>

