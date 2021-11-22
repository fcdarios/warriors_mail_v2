<?php

/***********************************************
SQLgrey Web Interface
Filename:	connect.php
Purpose: 	Renders the email/domains pages
Version: 	1.1.9
 ************************************************/

require "sgwi/includes/functions.inc.php";
require "sgwi/includes/connect.inc.php";
require_once "functions.php";


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

// For sort order.
if ($sort == null || $sort == "") {
	$sort = "sender_name";
}
$dir = "asc";
$ndir = "desc";
if ($sort == $csort && $_GET["order"] == "desc") {
	$dir = "desc";
	$ndir = "asc";
}

//  Perform demanded action.
$clearit = '<br /><br /><a class="btn btn-outline-wr-red font-weight-bold btn-sm" href="sgwi_connect.php">Clear this report</a>';
$report2 = "";
switch ($action) {
	case "act":
		isset($_POST["acttype"]) ? $acttype = $_POST["acttype"] : $acttype = "";
		isset($_POST["chk"]) ? $chk = $_POST["chk"] : $chk = "";
		switch ($acttype) {
			case "dodelete":
				// For batch deleting.
				if ($chk == '') {
					$report = '<br />Nothing was selected - nothing has been deleted.' . $clearit;
				} else {
					foreach ($chk as $args) {
						$parts = explode("@@", $args);
						forget_entry($parts[0], $parts[1], $parts[2], $parts[3]);
					}
					$report = $deleted . $clearit;
				}
				break;
			case "domove":
				// For batch moving to whitelist.
				if ($chk == '') {
					$report = '<br />Nothing was selected - nothing has been moved.' . $clearit;
				} else {
					foreach ($chk as $args) {
						$parts = explode("@@", $args);
						move_entry($parts[0], $parts[1], $parts[2], $parts[3]);
					}
					$report = $moved . $clearit;
				}
				break;
			case "":
				$report = '<br />Please select Forget... or Move...';
				break;
		}
		break;
	case "del_old":
		$year = $_POST["year"];
		$month = $_POST["month"];
		$day = $_POST["day"];
		$hour = $_POST["hour"];
		$minute = $_POST["minute"];
		$seconds = $_POST["seconds"];
		$err = 0;

		if ($year < 2000 || $year > 9999) $err = 1;
		else if ($month < 1 || $month > 12) $err = 1;
		else if ($day < 1 || $day > 31) $err = 1;
		else if ($hour < 0 || $hour > 23) $err = 1;
		else if ($minute < 0 || $minute > 59) $err = 1;
		else if ($seconds < 0 || $seconds > 60) $err = 1; # 60 indeed...

		del_older_than($year, $month, $day, $hour, $minute, $seconds, $err);
		$report2 = $message . $warning;
		$report = "";
		break;
	case "":
		$report = "";
		break;
}

// For the header.
$query = "SELECT COUNT(*) AS count FROM connect";
$result = do_query($query);
$n = fetch_row($result);

/* mysql> describe connect;
	  +---------------+---------------+------+-----+---------+-------+
	  | Field         | Type          | Null | Key | Default | Extra |
	  +---------------+---------------+------+-----+---------+-------+
	  | sender_name   | varchar(64)   |      |     |         |       |
	  | sender_domain | varchar(255)  |      |     |         |       |
	  | src           | varchar(39)   |      | MUL |         |       |
	  | rcpt          | varchar(255)  |      |     |         |       |
	  | first_seen    | timestamp(14) | YES  | MUL | NULL    |       |
	  +---------------+---------------+------+-----+---------+-------+
	*/

$refresh = html_head("greylist", 0, false, false);
html_body('grey', 'sgwi_connect');

echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li">' . __('greylist65') . '</li>';
echo '<li class="breadcrumb-item title_page_li active">' . __('greyhostdomainlisted') . ' (' . $n["count"] . ')</li>';
echo '</ol>';
echo '<div class="row">';
// ------------------------------------------

?>

<div class="col-md-12">
	<div class="card card-wr">
		<div class="card-header card-header-warriors">
			<i class="fas fa-envelope-square mr-1"></i>
			<?php echo __('greywaitingtitle65'); ?>
		</div>
		<div class="card-body col-xl-12">
			<form method="post" action="sgwi_connect.php?action=act">
				<div class="d-flex flex-row align-items-center justify-content-end mb-2">
					<a class="btn btn-sm btn-wr-red" style="color:white;" href="#end" title="End of List">EoL</a>
				</div>

				<div class="table-responsive">
					<table role="table" class="table table-bordered table-warriors-rep dataTable" width="100%" cellspacing="0" border="0" summary="data">
						<?php echo ('
					<thead class="table-head-warriors-rep">
					<tr>
						<th width="5">&nbsp;</th>
						<th width="210"><b><a class="greylink" href="sgwi_connect.php?sort=sender_name&amp;csort=' . $sort . '&amp;order=' . $ndir . '">' . __('greysendername65') . '</a></b></th>
						<th width="190"><b><a class="greylink" href="sgwi_connect.php?sort=sender_domain&amp;csort=' . $sort . '&amp;order=' . $ndir . '">' . __('greysenderdomain65') . '</a></b></th>
						<th width="100"><b><a class="greylink" href="sgwi_connect.php?sort=src&amp;csort=' . $sort . '&amp;order=' . $ndir . '">' . __('greyipaddr65') . '</a></b></th>
						<th width="260"><b><a class="greylink" href="sgwi_connect.php?sort=rcpt&amp;csort=' . $sort . '&amp;order=' . $ndir . '">' . __('greyrecipient65') . '</a></b></th>
						<th width="120"><b><a class="greylink" href="sgwi_connect.php?sort=first_seen&amp;csort=' . $sort . '&amp;order=' . $ndir . '">' . __('greyseenat65') . '</a></b></th>
					</tr>
					</thead>
				') ?>
						<tbody class="table-body-warriors-rep">
							<?php
							if ($sort == "sender_name")
								$order = "sender_name " . $dir . ", sender_domain " . $dir;
							else if ($sort == "sender_domain")
								$order = "sender_domain " . $dir . ", sender_name " . $dir;
							else
								$order = $sort . " " . $dir;
							$query = "SELECT sender_name, sender_domain, src, rcpt, first_seen FROM connect ORDER BY " . $order;
							$result = do_query($query);
							while ($line = fetch_row($result)) {
								$sn = $line["sender_name"];
								$sd = $line["sender_domain"];
								$src = $line["src"];
								$sr = $line["rcpt"];
								$fs = $line["first_seen"];
								echo ('
						<tr>
							<td width="5" ><div class="d-flex align-items-center"><input type="checkbox" name="chk[]" value="' . $sn . '@@' . $sd . '@@' . $src . '@@' . $sr . '" /></div></td>
							<td width="210"><div class="d-flex align-items-center"><span title="' . $sn . '">' . shorten_it($sn, 30) . '</span></div></td>
							<td width="190"><div class="d-flex align-items-center"><span title="' . $sd . '">' . shorten_it($sd, 30) . '</span></div></td>
							<td width="100"><div class="d-flex align-items-center"><span title="' . $src . '">' . shorten_it($src, 15) . '</span></div></td>
							<td width="260"><div class="d-flex align-items-center"><span title="' . $sr . '">' . shorten_it($sr, 40) . '</span></div></td>
							<td width="120"><div class="d-flex align-items-center">' . strip_millisecs($fs) . '</td>
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

				<div class="d-flex flex-row align-items-center justify-content-end">
					<a class="btn btn-sm btn-wr-red" style="color:white;" href="#" title="Top of List">ToL</a>
				</div>
				<div class="d-flex flex-column">
					<div class="d-flex flex-row">
						<input type="radio" class="mr-1" name="acttype" value="dodelete" /> <?php echo __('greyforgetselected65'); ?>
					</div>
					<div class="d-flex flex-row">
						<input type="radio" class="mr-1" name="acttype" value="domove" /> <?php echo __('greymoveselected65'); ?>
					</div>
				</div>
				<div class="d-flex flex-row align-items-center mt-2">
					<button class="btn btn-outline-wr-red font-weight-bold btn-sm" type="submit" value="Submit">
						<?php echo __('submit04'); ?>
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
	echo '		</div>
		</div>
	</div>';
} ?>

<div class="col-md-12">
	<div class="card card-wr">
		<div class="card-header card-header-warriors">
			<i class="fas fa-trash mr-1"></i>
			<?php echo __('greywaitingdelete65'); ?>
		</div>
		<div class="card-body col-xl-12">
			<form method="post" action="sgwi_connect.php?action=del_old&amp;sort=first_seen&amp;csort=first_seen&amp;order=asc">
				<div class="d-flex flex-row form-group align-items-center">
					<div class="d-flex flex-column pr-1">
						<div class="d-flex flex-row">
							<?php echo __('greyyear65'); ?>
						</div>
						<input type="text" class="form-control w-input-text" value="0" name="year" />
					</div>

					<div class="d-flex flex-column pr-1 pl-1">
						<div class="d-flex flex-row">
							<?php echo __('greymonth65'); ?>
						</div>
						<input type="text" class="form-control w-input-text" value="0" name="month" />
					</div>

					<div class="d-flex flex-column pr-1 pl-1">
						<div class="d-flex flex-row">
							<?php echo __('greyday65'); ?>
						</div>
						<input type="text" class="form-control w-input-text" value="0" name="day" />
					</div>

					<div class="d-flex flex-column pr-1 pl-1">
						<div class="d-flex flex-row">
							<?php echo __('greyhour65'); ?>
						</div>
						<input type="text" class="form-control w-input-text" value="0" name="hour" />
					</div>

					<div class="d-flex flex-column pr-1 pl-1">
						<div class="d-flex flex-row">
							<?php echo __('greyminute65'); ?>
						</div>
						<input type="text" class="form-control w-input-text" value="0" name="minute" />
					</div>

					<div class="d-flex flex-column pl-1">
						<div class="d-flex flex-row">
							<?php echo __('greysecond65'); ?>
						</div>
						<input type="text" class="form-control w-input-text" value="0" name="seconds" />
					</div>
				</div>

				<button class="btn btn-outline-wr-red font-weight-bold btn-sm " type="submit" name="submit" value="submit">
					<i class="fas fa-trash mr-2"></i><?php echo __('delete07'); ?>
				</button>
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