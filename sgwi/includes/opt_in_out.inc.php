<?php

/**************************************
SQLgrey Web Interface
Filename:	opt_in_out.inc.php
Purpose: 	Opt in/out functions
Version: 	1.1.8
***************************************/

	if ($_GET["direction"] == "out") {
		$helptag_dir = "<br />(recipients for whom messages are never greylisted)";
		$helptag_cardheader = "Recipients for whom messages are never greylisted";
		$table = "optout_";
	} else {
		$helptag_cardheader = "Recipients for whom messages are never greylisted unless they are in the ";
		$helptag_dir = "<br />(recipients for whom messages are always greylisted unless they are in the ";
		$table = "optin_";
	}
	
	if ($_GET["what"] == "domain") {
		$helptag_what_cardheader = "opt-out domain table";
		$helptag_what = "opt-out domain table)";
		$table .= "domain";
		$field = "domain";
	} else {
		$helptag_what_cardheader = "opt-out e-mail table";
		$helptag_what = "opt-out e-mail table)";
		$table .= "email";
		$field = "email";
	}
?>