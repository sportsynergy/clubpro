<?php

$bad = array(
"trounced",
"crushed",
"clobbered",
"slaughtered",
"demolished",
"skunked",
"humbled",
"destroyed",
"wrecked",
"made mincemeat of",
"wiped the floor with",
"pasted",
"walloped",
"ran the table against",
"blitzed",
"trashed",
"walked all over",
"tyrannized",
"had their way with",
"sped past");

$not_bad = array(
"conquered",
"triumphed over",
"confounded",
"vanquished",
"overwhelmed",
"baffled",
"subdued",
"derailed",
"scuttled",
"stymied",
"overpowered",
"foiled",
"outplayed",
"frustrated",
"subjugated",
"thwarted",
"stumped",
"reigned supreme over",
"eclipsed",
"held the upper hand with",
"handled",
"glided by"
    );

$close = array(
"beat",
"got by",
"edged",
"stole by",
"surmounted",
"disappointed",
"narrowly defeated",
"barely beat",
"slipped past",
"held on to beat",
"inched by",
"snuck by",
"overcame",
"weaseled past",
"prevailed over",
"nosed by",
"shuffled past",
"crept by",
"eked out a victory over",
"squeezed past",
"battled past"
    );

$score = 2;


     if ($score == 0) {
        $rand_key = array_rand($bad,1);
        $var = $bad[$rand_key];
        
    } elseif ($score == 2) {
        $rand_key = array_rand($close,1);
        $var = $close[$rand_key];
        
    } else {
        $rand_key = array_rand($not_bad,1);
        $var = $not_bad[$rand_key];
        
    }

    print $var;
?>