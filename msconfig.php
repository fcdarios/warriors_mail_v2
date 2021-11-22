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

if ($_SESSION['user_type'] !== 'A') {
    header('Location: index.php');
}

html_head(__('config27'));
html_body('other');

audit_log(__('auditlog27', true));
$conf_dir = get_conf_include_folder();
$MailScanner_conf_file = '' . MS_CONFIG_DIR . 'MailScanner.conf';
$array_output = array();
$array_output1 = parse_conf_file($MailScanner_conf_file);
$array_output2 = parse_conf_dir($conf_dir);

if (is_array($array_output2)) {
    $array_output = array_merge($array_output1, $array_output2);
} else {
    $array_output = $array_output1;
}
// Display the information from the configuration files
echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li"><a href="other.php">'.__('toolslinks03').'</a></li>';
echo '<li class="breadcrumb-item title_page_li active">'.__('msconfig27').'</li>';
echo '</ol>';
echo ' <div class="d-flex flex-column">';

echo '<div class="card card-wr">';
echo '    <div class="card-header card-header-warriors">';
echo '        <i class="fas fa-table mr-1"></i>';
echo __('msconfig27');
echo '    </div>';
echo '    <div class="card-body card-body-warriors">';
echo '    <table class="table table-bordered table-warriors-rep dataTable">';
echo '        <tbody class="table-body-warriors-rep">';
foreach ($array_output as $out_key => $value) {
    // expand %var% variables
    if (preg_match('/(%.+%)/', $value, $match)) {
        $value = preg_replace('/%.+%/', $var[$match[1]], $value);
    }

    // See if parameter is a rules file
    if (@is_file($value) && @is_readable($value) && !@is_executable($value)) {
        $value = '<a href="msrule.php?file=' . $value . '">' . $value . '</A>';
    }

    // Change newline charactors to <br />
    $value = nl2br(str_replace("\\n", "\n", $value));

    // change <br /> to <br> to keep with html 4.01 and above
    $value = preg_replace("/<br \/>/i", '<br>', $value);

    echo '<tr><td class="align-middle">' . $out_key . '</td><td>' . $value . '</td></tr>' . "\n";
}
echo '        </tbody>';
echo '    </table>';
echo '    </div>';
echo '</div> ';

echo '</div>';
echo '</div>';

// Add footer
html_end_new(); 

// Close any open db connections
dbclose();

