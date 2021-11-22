<?php

/*
 * MailWatch for MailScanner
 * Copyright (C) 2003-2011  Steve Freegard (steve@freegard.name)
 * Copyright (C) 2011  Garrod Alwood (garrod.alwood@lorodoes.com)
 * Copyright (C) 2014-2018  MailWatch Team (https://github.com/mailwatch/1.2.0/graphs/contributors)
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * In addition, as a special exception, the copyright holder gives permission to link the code of this program with
 * those files in the PEAR library that are licensed under the PHP License (or with modified versions of those files
 * that use the same license as those files), and distribute linked combinations including the two.
 * You must obey the GNU General Public License in all respects for all of the code used other than those files in the
 * PEAR library that are licensed under the PHP License. If you modify this program, you may extend this exception to
 * your version of the program, but you are not obligated to do so.
 * If you do not wish to do so, delete this exception statement from your version.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free
 * Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once __DIR__ . '/functions.php';
require __DIR__ . '/login.function.php';

if (isset($_GET['type'])) {
    $url_type = deepSanitizeInput($_GET['type'], 'url');
    if (!validateInput($url_type, 'urltype')) {
        $url_type = '';
    }
} else {
    $url_type = '';
}

if (isset($_POST['to'])) {
    $url_to = deepSanitizeInput($_POST['to'], 'string');
    if (!empty($url_to) && !validateInput($url_to, 'user')) {
        $url_to = '';
    }
} elseif (isset($_GET['to'])) {
    $url_to = deepSanitizeInput($_GET['to'], 'string');
    if (!validateInput($url_to, 'user')) {
        $url_to = '';
    }
} else {
    $url_to = '';
}

if (isset($_GET['host'])) {
    $url_host = deepSanitizeInput($_GET['host'], 'url');
    if (!validateInput($url_host, 'host')) {
        $url_host = '';
    }
} else {
    $url_host = '';
}

if (isset($_POST['from'])) {
    $url_from = deepSanitizeInput($_POST['from'], 'string');
    if (!validateInput($url_from, 'user')) {
        $url_from = '';
    }
} elseif (isset($_GET['from'])) {
    $url_from = deepSanitizeInput($_GET['from'], 'string');
    if (!validateInput($url_from, 'user')) {
        $url_from = '';
    }
} else {
    $url_from = '';
}

if (isset($_POST['submit'])) {
    $url_submit = deepSanitizeInput($_POST['submit'], 'url');
    if (!validateInput($url_submit, 'listsubmit')) {
        $url_submit = '';
    }
} elseif (isset($_GET['submit'])) {
    $url_submit = deepSanitizeInput($_GET['submit'], 'url');
    if (!validateInput($url_submit, 'listsubmit')) {
        $url_submit = '';
    }
} else {
    $url_submit = '';
}

if (isset($_POST['list'])) {
    $url_list = deepSanitizeInput($_POST['list'], 'url');
    if (!validateInput($url_list, 'list')) {
        $url_list = '';
    }
} elseif (isset($_GET['list'])) {
    $url_list = deepSanitizeInput($_GET['list'], 'url');
    if (!validateInput($url_list, 'list')) {
        $url_list = '';
    }
} else {
    $url_list = '';
}

if (isset($_POST['domain'])) {
    $url_domain = deepSanitizeInput($_POST['domain'], 'url');
    if (!empty($url_domain) && !validateInput($url_domain, 'host')) {
        $url_domain = '';
    }
} else {
    $url_domain = '';
}

if (isset($_GET['listid'])) {
    $url_id = deepSanitizeInput($_GET['listid'], 'num');
    if (!validateInput($url_id, 'num')) {
        $url_id = '';
    }
} else {
    $url_id = '';
}

// Split user/domain if necessary (from detail.php)
$touser = '';
$to_domain = '';
if (preg_match('/(\S+)@(\S+)/', $url_to, $split)) {
    $touser = $split[1];
    $to_domain = $split[2];
} else {
    $to_domain = $url_to;
}

// Type
switch ($url_type) {
    case 'h':
        $from = $url_host;
        break;
    case 'f':
        $from = $url_from;
        break;
    default:
        $from = $url_from;
}

$myusername = safe_value(stripslashes($_SESSION['myusername']));
// Validate input against the user type
$to_user_filter = array();
$to_domain_filter = array();
$to_address = '';
switch ($_SESSION['user_type']) {
    case 'U': // User
        $sql1 = "SELECT filter FROM user_filters WHERE username='$myusername' AND active='Y'";
        $result1 = dbquery($sql1);

        $filter = array();
        while ($row = $result1->fetch_assoc()) {
            $filter[] = $row['filter'];
        }
        $user_filter = array();
        foreach ($filter as $user_filter_check) {
            if (preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $user_filter_check)) {
                $user_filter[] = $user_filter_check;
            }
        }
        $user_filter[] = $myusername;
        foreach ($user_filter as $tempvar) {
            if (strpos($tempvar, '@')) {
                $ar = explode('@', $tempvar);
                $username = $ar[0];
                $domainname = $ar[1];
                $to_user_filter[] = $username;
                $to_domain_filter[] = $domainname;
            }
        }
        $to_user_filter = array_unique($to_user_filter);
        $to_domain_filter = array_unique($to_domain_filter);
        break;
    case 'D': // Domain Admin
        $sql1 = "SELECT filter FROM user_filters WHERE username='$myusername' AND active='Y'";
        $result1 = dbquery($sql1);

        while ($row = $result1->fetch_assoc()) {
            $to_domain_filter[] = $row['filter'];
        }
        if (strpos($_SESSION['myusername'], '@')) {
            $ar = explode('@', $_SESSION['myusername']);
            $domainname = $ar[1];
            $to_domain_filter[] = $domainname;
        } else {
            $to_domain_filter[] = $_SESSION['myusername'];
        }
        $to_domain_filter = array_unique($to_domain_filter);
        break;
    case 'A': // Administrator
        $to_address = 'default';
        break;
}
switch (true) {
    case(!empty($url_to)):
        $to_address = $url_to;
        if (!empty($url_domain)) {
            $to_address .= '@' . $url_domain;
        }
        break;
    case(!empty($url_domain)):
        $to_address = $url_domain;
        break;
}

// Submitted
if ($url_submit === 'add') {
    if (false === checkToken($_POST['token'])) {
        header('Location: login.php?error=pagetimeout');
        die();
    }
    if (false === checkFormToken('/lists.php list token', $_POST['formtoken'])) {
        header('Location: login.php?error=pagetimeout');
        die();
    }

    // Check input is valid
    if (empty($url_list)) {
        $errors[] = __('error071');
    }
    if (empty($from)) {
        $errors[] = __('error072');
    }

    $to_domain = strtolower($url_domain);
    // Insert the data
    if (!isset($errors)) {
        switch ($url_list) {
            case 'w': // Whitelist
                $list = 'whitelist';
                $listi18 = __('wl07');
                break;
            case 'b': // Blacklist
                $list = 'blacklist';
                $listi18 = __('bl07');
                break;
        }
        $sql = 'REPLACE INTO ' . $list . ' (to_address, to_domain, from_address) VALUES '
            . "('" . safe_value(stripslashes($to_address)) . "',"
            . "'" . safe_value($to_domain) . "',"
            . "'" . safe_value(stripslashes($from)) . "')";
        dbquery($sql);
        audit_log(sprintf(__('auditlogadded07', true), $from, $to_address, $listi18));
    }
    $to_domain = '';
    $touser = '';
    $from = '';
    $url_list = '';
}

// Delete
if ($url_submit === 'delete') {
    if (false === checkToken($_GET['token'])) {
        header('Location: login.php?error=pagetimeout');
        die();
    }
    $id = $url_id;
    switch ($url_list) {
        case 'w':
            $list = 'whitelist';
            $listi18 = __('wl07');
            break;
        case 'b':
            $list = 'blacklist';
            $listi18 = __('bl07');
            break;
    }

    $sqlfrom = "SELECT from_address FROM $list WHERE id='$id'";
    $result = dbquery($sqlfrom);
    $row = $result->fetch_array();
    $from_address = $row['from_address'];

    switch ($_SESSION['user_type']) {
        case 'U':
            $sql = "DELETE FROM $list WHERE id='$id' AND to_address='$to_address'";
            audit_log(sprintf(__('auditlogremoved07', true), $from_address, $to_address, $listi18));
            break;
        case 'D':
            $sql = "DELETE FROM $list WHERE id='$id' AND to_domain='$to_domain'";
            audit_log(sprintf(__('auditlogremoved07', true), $from_address, $to_address, $listi18));
            break;
        case 'A':
            $sql = "DELETE FROM $list WHERE id='$id'";
            audit_log(sprintf(__('auditlogremoved07', true), $from_address, $to_address, $listi18));
            break;
    }

    $id = safe_value($url_id);
    dbquery($sql);
    $to_domain = '';
    $touser = '';
    $from = '';
    $url_list = '';
}

/**
 * @param string $sql
 * @param string $list
 * @return array
 */
// Build table
function build_table($sql, $list)
{
    $sth = dbquery($sql);
    $table_html = '';
    $entries = $sth->num_rows;
    if ($sth->num_rows > 0) {
        while ($row = $sth->fetch_row()) {
            $table_html .= ' <tr>' . "\n";
            $table_html .= '  <td>' . $row[1] . '</td>' . "\n";
            $table_html .= '  <td>' . $row[2] . '</td>' . "\n";
            $table_html .= '  <td class="text-center"><a class="btn btn-outline-wr-red font-weight-bold btn-sm" href="lists.php?token=' . $_SESSION['token'] . '&amp;submit=delete&amp;listid=' . $row[0] . '&amp;to=' . $row[2] . '&amp;list=' . $list . '"><i class="fas fa-trash mr-2"></i>' . __('delete07') . '</a></td>' . "\n";
            $table_html .= ' </tr>' . "\n";
        }
    } else {
        $table_html = __('noentries07') . "\n";
    }

    return array('html' => $table_html, 'entry_number' => $entries);
}

$w = '';
$b = '';
switch ($url_list) {
    case 'w':
        $w = 'CHECKED';
        break;
    case 'b':
        $b = 'CHECKED';
        break;
}

html_head(__('wblists07'), 0, false, false);
html_body('lists');
?>

<div class="container-fluid">
    <ol class="breadcrumb my-4 title_page">
    <li class="breadcrumb-item title_page_li"><a href="lists.php"><?php echo __('lists03'); ?></a></li>
    </ol>
<div class="row">

    <div class="col-xl-6 col-md-10">
        <div class="card card-wr" style="min-height: 180px;">
            <div class="card-header card-header-warriors">
                <i class="fas fa-check-double"></i>
                <?php echo __('addwlbl07'); ?>
            </div>
            <div class="card-body card-body-warriors pt-0">
                <form action="lists.php" method="post">
                    <INPUT TYPE="HIDDEN" NAME="token" VALUE="<?php echo $_SESSION['token']; ?>">
                    <INPUT TYPE="HIDDEN" NAME="formtoken" VALUE="<?php echo generateFormToken('/lists.php list token'); ?>">
                <div class="row">
    <?php if ($_SESSION['user_type'] === 'A'): ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="small mb-1 w-label" for="from"><?php echo __('from03'); ?></label>
                            <input 
                                type="text" 
                                name="from" 
                                value="<?php echo $from; ?>" 
                                class="form-control w-input-text"  
                                id="from"  
                                placeholder="From" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-6 pr-1">
                                <div class="form-group">
                                    <label class="small mb-1 w-label" for="input_to"><?php echo __('to03'); ?></label>
                                    <input 
                                        class="form-control w-input-text" 
                                        id="input_to" 
                                    type="text" 
                                    name="to" 
                                    value="<?php echo stripslashes($touser); ?>" 
                                    placeholder="To"/>
                                </div>
                            </div>
                            <div class="col-6 pl-1">
                                <div class="form-group">
                                    <label class="small mb-1 w-label" for="domain">@</label>
                                    <input 
                                        type="text" 
                                        name="domain" 
                                        size=25 
                                        value="<?php echo $to_domain; ?>" 
                                        class="form-control w-input-text" 
                                        id="domain" 
                                        placeholder="domain" />
                                </div>
                            </div>
                        </div>
                    </div>
    <?php elseif ($_SESSION['user_type'] === 'D'): ?>
       
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="small mb-1 w-label" for="from"><?php echo __('from03'); ?></label>
                            <input 
                                type="text" 
                                name="from" 
                                value="<?php echo $from; ?>" 
                                class="form-control w-input-text"  
                                id="from"  
                                placeholder="From" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-6 pr-1">
                                <div class="form-group">
                                    <label class="small mb-1 w-label"><?php echo __('to03'); ?></label>
                                    <input type="text" name="to" size=22  class="form-control w-input-text" value="<?php echo stripslashes($touser); ?>">
                                </div>
                            </div>
                            <div class="col-6 pl-1">
                                <div class="form-group">
                                    <label class="small mb-1 w-label">@</label>
                                    <select name="domain" class="form-control custom-select w-input-text">
                                    <?php
                                         foreach ($to_domain_filter as $to_domain_selection) {
                                             if ($to_domain === $to_domain_selection) {
                                                 echo '<option selected>' . $to_domain_selection . '</option>';
                                             } else {
                                                 echo '<option>' . $to_domain_selection . '</option>';
                                             }
                                         }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>               
    <?php elseif ($_SESSION['user_type'] === 'U'): ?>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="small mb-1 w-label" for="from"><?php echo __('from03'); ?></label>
                            <input 
                                type="text" 
                                name="from" 
                                value="<?php echo $from; ?>" 
                                class="form-control w-input-text"  
                                id="from"  
                                placeholder="From" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-6 pr-1">
                                <div class="form-group">
                                    <label class="small mb-1 w-label"><?php echo __('to03'); ?></label>
                                    <select name="to" class="form-control custom-select w-input-text">
                                    <?php
                                        foreach ($to_user_filter as $to_user_selection) {
                                            if ($touser === $to_user_selection) {
                                                echo '<option selected>' . stripslashes($to_user_selection) . '</option>';
                                            } else {
                                                echo '<option>' . stripslashes($to_user_selection) . '</option>';
                                            }
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 pl-1">
                                <div class="form-group">
                                    <label class="small mb-1 w-label">@</label>
                                    <select name="domain" class="form-control custom-select w-input-text">
                                    <?php
                                        foreach ($to_domain_filter as $to_domain_selection) {
                                            if ($to_domain === $to_domain_selection) {
                                                echo '<option selected>' . $to_domain_selection . '</option>';
                                            } else {
                                                echo '<option>' . $to_domain_selection . '</option>';
                                            }
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>   
    <?php endif; ?>
                    <div class="col-sm-6">
                        <div class="d-flex flex-nowrap justify-content-start">
                            <div class="form-group form-check mr-sm-4 mr-1">
                                <input 
                                    type="radio" 
                                    value="w" 
                                    name="list" 
                                    class="form-check-input" 
                                    id="radio-white" 
                                    <?php echo $w; ?> />
                                <label class="form-check-label" for="radio-white">
                                <?php echo __('wl07'); ?>
                                </label>
                            </div>
                            <div class="form-check ml-sm-4 ml-1">
                                <input 
                                    type="radio" 
                                    value="b" 
                                    name="list" 
                                    class="form-check-input" 
                                    id="radio-black" 
                                    <?php echo $b; ?> />
                                <label class="form-check-label" for="radio-black">
                                <?php echo __('bl07'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex flex-nowrap justify-content-end">
                            <div class="pr-1">
                                <button type="reset" value="reset" class="btn btn-wr-black font-weight-bold btn-sm" style="width: 100px;">
                                    <i class="fas fa-undo"></i>
                                    <?php echo __('reset07'); ?>
                                </button>
                            </div>
                            <div class="pl-1">
                                <button type="submit" name="submit" value="add" class="btn btn-wr-red font-weight-bold btn-sm" style="width: 100px;">
                                    <i class="fas fa-plus mr-1"></i>
                                    <?php echo __('add07'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Div errors alert -->
                    <?php if (isset($errors)): ?>
                        <div class="col-12">
                                <div class="alert alert-danger" role="alert">
                                    <?php echo implode('<br>', $errors); ?>
                                </div>
                        </div>
                    <?php endif; ?>
                </div>
                </form>
            </div>
        </div>
    </div>

    <?php 
        $whitelist = build_table(
            'SELECT id, from_address, to_address FROM whitelist WHERE ' . $_SESSION['global_list'] . ' ORDER BY from_address',
            'w'
        );
        $blacklist = build_table(
            'SELECT id, from_address, to_address FROM blacklist WHERE ' . $_SESSION['global_list'] . ' ORDER BY from_address',
            'b'
        );
    ?>

    <!-- Tabla Whitelist -->
    <div class="col-xl-12">
        <div class="card card-wr">
            <div class="card-header card-header-warriors">
                <i class="far fa-list-alt"></i>
                <?php echo sprintf(__('wlentries07'), $whitelist['entry_number']); ?>
            </div>
            <div class="card-body card-body-warriors">
                <?php if ($whitelist['entry_number'] != 0): ?>
                <div class="table-responsive table-res-wr">
                    <table class="table table-sm table-striped table-wr" id="table_whitelist">
                        <thead>
                            <tr>
                                <th><?php echo __('from07'); ?></th>
                                <th><?php echo __('to07'); ?></th>
                                <th><?php echo __('action07'); ?></th>
                            </tr>
                        </thead>
                        <tbody >
                            <?php echo $whitelist['html']; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: 
                    echo $whitelist['html'];
                endif ?>
            </div>
        </div>
    </div>
    <!-- Tabla Blacklist -->
    <div class="col-xl-12">
        <div class="card card-wr">
            <div class="card-header card-header-warriors">
                <i class="fas fa-list-alt"></i>
                <?php echo sprintf(__('blentries07'), $blacklist['entry_number']); ?>
            </div>
            <div class="card-body card-body-warriors">
                <?php if ($blacklist['entry_number'] != 0): ?>
                <div class="table-responsive table-res-wr">
                    <table class="table table-sm table-striped table-wr" id="table_blacklist">
                        <thead class="table-head-warriors">
                            <tr>
                                <th><?php echo __('from07'); ?></th>
                                <th><?php echo __('to07'); ?></th>
                                <th ><?php echo __('action07'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="table-body-warriors">
                            <?php echo $blacklist['html']; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: 
                    echo $blacklist['html'];
                endif ?>
            </div>
        </div>
    </div>

</div>
</div>
<?php
// Add footer
html_end_new(); 

// Close any open db connections
dbclose();
?>


