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

// Require the functions page
require_once __DIR__ . '/functions.php';

// Require the login function code
require __DIR__ . '/login.function.php';

// Start the header code and Title
html_head(__('spamassassinbayesdatabaseinfo18'), 0, false, false);
html_body('other');

// Enter the Action in the Audit log
audit_log(__('auditlog18', true));

echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li"><a href="other.php">'.__('toolslinks03').'</a></li>';
echo '<li class="breadcrumb-item title_page_li active">'.__('bayesdatabaseinfo18').'</li>';
echo '</ol>';
echo ' <div class="d-flex justify-content-center">';

echo '<div class="card card-wr" style="width: 70%; min-width: 300px;">';
echo '    <div class="card-header card-header-warriors">';
echo '        <i class="fas fa-table mr-1"></i>';
echo __('bayesdatabaseinfo18');
echo '    </div>';
echo '    <div class="card-body card-body-warriors">';

// Clear Bayes database
if ($_SESSION['user_type'] === 'A') {
    $return = 0;
    if (isset($_POST['clear'])) {
        if (!is_file(SA_DIR . 'sa-learn')) {
            echo '<div class="error center">' . "\n";
            echo '<br>' . __('cannotfind18') . ' ' . SA_DIR . 'sa-learn';
            echo '</div>' . "\n";
        } else {
            // You can use --force-expire instead of --clear to test the routine
            passthru(SA_DIR . 'sa-learn -p ' . SA_PREFS . ' --clear', $return);
            if ($return === 0) {
                audit_log(__('auditlogwipe18', true));
            } else {
                echo '<div class="error center">' . "\n";
                echo '<br>' . __('error18') . ' ' . $return;
                echo '</div>' . "\n";
            }
        }
    }
}
echo '    <table class="table table-bordered table-warriors-rep dataTable">';
echo '        <tbody class="table-body-warriors-rep">';
// Open the spamassassin file
if (!is_file(SA_DIR . 'sa-learn')) {
    die(__('cannotfind18') . ' ' . SA_DIR . 'sa-learn');
}
$fh = popen(SA_DIR . 'sa-learn -p ' . SA_PREFS . ' --dump magic', 'r');

while (!feof($fh)) {
    $line = rtrim(fgets($fh, 4096));

    debug('line: ' . $line . "\n");

    if (preg_match('/(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+non-token data: (.+)/', $line, $regs)) {
        switch ($regs[5]) {
            case 'nspam':
                echo '<tr><td class="heading">' . __('nbrspammessage18') . '</td><td align="right">' . number_format(
                        $regs[3]
                    ) . '</td></tr>' . "\n";
                break;

            case 'nham':
                echo '<tr><td class="heading">' . __('nbrhammessage18') . '</td><td align="right">' . number_format(
                        $regs[3]
                    ) . '</td></tr>' . "\n";
                break;

            case 'ntokens':
                echo '<tr><td class="heading">' . __('nbrtoken18') . '</td><td align="right">' . number_format(
                        $regs[3]
                    ) . '</td></tr>' . "\n";
                break;

            case 'oldest atime':
                echo '<tr><td class="heading">' . __('oldesttoken18') . '</td><td align="right">' . date(
                        'r',
                        $regs[3]
                    ) . '</td></tr>' . "\n";
                break;

            case 'newest atime':
                echo '<tr><td class="heading">' . __('newesttoken18') . '</td><td align="right">' . date(
                        'r',
                        $regs[3]
                    ) . '</td></tr>' . "\n";
                break;

            case 'last journal sync atime':
                echo '<tr><td class="heading">' . __('lastjournalsync18') . '</td><td align="right">' . date(
                        'r',
                        $regs[3]
                    ) . '</td></tr>' . "\n";
                break;

            case 'last expiry atime':
                echo '<tr><td class="heading">' . __('lastexpiry18') . '</td><td align="right">' . date('r', $regs[3]) . '</td></tr>' . "\n";
                break;

            case 'last expire reduction count':
                echo '<tr><td class="heading">' . __('lastexpirycount18') . '</td><td align="right">' . number_format(
                        $regs[3]
                    ) . ' ' . __('tokens18') .'</td></tr>' . "\n";
                break;
        }
    }
}
// Close the file
pclose($fh);

echo '        </tbody>';
echo '    </table>';

// Clear button
if ($_SESSION['user_type'] === 'A') {
    echo '<br>' . "\n";
    echo '<div class="center">' . "\n";
    echo '<form method="post" action="bayes_info.php" onsubmit="return confirm(\'' . __('clearmessage18') . '\');" >' . "\n";
    echo '<button class="btn btn-wr-red" type="submit" value="' . __('cleardbbayes18') . '">' . __('cleardbbayes18') . '</button>';
    echo '<input type="hidden" name="clear" value="true">' . "\n";
    echo '</form>' . "\n";
    echo '</div>' . "\n";
    echo '<br>' . "\n";
}

echo '    </div>';
echo '</div> ';

echo '</div>';
echo '</div>';
// Add footer
html_end_new(); 

// Close any open db connections
dbclose();

