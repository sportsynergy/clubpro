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
* - pat_Empty()
* - pat_gen()
* Classes list:
*/
/**
 * Copyright 2006 Marek J. Bisz
 * Version:20071213
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
function pat_Empty($var) {
    return ((!isset($var)) || ($var == ''));
}

/*
 * diplays js tags for ajax
*/
function pat_gen($arg, $name, $req) {
    $var = (!pat_Empty($arg['uid'])) ? 'var ' . $arg['uid'] . '=' : '';
?>
<?=$var?>new AjaxJspTag.<?=$name?>(
 "<?=$arg['baseUrl']?>", {
<?
unset($arg['baseUrl']); 
$i=1;	
$l=count($arg);
foreach ($arg as $k=>$v){
$con=($i<$l)?',':'';
$i++;
switch ($k){
	case 'postFunction':
	case 'errorFunction':
	case 'emptyFunction':	
		?><?=$k?>: <?=$v?><?=$con?>
		<?
		break;
	default:
		?><?=$k?>: "<?=$v?>"<?=$con?>
		<?
	}
}
?>
});
<?
}

function pat_select($arg){pat_gen($arg,'Select',array());}
function pat_autoComplete($arg){pat_gen($arg,'Autocomplete',array());}
function pat_callout($arg){pat_gen($arg,'Callout',array());}

function pat_updateField($arg){pat_gen($arg,'UpdateField',array());}
function pat_htmlContent($arg){pat_gen($arg,'phpHtmlContent',array());}
function pat_portlet($arg){pat_gen($arg,'Portlet',array());}


function pat_tabPanel($arg){
	$panelStyleId=$arg['panelStyleId'];
	$contentStyleId=$arg['contentStyleId'];
	$currentStyleId=$arg['currentStyleId'];
?>	
<div id="<?=$panelStyleId?>">
<ul>
<? 
$defaultId='';
$defaultBaseUrl='';
$defaultParameters='';
foreach ($arg['tabs'] as $k => $v) {
	$default='';
	if ($v['defaultTab']){
		$default='id="'.$currentStyleId.'"';
		$defaultId=$currentStyleId;
		$defaultBaseUrl=$v['baseUrl'];
		$defaultParameters=$v['parameters'];
	}
	?>
  <li id="<?=$k?>"><a <?=$default?> href="javascript://nop/" onclick="executeAjaxTab(this, '<?=$v['baseUrl']?>', '<?=$v['parameters']?>'); return false;"><?=$v['caption']?></a></li>
<? } //foreach ?>
</ul>
</div>
<div id="<?=$contentStyleId?>"></div>
<script type="text/javascript">
function executeAjaxTab(elem, url, params) {
  var myAjax = new AjaxJspTag.TabPanel(url, {
parameters: params,
currentStyleId: "<?=$currentStyleId?>",
target: "<?=$contentStyleId?>",
source: elem
});
}
<? if ($defaultBaseUrl!='') { ?>
	addOnLoadEvent(executeAjaxTab($('<?=$defaultId?>'), '<?=$defaultBaseUrl?>', '<?=$defaultParameters?>'));
<? } ?>	
</script>
<?
}

function pat_toggle($arg){pat_gen($arg,'Toggle',array());}