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

// Include of necessary functions
require_once __DIR__ . '/functions.php';
include __DIR__ . '/msre_table_functions.php';

// Authentication checking
require __DIR__ . '/login.function.php';

// Check to see if the user is an administrator
if ($_SESSION['user_type'] !== 'A') {
    // If the user isn't an administrator send them back to the index page.
    header('Location: index.php');
    audit_log(__('auditlog29', true));
} else {
    
    html_head(__('rulesetedit29'), 0, false, false);

    html_body('other');

    echo '<div class="container-fluid">';
    echo '<ol class="breadcrumb my-4 title_page">';
    echo '<li class="breadcrumb-item title_page_li"><a href="other.php">'.__('toolslinks03').'</a></li>';
    echo '<li class="breadcrumb-item title_page_li active">'.__('rulesetedit29').'</li>';
    echo '</ol>';
    echo ' <div class="d-flex flex-column">';

    echo'<div>
      <div class="card card-wr">
          <div class="card-header card-header-warriors">
            <i class="fas fa-edit"></i>
              ' . __('editrule29') . '
          </div>
          <div class="card-body card-body-warriors">
          <div class="table-responsive ">
          <table  role="table" width="100%" id="table-tools" class="table-sm" cellspacing="0" >                       
               <tbody>';
    $ruleset_file = array();
    // Open directory and read its contents
    if (is_dir(MSRE_RULESET_DIR) && $dh = opendir(MSRE_RULESET_DIR)) {
        while ($file = readdir($dh)) {
            // If it's a ruleset (*.rules), add it to the array
            if (preg_match("/.+\.rules$/", $file)) {
                $ruleset_file[] = $file;
            }
        }
        closedir($dh);
    }

    if (empty($ruleset_file)) {
        TR(array(__('norulefound29')));
    } else {
        // Display it in a sorted table with links
        asort($ruleset_file);
        foreach ($ruleset_file as $this_ruleset_file) {
            TR(array('<a class="btn btn-outline-wr-redbw" href="msre_edit.php?token=' . $_SESSION['token'] .'&amp;file=' . $this_ruleset_file . '"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . $this_ruleset_file . '</a>'));
        }
        // Put a blank header line on the bottom. It just looks nicer that way to me.
        TRH(array(''));
    }
    echo '</tbody></table></div>' . "\n";
    echo '</div>';
    echo '</div>';
    
    // Add footer
    html_end_new(); 
}
