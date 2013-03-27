<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */

/**
* Class and Function List:
* Function list:
* Classes list:
*/



if (isset($clubid)) {
    $messagequery = "SELECT message, enable
                        FROM  tblMessages WHERE siteid = " . get_siteid() ." AND messagetypeid = 1 ORDER BY id ";
    $messageresult = db_query($messagequery);
    $messagearray = db_fetch_array($messageresult);
    
    if ($messagearray['enable'] == 1) {
?>
<script language="JavaScript1.2">

			/*
			Cross browser Marquee script- (C) Dynamic Drive (www.dynamicdrive.com)
			For full source code, 100's more DHTML scripts, and Terms Of Use, visit http://www.dynamicdrive.com
			Credit MUST stay intact
			*/
			
			//Specify the marquee's width (in pixels)
			var marqueewidth="770px";
			//Specify the marquee's height
			var marqueeheight="25px";
			//Specify the marquee's marquee speed (larger is faster 1-10)
			var marqueespeed=2;
			//configure background color:
			var marqueebgcolor="";
			//Pause marquee onMousever (0=no. 1=yes)?
			var pauseit=1;
			
			//Specify the marquee's content (don't delete <nobr> tag)
			//Keep all content on ONE line, and backslash any single quotations (ie: that\'s great):
			
			var marqueecontent='<nobr><font face="Arial"><strong><big><?echo "$messagearray[message]"?></big></strong></font></nobr>';
			
			
			////NO NEED TO EDIT BELOW THIS LINE////////////
			marqueespeed=(document.all)? marqueespeed : Math.max(1, marqueespeed-1) //slow speed down by 1 for NS
			var copyspeed=marqueespeed
			var pausespeed=(pauseit==0)? copyspeed: 0
			var iedom=document.all||document.getElementById
			if (iedom)
			document.write('<span id="temp" style="visibility:hidden;position:absolute;top:-100px;left:-9000px">'+marqueecontent+'</span>')
			var actualwidth=''
			var cross_marquee, ns_marquee
			
			function populate(){
			if (iedom){
			cross_marquee=document.getElementById? document.getElementById("iemarquee") : document.all.iemarquee
			cross_marquee.style.left=parseInt(marqueewidth)+8+"px"
			cross_marquee.innerHTML=marqueecontent
			actualwidth=document.all? temp.offsetWidth : document.getElementById("temp").offsetWidth
			}
			else if (document.layers){
			ns_marquee=document.ns_marquee.document.ns_marquee2
			ns_marquee.left=parseInt(marqueewidth)+8
			ns_marquee.document.write(marqueecontent)
			ns_marquee.document.close()
			actualwidth=ns_marquee.document.width
			}
			lefttime=setInterval("scrollmarquee()",20)
			}
			window.onload=populate
			
			function scrollmarquee(){
			if (iedom){
			if (parseInt(cross_marquee.style.left)>(actualwidth*(-1)+8))
			cross_marquee.style.left=parseInt(cross_marquee.style.left)-copyspeed+"px"
			else
			cross_marquee.style.left=parseInt(marqueewidth)+8+"px"
			
			}
			else if (document.layers){
			if (ns_marquee.left>(actualwidth*(-1)+8))
			ns_marquee.left-=copyspeed
			else
			ns_marquee.left=parseInt(marqueewidth)+8
			}
			}
			
			if (iedom||document.layers){
			with (document){
			document.write('<table border="0" cellspacing="0" cellpadding="0"><td>')
			if (iedom){
			write('<div style="position:relative;width:'+marqueewidth+';height:'+marqueeheight+';overflow:hidden">')
			write('<div style="position:absolute;width:'+marqueewidth+';height:'+marqueeheight+';background-color:'+marqueebgcolor+'" onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=marqueespeed">')
			write('<div id="iemarquee" style="position:absolute;left:0px;top:0px"></div>')
			write('</div></div>')
			}
			else if (document.layers){
			write('<ilayer width='+marqueewidth+' height='+marqueeheight+' name="ns_marquee" bgColor='+marqueebgcolor+'>')
			write('<layer name="ns_marquee2" left=0 top=0 onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=marqueespeed"></layer>')
			write('</ilayer>')
			}
			document.write('</td></table>')
			}
			}
			</script>

<ilayer width=&{marqueewidth}; height=&{marqueeheight}; name="cmarquee01"> <layer name="cmarquee02"></layer> </ilayer>
<?
}
 }
?>
