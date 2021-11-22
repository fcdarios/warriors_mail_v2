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

header('Location: status.php');

// // Include of necessary functions
// require_once __DIR__ . '/functions.php';

// // Authentication checking
// require __DIR__ . '/login.function.php';

// if ($_SESSION['user_type'] !== 'A') {
//     header('Location: index.php');
//     audit_log(__('auditlog11', true));
// } else {

//     html_head(__('mwandmsversion11'), 0, false, false);
//     html_body('sf_version');
//     echo '<div class="container-fluid">';
//     echo '<ol class="breadcrumb my-4 title_page">';
//     echo '<li class="breadcrumb-item title_page_li">'.__('softwareversions03').'</li>';
//     echo '</ol>';
//     echo '<div class="row">';
//     // -------------------------------


//     $mailwatch_version = mailwatch_version();
//     $mailscanner_version = get_conf_var('MailScannerVersionNumber');
//     $php_version = PHP_VERSION;
//     $mysql_version = database::mysqli_result(dbquery('SELECT VERSION()'), 0);
//     $geoip_version = false;
//     $geoip_database_file = __DIR__ . '/temp/GeoLite2-Country.mmdb';
//     if (file_exists($geoip_database_file)) {
//         require_once __DIR__ . '/lib/maxmind-db/reader/autoload.php';
//         $geoIpDbReader = new \MaxMind\Db\Reader($geoip_database_file);
//         $GeoIPDbMetadata = $geoIpDbReader->metadata();
//         $geoip_version = (isset($GeoIPDbMetadata->description['en']) ? $GeoIPDbMetadata->description['en'] : '') . ' ' . date('Y-m-d H:i:s', $GeoIPDbMetadata->buildEpoch);
//         $geoip_version = trim($geoip_version);
//     }

//     echo '<div class="col-xl-6">';
//         echo '<div class="card mb-4">';
//             echo '<div class="card-header card-header-warriors">';
//                 echo '<i class="far fa-window-maximize"></i> ';
//                 echo __('softver11');
//             echo '</div>';
//             echo '<div class="card-body">';
//                 echo '<table class="table table-warriors" width="100%" cellspacing="0">';
//                     echo '<tbody>';
//                         echo '<tr>';
//                             echo '<td>MailWatch ' . __('version11') . ' ' . $mailwatch_version . '</td>';
//                         echo '</tr>';
//                         echo '<tr>';
//                             echo '<td>';
//                             // Add test for OS
//                                 if (0 === stripos(PHP_OS, 'linux')) {
//                                     $vars = array();
//                                     $files = glob('/etc/*-release');
//                                     foreach ($files as $file) {
//                                         $lines = array_filter(array_map(function ($line) {
//                                             $parts = explode('=', $line);
//                                             if (count($parts) !== 2) {
//                                                 return false;
//                                             }
//                                             $parts[1] = str_replace(array('"', "'"), '', $parts[1]);
//                                             $parts[1] = trim($parts[1]);
//                                             return $parts;
//                                         }, file($file)));
//                                         foreach ($lines as $line) {
//                                             $vars[$line[0]] = $line[1];
//                                         }
//                                     }
//                                     if (isset($vars['ID']) && in_array(strtolower($vars['ID']), array('centos', 'debian'), true)) {
//                                         echo __('systemos11') . ' ' . $vars['PRETTY_NAME'];
//                                     }
//                                     if (isset($vars['ID']) && strtolower($vars['ID']) === 'ubuntu') {
//                                         echo __('systemos11') . ' ' . $vars['NAME'] . ' ' . $vars['VERSION'];
//                                     }
//                                 }
//                                 if (strtolower(PHP_OS) === 'freebsd') {
//                                     echo __('systemos11') . ' ' . PHP_OS . ' ' . php_uname('r') . ' ' . php_uname('m');
//                                 }
//                             echo '</td>';
//                         echo '</tr>';
//                         echo '<tr>';
//                             echo '<td>';
//                             // Add test for MTA
//                                 $mta = get_conf_var('mta');
//                                 if (get_conf_var('MTA', true) === 'postfix' || get_conf_var('MTA', true) === 'msmail') {
//                                     echo 'Postfix ' . __('version11') . ' ';
//                                     exec('which postconf', $postconf);
//                                     if (isset($postconf[0])) {
//                                         passthru("$postconf[0] -d | grep 'mail_version =' | cut -d' ' -f3");
//                                     } else {
//                                         echo 'postconf ' . __('notfound06');
//                                     }
//                                 }
//                                 if (get_conf_var('MTA', true) === 'exim') {
//                                     echo 'Exim ' . __('version11') . ' ';
//                                     exec('which exim', $exim);
//                                     if (isset($exim[0])) {
//                                         passthru("$exim[0] -bV | grep 'Exim version' | cut -d' ' -f3");
//                                     } else {
//                                         echo 'exim ' . __('notfound06');
//                                     }
//                                 }
//                                 if (get_conf_var('MTA', true) === 'sendmail') {
//                                     echo 'Sendmail ' . __('version11') . ' ';
//                                     exec('which sendmail', $sendmail);
//                                     if (isset($sendmail[0])) {
//                                         passthru("$sendmail[0] -d0.4 -bv root | grep 'Version' | cut -d' ' -f2");
//                                     } else {
//                                         echo 'sendmail ' . __('notfound06');
//                                     }
//                                 }
//                             echo '</td>';
//                         echo '</tr>';
//                         echo '<tr>';
//                             echo '<td>MailWatch ' . __('version11') . ' ' . $mailwatch_version . '</td>';
//                         echo '</tr>';
//                         echo '<tr>';
//                             echo '<td>MailScanner ' . __('version11') . ' ' . $mailscanner_version . '</td>';
//                         echo '</tr>';
//                         echo '<tr>';
//                             echo '<td>';
//                                 $virusScanner = get_conf_var('VirusScanners');
//                                 // Add test for other virus scanners.
//                                 if (false !== stripos($virusScanner, 'clam')) {
//                                     echo 'Antivirus ' . __('version11') . ' ';
//                                     exec("which clamscan", $clamscan);
//                                     if (isset($clamscan[0])) {
//                                         passthru("$clamscan[0] -V | cut -d/ -f1 | cut -d' ' -f2");
//                                     }
//                                 }
//                             echo '</td>';
//                         echo '</tr>';
//                         echo '<tr>';
//                             echo '<td>';
//                                 echo 'SpamAssassin ' . __('version11') . ' ';
//                                 passthru(SA_DIR . "spamassassin -V | tr '\\\n' ' ' | cut -d' ' -f3");
//                             echo '</td>';                                
//                         echo '</tr>';
//                         echo '<tr>';
//                             echo '<td>PHP ' . __('version11') . ' ' . $php_version . '</td>';
//                         echo '</tr>';
//                         echo '<tr>';
//                             echo '<td>MySQL ' . __('version11') . ' ' . $mysql_version . '</td>';
//                         echo '</tr>';
//                         echo '<tr>';
//                             echo '<td>GeoIP Database ' . __('version11').' ';
//                             if (false !== $geoip_version) {
//                                 echo $geoip_version;
//                             } else {
//                                 echo __('nodbdown11') . ' ';
//                             }
//                             echo '</td>';
//                         echo '</tr>';
//                     echo '</tbody>';
//                 echo '</table>';
//             echo '</div>';
//         echo '</div>';
//     echo '</div>';
    
//     // -------------------------------
//     echo '</div>';
//     echo '</div>';
//     // Add footer
//     html_end_new(); 

//     // Close any open db connections
//     dbclose();

// }
