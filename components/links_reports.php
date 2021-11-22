<div class="col-12">
   <div class="card card-wr">
         <div class="card-header card-header-warriors">
            <i class="fas fa-file-code"></i>
            <?php echo __('reports09'); ?>
         </div>
         <div class="card-body card-body-warriors">
            <div class="d-flex flex-wrap justify-content-between">
               <?php 

                  foreach ($this->reports as $report) {
                     $url = $report['url'];
                     if ($report['useToken']) {
                        $url .= '?token=' . $token;
                     }
                     echo '
                        <a class="btn-links-wr" href="' . $url . '"><div class="box-links-wr">' . $report['description'] . '</div></a>
                     ';  
                  }
               ?>
            </div>
         </div>
   </div>
</div>
