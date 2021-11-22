<?php

/********************************************
SQLgrey Web Interface
Filename:	opt_in_out.php
Purpose: 	Renders the optin/out pages
Version: 	1.1.9
*********************************************/

	require "includes/functions.inc.php";
	require "includes/opt_in_out.inc.php";

	// Input validation for direction.
  if (isset($_GET['direction'])) { $direction = $_GET["direction"]; if (!preg_match('/^(in|out)$/',$direction )) { die( "ERROR: invalid direction field" ); } } else { $direction = "out"; }
  // Input validation for what
  if (isset($_GET['what'])) { $what = $_GET["what"]; if (!preg_match('/^(domain|email)$/',$what )) { die( "ERROR: invalid what field" );  } } else { $what = "domain"; }
  // Input validation for action
  if (isset($_GET['action'])) { $action = $_GET["action"]; if (!preg_match('/^(del|add)$/',$action )) { die( "ERROR: invalid action field" ); } } else { $action = ""; }
	// Input validation for field
	if (isset($_GET['field']) && !preg_match('/^[-a-zA-Z0-9=@#._%+-]{2,256}$/',$_GET['field'])) { die( "ERROR: invalid field" ); }

	//  Add some explanation.
	if ($direction == "out") {
		$helptag = $helptag_dir;
	} else {
		$helptag = $helptag_dir.$helptag_what;
	}

	// Perform demanded action.
	switch ($action) {
		case "del":
			$entry = $_GET["field"];
			if ($entry == '') {
				$report = '<br />Nothing was entered.';
			} else {
				do_query("DELETE FROM ".$table." WHERE ".$field."='".addslashes($entry)."'");
				$report = '<br />'.$entry.' deleted.';
			}
			break;
		case "add":
			$entry = $_POST[$field];
			if ($entry == '') {
				$report = '<br />Nothing was entered.';
			} else {
				if (preg_match('/^[-a-zA-Z0-9=@#._%+-]{2,256}$/',$entry)) {
        	do_query("INSERT INTO ".$table."(".$field.") VALUES('".addslashes(strtolower($entry))."')");
          $report = '<br />'.$entry.' added.';
        } else {
          $report = '<br />Invalid input syntax.';
        }
			}
			break;
		case "":
			$report = "";
			break;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo $title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="main.css" type="text/css" charset="utf-8" />
</head>

<body>

<div id="page">

	<div class="navcontainer">
		<?php shownav('grey','', $direction, $what); ?>
	</div>

	<table width="100%" summary="main">
	    <tr>
		<td>
			<p><span class="h1"><?php echo $title; ?></span>&nbsp;&nbsp;<span class="h2"><?php echo $helptag; ?></span></p>
			<table border="0" summary="data"><tr><td> </td></tr><?php
				$query = "SELECT ".$field." FROM ".$table." ORDER BY ".$field;
				$result = do_query($query);
				while($line = fetch_row($result)) {
				echo ('
				<tr>
					<td>'.$line[$field].'</td>
					<td><a href="opt_in_out.php?direction='.$direction.'&amp;what='.$what.'&amp;field='.$line[$field].'&amp;action=del">delete</a></td>
				</tr>');
				}
				echo "\n";
			?>
			</table>

			<br /><br />

			<form action="opt_in_out.php?direction=<?php echo $direction.'&amp;what='.$what; ?>&amp;action=add" method="post">
				<input type="text" name="<?php echo $field; ?>" size="40" />
				<input class="btn" type="submit" value="Add" />
			</form>
			<?php if (! $report == '' ) echo '<span class="alert">'.$report.'</span>'; ?>
		</td>
	    </tr>
	</table>

	<br />

	<div id="footer" style="width: 800px">
		<?php include "includes/copyright.inc.php" ?>
	</div>

</div>

</body>

</html>
