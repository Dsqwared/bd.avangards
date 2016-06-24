<?php
ini_set("pcre.backtrack_limit", "100000000");
	ini_set("pcre.recursion_limit", "230013370");
		ini_set('session.cookie_lifetime', 60 * 60 * 24 * 1);
	set_time_limit(0);
	ini_set('date.timezone', "Europe/Kiev");

require("classes.php");


$prs=new Parser();
$prs->parse();