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

html_head(__('salint51'), 0, false, false);
html_body('other');

if (!$fp = popen(SA_DIR . 'spamassassin -x -D -p ' . SA_PREFS . ' --lint 2>&1', 'r')) {
    die(__('diepipe51'));
}

audit_log(__('auditlog51', true));

echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li"><a href="other.php">'.__('toolslinks03').'</a></li>';
echo '<li class="breadcrumb-item title_page_li active">'.__('salint51').'</li>';
echo '</ol>';
echo ' <div class="d-flex flex-column">';

echo '<div class="card card-wr">';
echo '    <div class="card-header card-header-warriors">';
echo '        <i class="fas fa-table mr-1"></i>';
echo __('salint51');
echo '    </div>';
echo '    <div class="card-body card-body-warriors">';
echo '    <table id="table_sa_lint" class="table table-bordered table-warriors-rep dataTable">';
echo '        <thead class="table-head-warriors-rep">';
echo '          <tr>';
echo '              <th class="m-0 pl-2 py-2" style="width: 90%;">' . __('message51') . '</th>';
echo '              <th class="m-0 pl-2 py-2" style="width: 10%;">' . __('time51') . '</th>';
echo '          </tr>';
echo '        </thead>';
echo '        <tbody class="table-body-warriors-rep">';
// Start timer
$start = get_microtime();
$last = false;
while ($line = fgets($fp, 2096)) {
    $line = preg_replace("/\n/i", '', $line);
    $line = preg_replace('/</', '&lt;', $line);
    if ($line !== '' && $line !== ' ') {
        $timer = get_microtime();
        $linet = $timer - $start;
        if (!$last) {
            $last = $linet;
        }
        // Check for 'subtests=' to add space after comma (to fit the screen)
        if (preg_match('/subtests=/i', $line)) {
            $line = str_replace(',', ', ', $line);
        }
        echo "<!-- Timer: $timer, Line Start: $linet -->\n";
        echo "    <TR>\n";
        echo "     <TD>$line</TD>\n";
        $thisone = $linet - $last;
        $last = $linet;
        if ($thisone >= 2) {
            echo '     <TD CLASS="lint_5">' . round($thisone, 5) . '</TD>' . "\n";
        } elseif ($thisone >= 1.5) {
            echo '     <TD CLASS="lint_4">' . round($thisone, 5) . '</TD>' . "\n";
        } elseif ($thisone >= 1) {
            echo '     <TD CLASS="lint_3">' . round($thisone, 5) . '</TD>' . "\n";
        } elseif ($thisone >= 0.5) {
            echo '     <TD CLASS="lint_2">' . round($thisone, 5) . '</TD>' . "\n";
        } elseif ($thisone < 0.5) {
            echo '     <TD CLASS="lint_1">' . round($thisone, 5) . '</TD>' . "\n";
        }
        echo "    </TR>\n";
    }
}
pclose($fp);
echo '        </tbody>';
echo '        <tfoot class="table-foot-warriors-rep">';
echo '          <tr>';
echo '              <th class="m-0 pl-2 py-2" style="width: 90%;">' . __('finish51') . '</th>';
echo '              <th class="m-0 pl-2 py-2" style="width: 10%;">' . round(get_microtime() - $start, 5) . '</th>';
echo '          </tr>';
echo '        </tfoot>';
echo '    </table>';
echo '    </div>';
echo '</div> ';



echo '</div>';
echo '</div>';
// Add footer
html_end_new(); 

// Close any open db connections
dbclose();