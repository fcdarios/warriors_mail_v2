<?php

/********************************************
SQLgrey Web Interface
Filename:	awl.php
Purpose: 	Renders the greylist page
Version: 	1.1.9
 *********************************************/
require_once "sgwi/includes/functions.inc.php";
require "sgwi/includes/awl.inc.php";
require_once "functions.php";

// Input validation for mode
if (isset($_GET['mode'])) {
	$mode = $_GET["mode"];
	if (!preg_match('/^(domains|email)$/', $mode)) {
		die("ERROR: invalid mode");
	}
} else {
	$mode = "out";
}
// Input validation for action
if (isset($_GET['action'])) {
	$action = $_GET["action"];
	if (!preg_match('/^[a-zA-Z0-9=&?_-]{2,256}$/', $action)) {
		die("ERROR: invalid action");
	}
} else {
	$action = "";
}
// Input validation for csort
if (isset($_GET['csort'])) {
	$csort = $_GET["csort"];
	if (!preg_match('/^(sender_name|sender_domain|src|rcpt|first_seen|last_seen)$/', $csort)) {
		die("ERROR: invalid sort option");
	}
} else {
	$csort = "";
}
// Input validation for sort
if (isset($_GET['sort'])) {
	$sort = $_GET["sort"];
	if (!preg_match('/^(sender_name|sender_domain|src|rcpt|first_seen|last_seen)$/', $sort)) {
		die("ERROR: invalid sort option");
	}
} else {
	$sort = "";
}
// Input validation for order
if (isset($_GET['order']) && !preg_match('/^(asc|desc)$/', $_GET['order'])) {
	die("ERROR: invalid order field");
}

// For sort order
if ($sort == null || $sort == "") {
	if ($mode == "email") {
		$sort = "sender_name";
	} else {
		$sort = "sender_domain";
	}
}
$dir = "asc";
$ndir = "desc";
if ($sort == $csort && $_GET["order"] == "desc") {
	$dir = "desc";
	$ndir = "asc";
}

//  Perform demanded action.
$clearit = '<br /><br /><a class="navlike" href="awl.php?mode=' . $mode . '">Clear this report</a>';
switch ($action) {
	case "del_selection":
		// For batch deleting.
		isset($_POST["chk"]) ? $chk = $_POST["chk"] : $chk = "";
		if ($chk == "") {
			$report = '<br />Nothing was selected - nothing has been deleted.' . $clearit;
		} else {
			foreach ($chk as $args) {
				$parts = explode("@@", $args);
				delete_entry($mode, $parts[0], $parts[1], $parts[2]);
			}
			$report = $deleted . $clearit;
		}
		$report2 = "";
		break;
	case "del_undef":
		delete_undef($mode);
		$report = "";
		$report2 = $message;
		break;
	case "add_sender":
		isset($_POST["sender_name"]) ? $sn = $_POST["sender_name"] : $sn = "";
		add_sender($mode, $sn, $_POST["sender_domain"], $_POST["src"]);
		$report = "";
		$report2 = $added;
		break;
	case "":
		$report = "";
		$report2 = "";
		break;
}

//  Make a nice header.
if ($mode == "email") {
	$query = "SELECT COUNT(*) AS count FROM from_awl";
	$title = __('greyemailaddr65') . " (";
} else {
	$query = "SELECT COUNT(*) AS count FROM domain_awl";
	$title = __('greydomains65') . " (";
}
$result = do_query($query);
$n = fetch_row($result);
$title .= $n["count"] . ")";

/*
	  mysql> describe from_awl;
	  +---------------+---------------+------+-----+----------------+-------+
	  | Field         | Type          | Null | Key | Default        | Extra |
	  +---------------+---------------+------+-----+----------------+-------+
	  | sender_name   | varchar(64)   |      | PRI |                |       |
	  | sender_domain | varchar(255)  |      | PRI |                |       |
	  | src           | varchar(39)   |      | PRI |                |       |
	  | first_seen    | timestamp(14) | YES  |     | NULL           |       |
	  | last_seen     | timestamp(14) | YES  | MUL | 00000000000000 |       |
	  +---------------+---------------+------+-----+----------------+-------+

	  mysql> describe domain_awl;
	  +---------------+---------------+------+-----+----------------+-------+
	  | Field         | Type          | Null | Key | Default        | Extra |
	  +---------------+---------------+------+-----+----------------+-------+
	  | sender_domain | varchar(255)  |      | PRI |                |       |
	  | src           | varchar(39)   |      | PRI |                |       |
	  | first_seen    | timestamp(14) | YES  |     | NULL           |       |
	  | last_seen     | timestamp(14) | YES  | MUL | 00000000000000 |       |
	  +---------------+---------------+------+-----+----------------+-------+
	*/

$refresh = html_head("greylist", 0, false, false);
html_body('grey', 'sgwi_' . $mode);

echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li">' . __('greylist65') . '</li>';
echo '<li class="breadcrumb-item title_page_li active">' . __('whitelisted03') . ' ' . $title . '</li>';
echo '</ol>';
echo '<div class="row">';
// ------------------------------------------

?>

<div class="col-md-12">
	<div class="card card-wr">
		<div class="card-header card-header-warriors">
			<?php if ($mode === 'email') { ?>
				<i class="fas fa-envelope-square mr-1"></i>
			<?php } else { ?>
				<i class="fas fa-globe mr-1"></i>
			<?php }
			echo __('greyawlcardtitle65');
			?>
		</div>
		<div class="card-body col-xl-12">
			<form method="post" action="sgwi_awl.php?mode=<?php echo $mode; ?>&amp;action=del_selection">
				<div class="d-flex flex-row align-items-center justify-content-end mb-2">
					<a class="btn btn-sm btn-wr-red" style="color:white;" href="#end" title="End of List">EoL</a>
				</div>
				<div class="table-responsive">
					<table role="table" class="table table-bordered table-warriors-rep dataTable" width="100%" cellspacing="0" border="0" summary="sortbar"><?php echo ('
				<thead class="table-head-warriors-rep">
				<tr>
				<th width="20">&nbsp;</th>');
																																							if ($mode == "email") echo ('
				<th width="300">&nbsp;<b><a class="greylink" href="sgwi_awl.php?mode=' . $mode . '&amp;sort=sender_name&amp;csort=' . $sort . '&amp;order=' . $ndir . '">' . __('greysendername65') . '</a></b></th>');
																																							echo ('
				<th width="240"><b><a class="greylink" href="sgwi_awl.php?mode=' . $mode . '&amp;sort=sender_domain&amp;csort=' . $sort . '&amp;order=' . $ndir . '">' . __('greysenderdomain65') . '</a></b></th>
				<th width="100"><b><a class="greylink" href="sgwi_awl.php?mode=' . $mode . '&amp;sort=src&amp;csort=' . $sort . '&amp;order=' . $ndir . '">' . __('greyawlsource65') . '</a></b></th>
				<th width="120"><b><a class="greylink" href="sgwi_awl.php?mode=' . $mode . '&amp;sort=first_seen&amp;csort=' . $sort . '&amp;order=' . $ndir . '">' . __('firstseen50') . '</a></b></th>
				<th width="120"><b><a class="greylink" href="sgwi_awl.php?mode=' . $mode . '&amp;sort=last_seen&amp;csort=' . $sort . '&amp;order=' . $ndir . '">' . __('greyawllastseen65') . '</a></b></th>
				</tr>
				</thead>');
																																							?>
						<tbody class="table-body-warriors-rep">

							<?php
							if ($mode == "email") {
								if ($sort == "sender_name") {
									$order = "sender_name " . $dir . ", sender_domain " . $dir;
								} else if ($sort == "sender_domain") {
									$order = "sender_domain " . $dir . ", sender_name " . $dir;
								} else {
									$order = $sort . " " . $dir;
								}
								$query = "SELECT sender_name, sender_domain, src, first_seen, last_seen FROM from_awl ORDER BY " . $order;
							} else {
								$order = $sort . " " . $dir;
								$query = "SELECT sender_domain, src, first_seen, last_seen FROM domain_awl ORDER BY " . $order;
							}
							$result = do_query($query);

							while ($line = fetch_row($result)) {
								$sd = $line["sender_domain"];
								$src = $line["src"];
								$fs = $line["first_seen"];
								$ls = $line["last_seen"];
								if ($mode == "email") {
									$sn = $line["sender_name"];
									echo ('
					<tr>
						<td width="20"><input type="checkbox" name="chk[]" value="' . $sn . '@@' . $sd . '@@' . $src . '" /></td>
						<td width="300"><span title="' . $sn . '">' . shorten_it($sn, 42) . '</span></td>
						');
								} else {
									$sn = "noname";
									echo ('
					<tr>
						<td width="20"><input type="checkbox" name="chk[]" value="' . $sn . '@@' . $sd . '@@' . $src . '" /></td>
						');
								}
								echo ('
						<td width="240"><span title="' . $sd . '">' . shorten_it($sd, 35) . '</span></td>
						<td width="100"><span title="' . $src . '">' . shorten_it($src, 15) . '</span></td>
						<td width="120">' . strip_millisecs($fs) . '</td>
						<td width="120">' . strip_millisecs($ls) . '</td>
					</tr>
					');
							}
							?>
							<tr>
								<td colspan="6"><a id="end"></a></td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="d-flex flex-row align-items-center justify-content-end mb-4">
					<a class="btn btn-sm btn-wr-red" style="color:white;" href="#" title="Top of List">ToL</a>
				</div>

				<div class="d-flex flex-row align-items-center mt-2">
					<button class="btn btn-outline-wr-red font-weight-bold btn-sm" type="submit">
						<?php echo __('greyawldeleteselected65'); ?>
					</button>
				</div>
		</div>
	</div>
</div>
</form>

<?php if (!$report == '') {
	echo '<div class="col-md-12">
	<div class="card card-wr">
		<div class="card-header card-header-warriors">
			<i class="far fa-clipboard mr-1"></i>
			' . __('report04') . '
		</div>
		<div class="card-body col-xl-12">';
	echo '			<span class="alert">' . $report . '</span>';
	echo '	</div>
	</div>
 </div>';
} ?>

<div class="col-md-12">
	<div class="card card-wr">
		<div class="card-header card-header-warriors">
			<i class="fas fa-plus mr-1"></i>
			<?php echo __('addwl04'); ?>
		</div>
		<div class="card-body col-xl-12">
			<form action="sgwi_awl.php?mode=<?php echo $mode; ?>&amp;action=add_sender" method="post">

				<?php if ($mode == "email") { ?>
					<div class="d-flex flex-row mb-2">
						<div class="mr-2 w-25"><?php echo __('greysendername65'); ?>:</div>
						<input class="form-control w-input-text" type="text" name="sender_name" />
					</div>
				<?php } ?>

				<div class="d-flex flex-row mb-2">
					<div class="mr-2 w-25"><?php echo __('greyawlsourcecd65'); ?>:</div>
					<input class="form-control w-input-text" type="text" name="sender_domain" />
				</div>

				<div class="d-flex flex-row mb-2">
					<div class="mr-2 w-25"><?php echo __('greysenderdomain65'); ?>:</div>
					<input class="form-control w-input-text " type="text" name="src" />
				</div>

				<div class="d-flex justify-content-between flex-row">
					<button class="btn btn-wr-black btn-sm" type="submit">
						<i class="fas fa-plus mr-1"></i>
						<?php echo __('add07'); ?>
					</button>
					<a class="btn btn-wr-red btn-sm" href="sgwi_awl.php?mode=<?php echo $mode; ?>&amp;action=del_undef"><?php echo __('greyawldeleteudef65'); ?></a>
				</div>
			</form>
		</div>
	</div>
</div>

<?php if (!$report2 == '') {
	echo '<div class="col-md-12">
			<div class="card card-wr">
				<div class="card-header card-header-warriors">
					<i class="far fa-clipboard mr-1"></i>
					' . __('report04') . '
				</div>
				<div class="card-body col-xl-12">';
	echo '			<span class="alert">' . $report2 . '</span>';
	echo '		</div>
			</div>
 		</div>';
} ?>

<?php
echo '</div>';
echo '</div>';
// Add footer
html_end_new();

// Close any open db connections
dbclose();
?>