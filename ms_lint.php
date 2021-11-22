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

html_head(__('mailscannerlint28'), 0, false, false);

html_body('other');

echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li"><a href="other.php">'.__('toolslinks03').'</a></li>';
echo '<li class="breadcrumb-item title_page_li active">'.__('mailscannerlint28').'</li>';
echo '</ol>';
echo ' <div class="d-flex flex-column">';

echo '<div class="card card-wr">
<div class="card-header card-header-warriors">
    <i class="far fa-list-alt"></i>';                      
        echo "\n" . __('mailscannerlint28') ."\n";                        
echo'       </div>
<div class="card-body card-body-warriors">';

echo '<div class="table-responsive">
<table  role="table" id="wr-table-enginelint" class="table table-bordered table-warriors-rep dataTable" width="100%" cellspacing="0">'."\n";

echo '<thead class="table-head-warriors-rep">'."\n";    
echo ' <tr  class="wr-tabletr">' . "\n";
echo '  <th class="pt-2 pb-2 pl-2 wr-tableheading2">' . __('message28') . '</th>' . "\n";
echo '  <th class="pt-2 pb-2 pl-2 wr-tableheading2">' . __('time28') . '</th>' . "\n";
echo ' </tr>' . "\n";
echo '</thead>'."\n";    

echo '<tbody class="table-body-warriors-rep">'."\n";    

if (!defined('MS_EXECUTABLE_PATH')) {
    echo '<tr class="pt-1 pb-1 wr-tabletr">
    <td colspan="2" class="pl-2 pr-2">' . __('errormessage28') . '</td>
    </tr>';
} else {
    if (!$fp = popen('sudo ' . MS_EXECUTABLE_PATH . ' --lint 2>&1', 'r')) {
        die(__('diepipe28'));
    }

    audit_log(__('auditlog28', true));

    // Start timer
    $start = get_microtime();
    $last = false;
    while ($line = fgets($fp, 2096)) {
        $line = preg_replace("/\n/i", '', $line);
        if ($line !== '' && $line !== ' ') {
            $timer = get_microtime();
            $linet = $timer - $start;
            if (!$last) {
                $last = $linet;
            }

            echo '<!-- Timer: ' . $timer . ', Line Start: ' . $linet . ' -->' . "\n";
            echo '    <tr class="pt-1 pb-1 wr-tabletr">' . "\n";
            echo '     <td class="pl-2 pr-2">' . $line . '</td>' . "\n";
            $thisone = $linet - $last;
            $last = $linet;
            if ($thisone >= 2) {
                echo '     <td class="pl-2 pr-2 lint_5">' . round($thisone, 5) . '</td>' . "\n";
            } elseif ($thisone >= 1.5) {
                echo '     <td class="pl-2 pr-2 lint_4">' . round($thisone, 5) . '</td>' . "\n";
            } elseif ($thisone >= 1) {
                echo '     <td class="pl-2 pr-2 lint_3">' . round($thisone, 5) . '</td>' . "\n";
            } elseif ($thisone >= 0.5) {
                echo '     <td class="pl-2 pr-2 lint_2">' . round($thisone, 5) . '</td>' . "\n";
            } elseif ($thisone < 0.5) {
                echo '     <td class="pl-2 pr-2 lint_1">' . round($thisone, 5) . '</td>' . "\n";
            }
            echo '    </tr>' . "\n";
        }
    }
    pclose($fp);
    echo '   <tr class="pt-1 pb-1 wr-tabletr">' . "\n";
    echo '    <td class="pl-2 pr-2"><b>' . __('finish28') . '</b></td>' . "\n";
    echo '    <td class="pl-2 pr-2"><b>' . round(get_microtime() - $start, 5) . '</b></td>' . "\n";
    echo '   </tr>' . "\n";
}
echo '<tbody>'."\n";    

echo '</table> </div>' . "\n";
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

// Add footer
html_end_new(); 
// close the connection to the Database
dbclose();
