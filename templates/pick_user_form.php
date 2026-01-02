
<html>
<head>
<title>Clubpro Login</title>


<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/reset-fonts-grids/reset-fonts-grids.css" rel="stylesheet" type="text/css"> 
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.css?123" rel=stylesheet type=text/css>

<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/assets/skins/sam/button.css" />
<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/yahoo-dom-event/yahoo-dom-event.js"></script>

<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/element/element-min.js"></script>
<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/button-min.js"></script>

<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/forms.js" type="text/javascript"></script>

</head>



<body bgcolor=#ffffff link=#0000ff vlink=#000099 alink=#ff0000 class="yui-skin-sam">

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



<form name="entryform" method="post" action="<?=$_SESSION["wantsurl"]?>">

<table cellspacing="0" cellpadding="20" width="400"  class="generictable" id="formtable">


 <tr class="borderow">
    <td class="loginth">
    	<span class="whiteh1">
    		<div align="center">Please Select User</div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>


       
        <table>
        <tr>
                <td class="label">User:</td>
                <td>

					<select name="userid">
					
								<? 
									//variable set in login.php
									while($user = mysqli_fetch_array($usersResult)){ ?>
                        			<option value="<?=$user['userid']?>" ><? print $user['firstname']?></option>
                        	   <? } ?>
                    </select>

			    </td>
        </tr>
        <tr>
        	<td class="normal"> This Id is assciated to multiple players, please select who you would like to login as.</td>
        	<td><input type="hidden" name="frompickform" value=""></td>
        </tr>
        <tr>
        	<td>
        		<input type="button" value="Sign in with this user" id="submitbutton">
        	</td>
        </tr>
        
        </table>
       
</td>
</tr>
</table>

 </form>
 
 
 
</body>
</html>