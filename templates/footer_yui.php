
</div>
</div>

<!-- end: secondary column from outer template -->
</div>

<div style="height: 35px"></div>
<div id="ft">
  <div align="center">
    <hr/>
    <span>
    <?php if( ! empty($_SESSION["footermessage"]) ){ ?>
    <?php echo $_SESSION["footermessage"]; ?>
    <?php } ?>
    </span> <br/>
  </div>
  <div style="text-align: center;"> <span class="lighttext">Got a question? Let us 
  <a href="mailto:support@sportsynergy.net">know</a>.</span> </div>
  <div style="text-align: center;"> <span class="lighttext">&copy;2019
  <a href="http://sportsynergy.net" target="_blank">Sportsynergy</a> </span> </div>
</div>
</div>
<?php 
// PrettyPhoto Inclusion
if(	(defined("_JQUERY_") && _JQUERY_ == true) 
	&& (defined("_PRETTYPHOTO_") && _PRETTYPHOTO_ == true)){
	echo '<script type="text/javascript" charset="utf-8">
  $(document).ready(function(){
    $("a[rel^=\'prettyPhoto\']").prettyPhoto({social_tools: \'\'});
  });
</script>';
}
?>
</body>
</html>