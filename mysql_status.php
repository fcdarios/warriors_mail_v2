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

html_head(__('mysqlstatus31'), 0, false, false);

html_body('other');

echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li"><a href="other.php">'.__('toolslinks03').'</a></li>';
echo '<li class="breadcrumb-item title_page_li active">'.__('mysqlstatus31').'</li>';
echo '</ol>';
echo ' <div class="d-flex flex-column">';
if ($_SESSION['user_type'] !== 'A') {
    echo __('notauthorized31') . '\n';
} else {
    audit_log(__('auditlog31', true));

    echo '<div class="m-2"> </div>';
        echo '<div class="card card-wr">
                <div class="card-header card-header-warriors">
                    <i class="far fa-list-alt pr-2"></i>';
        echo __('mysqltablestatus67');            
        echo'   </div>
                <div class="card-body card-body-warriors">';
    dbtable_v2('SHOW TABLE STATUS', null, false, false,'wr-table-tstatus');
    echo '</div></div>';
    echo '<div class="m-2"> </div>';
        echo '<div class="card card-wr">
                <div class="card-header card-header-warriors">
                    <i class="far fa-list-alt pr-2"></i>';
        echo __('mysqlprocesslist67');            
        echo'   </div>
                <div class="card-body card-body-warriors">';    
    dbtable_v2('SHOW FULL PROCESSLIST', null, false, false,'wr-table-processlist');
    echo '</div></div>';
    echo '<div class="m-2"> </div>';  
        echo '<div class="card card-wr">
                <div class="card-header card-header-warriors">
                    <i class="far fa-list-alt pr-2"></i>';
        echo __('mysqlvariables67');            
        echo'   </div>
                <div class="card-body card-body-warriors">';
    dbtable_v2('SHOW VARIABLES', null, false, false,'wr-table-variables');
    echo '</div></div>';
    echo '<div class="m-2"> </div>';  


    echo '</div>';
    echo '</div>';
    
    // Add footer
    html_end_new(); 
    
    // Close any open db connections
    dbclose();
}
