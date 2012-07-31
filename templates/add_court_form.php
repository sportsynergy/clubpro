

<script type="text/javascript">

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}

</script>



<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">



<table cellspacing="0" cellpadding="20" width="550" class="generictable" id="formtable">
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>
 <tr>

    <td >

     <table width="550" cellpadding="5" cellspacing="2">
        
        <tr>
            <td class="label">Name:</td>
            <td>
            	<input type="text" name="courtname" maxlength="30" size="30">
            </td>
        </tr>
       
        <tr>
            <td class="label">Court Type:</td>
            <td><select name="courttypeid">
            		<?

            		while($courttype = mysql_fetch_array($courttypes)){ ?>
            			<option value="<?=$courttype['courttypeid']?>"> <?=$courttype['courttypename']?></option>
            			
            		<? } ?>
                        
                </select>
            </td>
        </tr>

		<tr>
            <td class="label">Open Time:</td>
            <td><select name="opentime">
            		<?
            		for($i = 0; $i < 23; ++$i){ ?>
            			<option value="<?=$i?>:00:00"><?=$i?>:00:00</option>
            			
            		<? } ?>
                        
                </select>
            </td>
        </tr>
   
			<tr>
	            <td class="label">Close Time:</td>
	            <td><select name="closetime">
	            		<?
	            		for($i = 0; $i < 23; ++$i){ ?>
	            			<option value="<?=$i?>:00:00"><?=$i?>:00:00</option>

	            		<? } ?>

	                </select>
	            </td>
	        </tr>
     
       <tr>
           <td>
           		<input type="button" name="submit" value="<?=$DOC_TITLE?>" id="submitbutton"/>
				<input type="hidden" name="siteid" value="<?=$_SESSION["selected_site"]?>"/>
           		<input type="hidden" name="submitme" value="submitme"/>
          </td>
       </tr>
       	
 </table>
</td>

</tr>

</table>
</form>


<div style="height: 2em;"></div>
<div>
	<span class="normal"> 
		<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/site_info.php?siteid=<?=$_SESSION["selected_site"]?>" > << Back to Site Info </a> 
	</span>
</div>