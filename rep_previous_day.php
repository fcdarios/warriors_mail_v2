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
// Authentication checking
require __DIR__ . '/login.function.php';
require_once __DIR__ . '/graphgenerator.inc.php';

$filter = html_head(__('totalmaillasthours36'), 0, false, true);
html_body('reports');

   // Check if MCP is enabled
   $is_MCP_enabled = get_conf_truefalse('mcpchecks');
   // Set Date format
   $date_format = "'" . DATE_FORMAT . "'";

    $graphgenerator = new GraphGenerator_v2();
    $graphgenerator->sqlQuery = "
    SELECT
    timestamp AS xaxis,
    1 as total_mail,
    virusinfected AS total_virus,
    isspam AS total_spam,
    size AS total_size
    FROM
    maillog
    WHERE
    1=1
    AND
    timestamp BETWEEN (NOW() - INTERVAL 24 HOUR) AND NOW()
    " . $filter->CreateSQL() . '
    ORDER BY
    timestamp DESC
    ';
    $graphgenerator->tableColumns = array(
        'time' => __('hours36'),
        'total_mailconv' => __('mailcount36'),
        'total_virusconv' => __('viruscount36'),
        'total_spamconv' => __('spamcount36'),
        'total_sizeconvconv' => __('size36'),
    );
    $graphgenerator->sqlColumns = array(
        'xaxis',
        'total_mail',
        'total_size',
        'total_virus',
        'total_spam',
    );
    $graphgenerator->valueConversion = array(
        'xaxis' => 'generatetimescale',
        'total_size' => 'timescale',
        'total_sizeconv' => 'scale', //do not change this order
        'total_mail' => 'timescale',
        'total_virus' => 'timescale',
        'total_spam' => 'timescale',
    );
    $graphgenerator->graphColumns = array(
        'labelColumn' => 'time',
        'dataLabels' => array(
            array(__('barmail36'), __('barvirus36'), __('barspam36')),
            array(__('volume36')),
        ),
        'dataNumericColumns' => array(
            array('total_mailconv', 'total_virusconv', 'total_spamconv'),
            array('total_sizeconv')
        ),
        'dataFormattedColumns' => array(
            array('total_mailconv', 'total_virusconv', 'total_spamconv'),
            array('total_sizeconvconv')
        ),
        'xAxeDescription' => __('hours36'),
        'yAxeDescriptions' => array(
            __('nomessages36'),
            __('volume36')
        ),
        'fillBelowLine' => array('false', 'true')
    );
    $graphgenerator->types = array(
        array('bar', 'bar', 'bar'),
        array('line')
    );
    $graphgenerator->graphTitle = __('totalmaillasthours36');
    $graphgenerator->settings['timeInterval'] = 'P1D';
    $graphgenerator->settings['timeScale'] = 'PT1H';
    $graphgenerator->settings['timeGroupFormat'] = 'Y-m-dTH:00:00';
    $graphgenerator->settings['timeFormat'] = 'H:00';
    $graphgenerator->settings['maxTicks'] = '12';
    $graphgenerator->settings['valueTypes'] = array('plain', 'volume');
    $graphgenerator->printTable = false;
    
echo '<div class="container-fluid">';
   echo '<ol class="breadcrumb my-4 title_page">';
   echo '<li class="breadcrumb-item title_page_li"><a href="reports.php">'.__('reports03').'</a></li>';
   echo '<li class="breadcrumb-item title_page_li active">'.__('totalmaillasthours36').'</li>';
   echo '</ol>';
echo '<div class="row">';


?>

<div class="col-12">
    <div class="card card-wr">
        <div class="card-header card-header-warriors">
            <i class="fas fa-compact-disc"></i>
            <?php echo __('trafficgraph03'); ?>
        </div>
        <div class="card-body card-body-warriors" style="height: 400px;">
            <?php 
                $graphgenerator->printLineGraph();
            ?>            
        </div>
    </div>
</div>

<?php 
    $graphgenerator->printTable_v2();
?> 

    
<?php
echo '</div>';
echo '</div>';


// Add footer
html_end_new(); 

// Close any open db connections
dbclose();

