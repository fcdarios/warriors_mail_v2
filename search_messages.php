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

require_once __DIR__ . '/filter.inc.php';
require_once __DIR__ . '/functions.php';

require __DIR__ . '/login.function.php';

$error = false;
if ( !isset($_POST['token']) && !$_SESSION['token'] == deepSanitizeInput($_POST['token'], 'url')) {
   $error = true;
}
if( !isset( $_POST['search_query'] ) || $_POST['search_query'] === ''){
   $error = true;
}
$data = array();
$data['error'] = $error;
$data['length'] = 0;
if ( !$error ) {
   
   $query = $_POST['search_query'];

   $sql = "
   SELECT
   id,
   DATE_FORMAT(timestamp, '" . DATE_FORMAT . ' ' . TIME_FORMAT . "') AS datetime,
   from_address,
   to_address,
   subject,
   size as size,
   '' AS status
   FROM
   maillog
   WHERE
   ".$_SESSION['global_filter']."
   AND (
      id LIKE '%".$query."%'
      OR from_address LIKE '%".$query."%'
      OR to_address LIKE '%".$query."%'
   )
   ORDER BY
   date DESC,
   time DESC
   LIMIT 1000
   ";

   $result = dbquery($sql);
   $data['list_messages'] = $result->fetch_all();
   $data['length'] = sizeof($data['list_messages']);
   $data['columns'][0] = __('datetime03');
   $data['columns'][1] = __('from03');
   $data['columns'][2] = __('to03');
   $data['columns'][3] = __('subject03');
 }


// Close any open db connections
dbclose();
echo json_encode($data, true);

?>