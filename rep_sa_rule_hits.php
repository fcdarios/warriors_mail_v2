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
require_once __DIR__ . '/filter.inc.php';
require_once __DIR__ . '/functions.php';

// Authentication checking
require __DIR__ . '/login.function.php';

$filter = html_head(__('sarulehits37'), 0, false, true);


html_body('reports');

echo '<div class="container-fluid">';
   echo '<ol class="breadcrumb my-4 title_page">';
   echo '<li class="breadcrumb-item title_page_li"><a href="reports.php">'.__('reports03').'</a></li>';
   echo '<li class="breadcrumb-item title_page_li active">'.__('sarulehits37').'</li>';
   echo '</ol>';
   echo ' <div class="d-flex flex-column">';


$sql = '
 SELECT
  spamreport,
  isspam
 FROM
  maillog
 WHERE
  spamreport IS NOT NULL
 AND spamreport != ""
' . $filter->CreateSQL();

$result = dbquery($sql);
if (!$result->num_rows > 0) {
    die(__('diemysql99') . "\n");
}

// Initialise the array
$sa_array = array();

// Retrieve rows and insert into array
while ($row = $result->fetch_object()) {
    //##### TODEL/TODO #
    //##### TODEL/TODO # stdClass Object
    //##### TODEL/TODO # (
    //##### TODEL/TODO # [spamreport] => not spam (too large)
    //##### TODEL/TODO # [isspam] => 0
    //##### TODEL/TODO #)
    //##### TODEL/TODO #
    //##### TODEL/TODO # printf("<pre>\n");print_r($row);printf("</pre>\n");
    //##### TODEL/TODO #
    preg_match('/SpamAssassin \((.+?)\)/i', $row->spamreport, $sa_rules);
    // Get rid of first match from the array
    $junk = array_shift($sa_rules);
    // Split the array, and get rid of the score and required values
    if (isset($sa_rules[0])) {
        $sa_rules = explode(', ', $sa_rules[0]);
    } else {
        $sa_rules = array();
    }
    $junk = array_shift($sa_rules); // score=
    $junk = array_shift($sa_rules); // required
    foreach ($sa_rules as $rule) {
        // Check if SA scoring is present
        if (preg_match('/^(.+) (.+)$/', $rule, $regs)) {
            $rule = $regs[1];
            $score = $regs[2];
        }
        if (isset($sa_array[$rule]['total'])) {
            $sa_array[$rule]['total']++;
        } else {
            $sa_array[$rule]['total'] = 1;
        }

        if (!isset($sa_array[$rule]['score'])) {
            $sa_array[$rule]['score'] = $score;
        }

        // Initialise the other dimensions of the array
        if (!isset($sa_array[$rule]['spam'])) {
            $sa_array[$rule]['spam'] = 0;
        }
        if (!isset($sa_array[$rule]['not-spam'])) {
            $sa_array[$rule]['not-spam'] = 0;
        }

        if ($row->isspam !== '0') {
            $sa_array[$rule]['spam']++;
        } else {
            $sa_array[$rule]['not-spam']++;
        }
    }
}

reset($sa_array);
arsort($sa_array);
echo '<div class="card card-wr">
            <div class="card-header card-header-warriors">
                <i class="far fa-list-alt"></i>
                '. __('sarulehits37') .'
            </div>
            <div class="card-body m-0 p-3">';

echo '<div class="table-responsive">
         <table  role="table" id="wr-table-vPage" class="dark-table" width="100%" cellspacing="0">'."\n";

echo '<thead>
        <tr class="wr-tabletr">
            <th class="pt-2 pb-2 pl-2 wr-tableheading2">' . __('rule37') . '</th>
            <th class="pt-2 pb-2 pl-2 wr-tableheading2">' . __('desc37') . '</th>
            <th class="pt-2 pb-2 pl-2 wr-tableheading2">' . __('score37') . '</th>
            <th class="pt-2 pb-2 pl-2 wr-tableheading2">' . __('total37') . '</th>
            <th class="pt-2 pb-2 pl-2 wr-tableheading2">' . __('ham37') . '</th>
            <th class="pt-2 pb-2 pl-2 wr-tableheading2">%</th>
            <th class="pt-2 pb-2 pl-2 wr-tableheading2">' . __('spam37') . '</th>
            <th class="pt-2 pb-2 pl-2 wr-tableheading2">%</th>
        </tr>' . "\n";
echo '</thead>
      <tbody>'. "\n";
foreach ($sa_array as $key => $val) {
    echo "
        <tr class=\"wr-tabletr \">
            <td class=\"pl-2\">$key</td>
            <td class=\"pl-2\">" . return_sa_rule_desc(strtoupper($key)) . '</td>
            <td  align="center">' . sprintf('%0.2f', $val['score']) . '</td>
            <td  align="center">' . number_format($val['total']) . '</td>
            <td  align="center">' . number_format($val['not-spam']) . '</td>
            <td  align="center">' . round(($val['not-spam'] / $val['total']) * 100, 1) . '</td>
            <td  align="center">' . number_format($val['spam']) . '</td>
            <td  align="center" class="pr-2">' . round(($val['spam'] / $val['total']) * 100, 1) .'</td>
        </tr>';
}
echo '</tbody>'. "\n";
echo '</table>
</div>' . "\n";

echo '</div></div>';



echo '</div>';
echo '</div>';




// Add footer
html_end_new(); 

// Close any open db connections
dbclose();
