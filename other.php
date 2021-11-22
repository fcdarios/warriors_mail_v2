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

// Authentication checking
require __DIR__ . '/login.function.php';

html_head(__('toolslinks10'), 0, false, false);

html_body('other');

echo '<div class="container-fluid">';
   echo '<ol class="breadcrumb my-4 title_page">';
   echo '<li class="breadcrumb-item title_page_li">'.__('toolslinks03').'</li>';
   echo '</ol>';
echo '<div class="row">';
// -----------------------------------------------------
       
echo'<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
      <div class="card card-wr">
          <div class="card-header card-header-warriors">
              <i class="fas fa-tools"></i>
              ' . __('tools10') . '
          </div>
          <div class="card-body card-body-warriors">
          <div class="table-responsive ">
                  <table  role="table" width="100%" id="table-tools" class="table-sm" cellspacing="0" >                       
                       <tbody>';
//TOOLS ************************************************************************************************+      
echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="user_manager.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('usermgnt10') . '</a> </td>
</tr>';
if ($_SESSION['user_type'] === 'A') {
    $virusScanner = get_conf_var('VirusScanners');
    if (preg_match('/sophos/i', $virusScanner)) {
        echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="sophos_status.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('avsophosstatus10') . '</a> </td>
</tr>';
    }
    if (preg_match('/f-secure/i', $virusScanner)) {
        echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="f-secure_status.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('avfsecurestatus10') . '</a> </td>
</tr>';
    }
    if (preg_match('/clam/i', $virusScanner)) {
        echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="clamav_status.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('avclamavstatus10') . '</a> </td>
</tr>';
    }
    if (preg_match('/mcafee/i', $virusScanner)) {
        echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="mcafee_status.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('avmcafeestatus10') . '</a> </td>
</tr>';
    }
    if (preg_match('/f-prot/i', $virusScanner)) {
        echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="f-prot_status.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('avfprotstatus10') . '</a> </td>
</tr>';
    }

    echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="mysql_status.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>'.__('mysqldatabasestatus10').'</a> </td>
</tr>';
    echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="msconfig.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('viewconfms10') . '</a> </td>
</tr>';
    if (defined('MSRE') && MSRE === true) {
        echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="msre_index.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('editmsrules10') . '</a> </td>
</tr>';
    }
    if (!DISTRIBUTED_SETUP && get_conf_truefalse('UseSpamAssassin') === true) {
        echo '
     <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="bayes_info.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>'.__('spamassassinbayesdatabaseinfo10').'</a> </td>
</tr>
     <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="sa_lint.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>SpamAssassin Lint (Test)</a> </td>
</tr>
     <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="ms_lint.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>MailScanner Lint (Test)</a> </td>
</tr>
     <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="sa_rules_update.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('updatesadesc10') . '</a> </td>
</tr>';
    }
    if (!DISTRIBUTED_SETUP && get_conf_truefalse('MCPChecks') === true) {
        echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="mcp_rules_update.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('updatemcpdesc10') . '</a> </td>
</tr>';
    }
    echo ' <tr class="trother" role="row">
         <td role="cell"><a class="btn btn-outline-wr-redbw" href="geoip_update.php"><i class="fa-xs fas fa-chevron-right tool-icon"></i>' . __('updategeoip10') . '</a> </td>
</tr>';
}
echo '    </tbody>
        </table>
      </div>
    </div>
  </div>
</div>';


//LINKS ***********************************************************************************************
if ($_SESSION['user_type'] === 'A') {
    echo '
    <div class="col-md-6">
    <div class="card card-wr">
        <div class="card-header card-header-warriors">
          <i class="fas fa-link"></i>
          ' . __('links10') . '
        </div>
        <div class="card-body card-body-warriors">
        <div class="table-responsive">
            <table  id="table-tools" role="table" width="100%" class="table-sm" cellspacing="0" >                       
                <tbody>    

    <tr class="trother" role="row">
       <td role="cell"><a class="btn btn-outline-wr-redbw" href="http://mailwatch.org"><i class="fa-xs fas fa-chevron-right tool-icon"></i>MailWatch for MailScanner</a></td></tr>
    <tr class="trother" role="row">
       <td role="cell"><a class="btn btn-outline-wr-redbw" href="http://www.mailscanner.info"><i class="fa-xs fas fa-chevron-right tool-icon"></i>MailScanner</a></td></tr>';

    if (true === get_conf_truefalse('UseSpamAssassin')) {
        echo '<tr class="trother" role="row">
       <td role="cell"><a class="btn btn-outline-wr-redbw" href="http://spamassassin.apache.org/"><i class="fa-xs fas fa-chevron-right tool-icon"></i>SpamAssassin</a></td></tr>';
    }

    if (preg_match('/sophos/i', $virusScanner)) {
        echo '<tr class="trother" role="row">
       <td role="cell"><a class="btn btn-outline-wr-redbw" href="http://www.sophos.com"><i class="fa-xs fas fa-chevron-right tool-icon"></i>Sophos</a></td></tr>';
    }

    if (preg_match('/clam/i', $virusScanner)) {
        echo '<tr class="trother" role="row">
       <td role="cell"><a class="btn btn-outline-wr-redbw" href="http://clamav.net"><i class="fa-xs fas fa-chevron-right tool-icon"></i>Antivirus</a></td></tr>';
    }

    echo '
    <tr class="trother" role="row">
       <td role="cell"><a class="btn btn-outline-wr-redbw" href="http://www.dnsstuff.com"><i class="fa-xs fas fa-chevron-right tool-icon"></i>DNSstuff</a></td></tr>
    <tr class="trother" role="row">
       <td role="cell"><a class="btn btn-outline-wr-redbw" href="http://mxtoolbox.com/NetworkTools.aspx"><i class="fa-xs fas fa-chevron-right tool-icon"></i>MXToolbox Network Tools</a></td></tr>
    <tr class="trother" role="row">
       <td role="cell"><a class="btn btn-outline-wr-redbw" href="http://www.anti-abuse.org/multi-rbl-check/"><i class="fa-xs fas fa-chevron-right tool-icon"></i>Multi-RBL Check</a></td></tr>
    ';

    echo ' </tbody>
         </table>
       </div>
     </div>
   </div>
 </div>';
}


// ----------------------------------------

echo '</div>';
echo '</div>';

// Add footer
html_end_new(); 

// Close any open db connections
dbclose();
