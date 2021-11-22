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

set_time_limit(60);
require_once("./functions.php");
require('login.function.php');
require_once __DIR__ . '/filter.inc.php';

$refresh = html_head("info",0,false,true);
html_body('dashboard');

$filter = new Filter();


echo '<link href="public/lib/Chart.js/Chart.min.css" rel="stylesheet">';
echo '<script src="public/lib/Chart.js/Chart.min.js"></script>';
echo '<script src="public/lib/Chart.js/chartjs-datalabels.min.js"></script>';
echo '<script src="public/js/charts_dashboard.js"></script>' . "\n";

echo '<div class="container-fluid">';
   echo '<ol class="breadcrumb my-4 title_page">';
      echo '<li class="breadcrumb-item title_page_li"><a href="dashboard.php">'.__('dashboard').'</a></li>';
   echo '</ol>';
echo '<div class="row">';

// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- //

?>

<div class="col-12">
   <div class="card card-wr" style="min-height: 180px;">
        <div class="card-header card-header-warriors">
            <i class="fas fa-check-double"></i>
            <?php echo __('todaystotals03'); ?>
        </div>
        <div class="card-body card-body-warriors ">
            <?php 
                printTodayStatistics_data() 
            ?>
        </div>
    </div>
</div>

<?php if ($_SESSION['user_type'] === 'A'): ?>
<div class="col-sm-6">
   <div class="card card-wr" style="min-height: 320px;">
      <div class="card-header card-header-warriors">
         <i class="fas fa-server"></i>
         <?php echo __('dashserver66'); ?>
      </div>
      <div id="idBody" class="card-body card-body-warriors pt-0">
         <div class="d-flex flex-column ">

            <div class="d-flex flex-wrap justify-content-center">
               <div class="mx-md-2 my-sm-0 my-2" style="min-width: 100px; width: 180px; height: 100%;">
                  <div class="d-flex flex-column chart_cpu_ram">
                     <div id="chart_ram_text" class="chart_cpu_ram_text"></div>
                     <canvas id="chart_ram" class="chartjs-render-monitor"></canvas>
                  </div>
               </div>

               <div class="mx-md-2 my-sm-0 my-2" style="min-width: 100px; width: 180px; height: 100%;">
                  <div class="d-flex flex-column chart_cpu_ram">
                     <div id="chart_cpu_text" class="chart_cpu_ram_text"></div>
                     <canvas id="chart_cpu" class="chartjs-render-monitor"></canvas>
                  </div>
               </div>
            </div>

            <div class="d-flex flex-wrap w-100 pt-4 px-4">
               <table class="table table_warriors_info">
                  <tbody>                 
                     <tr>
                        <td><?php echo __('dashtotalram66'); ?></td>
                        <td id="chart_ram_total_text"></td>
                     </tr>
                     <tr>
                        <td><?php echo __('dashusedram66'); ?></td>
                        <td id="chart_ram_used_text"></td>
                     </tr>
                     <tr>
                        <td><?php echo __('dashavailableram66'); ?></td>
                        <td id="chart_ram_available_text"></td>
                     </tr>
                  </tbody>
               </table>
               
            </div>
         </div>
      </div>
   </div>
</div>
<?php endif; ?>


<?php if ($_SESSION['user_type'] === 'A' || $_SESSION['user_type'] === 'D'): ?>
<div class="col-sm-6">
   <div class="card card-wr" style="min-height: 320px;">
         <div class="card-header card-header-warriors">
            <i class="fas fa-desktop"></i>
            <?php echo __('status03'); ?>
         </div>
         <div class="card-body card-body-warriors">
            <table class="table table_warriors_info">
               <tbody>                 
               <?php 
                  printServiceStatus_v2();
                  printAverageLoad_v2();
                  print_uptime();
               ?>
              </tbody>
            </table>
         </div>
   </div>
</div>
<?php endif; ?>

<?php if ($_SESSION['user_type'] === 'A'): ?>
<!-- Disk -->
<div class="col-xl-4 col-sm-5">
   <div class="card card-wr" id="card_diskspace">   
        <div class="card-header card-header-warriors">
           <i class="fas fa-compact-disc"></i>
           <?php echo __('freedspace03'); ?>
        </div>
        <div class="card-body card-body-warriors pt-0">
            <?php 
                printFreeDiskSpace_v2(); 
            ?>
        </div>
  </div>
</div>
<?php endif; ?>

<?php if ($_SESSION['user_type'] === 'A'): ?>
<div class="col-xl-8 col-sm-7">
    <div class="card card-wr" id="card_trafficgraph">   
        <div class="card-header card-header-warriors">
            <i class="fas fa-chart-area"></i>
            <?php echo __('trafficgraph03'); ?>
        </div>
        <div class="card-body py-2 px-4">        
            <?php 
                printTrafficGraph_v2();
            ?>            
        </div>
    </div>
</div>
<?php endif; ?>
<?php if ($_SESSION['user_type'] === 'D'): ?>
<div class="col-xl-6 col-sm-6">
    <div class="card card-wr" style="height: 320px;">   
        <div class="card-header card-header-warriors">
            <i class="fas fa-chart-area"></i>
            <?php echo __('trafficgraph03'); ?>
        </div>
        <div class="card-body py-2 px-4">        
            <?php 
                printTrafficGraph_v2();
            ?>            
        </div>
    </div>
</div>
<?php endif; ?>


<?php if ($_SESSION['user_type'] === 'A'): ?>
   <div class="col-xl-4 col-sm-5">
      <div class="card card-wr">
            <div class="card-header card-header-warriors">
               <i class="fas fa-inbox"></i>
               <?php echo __('mailqueue03'); ?>
            </div>
            <div class="card-body py-2 px-4" >
               <table class="table table_warriors_info" >
                  <tbody>                 
                  <?php 
                     printMTAQueue_data();
                  ?>
                  </tbody>
               </table>
            </div>
      </div>
   </div>
<?php endif; ?>


<?php if ($_SESSION['user_type'] === 'D'): ?>
   <div class="col-12">
<?php else: ?>
   <div class="col-xl-8 col-sm-7">
<?php endif; ?>
   <div class="card card-wr" style="height: 350px;">
      <div class="card-header card-header-warriors">
         <i class="far fa-calendar"></i>
         <?php echo __('mailweekgraph03');?>
      </div>
      <div class="card-body card-body-warriors">
         <div class="d-flex flex-column align-items-center h-100" id="graph_mails_last_week_body">
            <div id="load_graph_mails_last_week" class="spinner-border text-danger"  style="width: 4rem; height: 4rem;" role="status">
                  <span class="sr-only">Loading...</span>
            </div>
            <canvas id="graph_mails_last_week" name="graph_mails_last_week" style="display:none;" class="chartjs-render-monitor"></canvas>
         </div>
      </div>
   </div>
</div>


<div class="col-12 my-0 py-0">
   <ol class="breadcrumb title_page mt-4 mb-2">
         <li class="breadcrumb-item title_page_li my-0"><a href="reports.php"><?php echo __('reportsmonth66'); ?></a></li>
   </ol>
</div>

<?php

   //Nombre de la gráfica , IdGrafica , URl de la grafica
   print_pie_chart_card(__('top10mailrelays39'),'print_graph_rep_top_mail_relays',
   'rep_top_mail_relays.php');
   print_pie_chart_card(__('top10virus48'),'print_graph_rep_top_viruses',
   'rep_top_viruses.php');
   print_pie_chart_card(__('top10sendersvol47'),'print_graph_rep_top_senders_by_volumen',
   'rep_top_senders_by_volume.php');
   print_pie_chart_card(__('top10sendersqt46'),'print_graph_rep_top_senders_by_quantity',
   'rep_top_senders_by_quantity.php');
   print_pie_chart_card(__('top10recipqt42'),'print_graph_rep_top_recipients_by_quantity',
   'rep_top_recipients_by_quantity.php');
   print_pie_chart_card(__('top10recipvol43'),'print_graph_rep_top_recipients_by_volumen',
   'rep_top_recipients_by_volume.php');
   print_pie_chart_card(__('top10senderdomqt44'),'print_graph_rep_top_senders_domain_by_quantity',
   'rep_top_sender_domains_by_quantity.php');
   print_pie_chart_card(__('top10senderdomvol45'),'print_graph_rep_top_senders_domain_by_volumen',
   'rep_top_sender_domains_by_volume.php');
   print_pie_chart_card(__('top10recipdomqt40'),'print_graph_rep_top_recipients_domain_by_quantity',
   'rep_top_recipient_domains_by_quantity.php');
   print_pie_chart_card(__('top10recipdomvol41'),'print_graph_rep_top_recipients_domain_by_volumen',
   'rep_top_recipient_domains_by_volume.php');

// ------------------------------------------
echo '</div>';
echo '</div>';

?>
<script>
   async_charts();
</script>

<?php
// Add footer
html_end_new(); 

// Close any open db connections
dbclose();

