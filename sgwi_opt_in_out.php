<?php

/********************************************
SQLgrey Web Interface
Filename:	opt_in_out.php
Purpose: 	Renders the optin/out pages
Version: 	1.1.9
 *********************************************/

require "sgwi/includes/functions.inc.php";
require "sgwi/includes/opt_in_out.inc.php";
require_once "functions.php";

// Input validation for direction.
if (isset($_GET['direction'])) {
	$direction = $_GET["direction"];
	if (!preg_match('/^(in|out)$/', $direction)) {
		die("ERROR: invalid direction field");
	}
} else {
	$direction = "out";
}
// Input validation for what
if (isset($_GET['what'])) {
	$what = $_GET["what"];
	if (!preg_match('/^(domain|email)$/', $what)) {
		die("ERROR: invalid what field");
	}
} else {
	$what = "domain";
}
// Input validation for action
if (isset($_GET['action'])) {
	$action = $_GET["action"];
	if (!preg_match('/^(del|add)$/', $action)) {
		die("ERROR: invalid action field");
	}
} else {
	$action = "";
}
// Input validation for field
if (isset($_GET['field']) && !preg_match('/^[-a-zA-Z0-9=@#._%+-]{2,256}$/', $_GET['field'])) {
	die("ERROR: invalid field");
}

if ($direction == "out") {
	if ($what == "domain")
		$title = __('greyoutdomain65');
	else
		$title = __('greyoutemail65');
} else {
	if ($what == "domain")
		$title = __('greyindomain65');
	else
		$title = __('greyinemail65');
}

//  Add some explanation.
if ($direction == "out") {
	$helptag = __('greyoptinouttitle165');
} else {
	if (strcmp($helptag_what_cardheader, 'opt-out domain table') === 0)
		$helptag = __('greyoptinouttitle265');
	else
		$helptag = __('greyoptinouttitle365');
}

// Perform demanded action.
switch ($action) {
	case "del":
		$entry = $_GET["field"];
		if ($entry == '') {
			$report = '<br />Nothing was entered.';
		} else {
			do_query("DELETE FROM " . $table . " WHERE " . $field . "='" . addslashes($entry) . "'");
			$report = '<br />' . $entry . ' deleted.';
		}
		break;
	case "add":
		$entry = $_POST[$field];
		if ($entry == '') {
			$report = '<br />Nothing was entered.';
		} else {
			if (preg_match('/^[-a-zA-Z0-9=@#._%+-]{2,256}$/', $entry)) {
				do_query("INSERT INTO " . $table . "(" . $field . ") VALUES('" . addslashes(strtolower($entry)) . "')");
				$report = '<br />' . $entry . ' added.';
			} else {
				$report = '<br />Invalid input syntax.';
			}
		}
		break;
	case "":
		$report = "";
		break;
}

$refresh = html_head("greylist", 0, false, false);
html_body('grey', 'sgwi_opt_' . $direction . '_' . $what);

echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li">' . __('greylist65') . '</li>';
echo '<li class="breadcrumb-item title_page_li active">' . $title . '</li>';
echo '</ol>';
echo '<div class="row">';
// ------------------------------------------

?>
<div class="col-md-12">
	<div class="card card-wr">
		<div class="card-header card-header-warriors">
			<?php if ($what === 'email') { ?>
				<i class="fas fa-envelope-square mr-1"></i>
			<?php } else { ?>
				<i class="fas fa-globe mr-1"></i>
			<?php }

			echo $helptag; ?>

		</div>
		<div class="card-body col-xl-12">

			<div class="table-responsive">
				<table role="table" id="wr-table" class="table table-bordered table-warriors-rep dataTable" width="100%" cellspacing="0" border="0" summary="data">
					<thead class="table-head-warriors-rep">
						<tr>
							<th width="75%"><?php if ($field === 'domain')
												echo __('greydomains65');
											else
												echo __('barmail03');
											?></th>
							<th width="25%"><?php echo __('action61'); ?></th>
						</tr>
					</thead>
					<tbody class="table-body-warriors-rep">
						<?php
						$query = "SELECT " . $field . " FROM " . $table . " ORDER BY " . $field;
						$result = do_query($query);
						while ($line = fetch_row($result)) {
							echo ('
							<tr>
								<td>' . $line[$field] . '</td>
								<td><a class="btn btn-outline-wr-red font-weight-bold btn-sm" href="sgwi_opt_in_out.php?direction=' . $direction . '&amp;what=' . $what . '&amp;field=' . $line[$field] . '&amp;action=del"><i class="fas fa-trash mr-2"></i>delete</a></td>
							</tr>');
						}
						echo "\n";
						?>
					</tbody>
				</table>
			</div>
			<div class="col-8">
				<form action="sgwi_opt_in_out.php?direction=<?php echo $direction . '&amp;what=' . $what; ?>&amp;action=add" method="post">
					<input type="text" name="<?php echo $field; ?>" size="40" />
					<button class="btn btn-outline-wr-black font-weight-bold btn-sm" type="submit" name="submit" value="Add">
						<i class="fas fa-plus mr-2"></i> <?php echo __('add07'); ?>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php if (!$report == '') {
	echo '<div class="col-md-12">
		<div class="card card-wr">
			<div class="card-header card-header-warriors">
				<i class="far fa-clipboard mr-1"></i>
				' . __('report04') . '
				</div>
			<div class="card-body col-xl-12">';
	echo '			<span class="alert">' . $report . '</span>';
	echo '		</div>
		</div>
	</div>';
} ?>




<div id="footer" style="width: 800px">
	<?php include "includes/copyright.inc.php" ?>
</div>


<?php
echo '</div>';
echo '</div>';
// Add footer
html_end_new();

// Close any open db connections
dbclose();
?>