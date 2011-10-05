<?
  /*
 * $LastChangedRevision: 857 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:08:03 -0500 (Mon, 14 Mar 2011) $

*/
?>


<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">




<table cellspacing="0" cellpadding="20" width="400" class="generictable">
 <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class=whiteh1>
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table width="400">


        <tr>
	        <td class=label>Member Name:</td>
	        <td><input type="text" name="searchname" size="25" value="<? pv($searchname) ?>">
	                <?err($errors->searchname)?>
	        </td>
		</tr>

       <tr>
           <td colspan="2">
	           Search for the first or last name of a member. *Note partial string are supported.
	            <span style="font-style: italic;"> i.e. Smi for Smith or Pet for Peter</span>
           </td>


       </tr>
       <tr>
           <td></td>
           <td><input type="submit" name="submit" value="Search"></td>

       </tr>
 </table>

</table>



</form>

<div style="height: 30px"></div>

<script type="text/javascript">

function defaultform(){
	document.entryform.searchname.focus();
}

defaultform();


</script>
