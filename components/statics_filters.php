<div class="col-lg-6">
   <div class="card card-wr">
         <div class="card-header card-header-warriors">
            <i class="far fa-window-maximize"></i>
            <?php echo __('stats09'); ?>
         </div>
         <div class="card-body card-body-warriors">
            <div class="table-responsive">
               <table class="table table-bordered table-warriors" width="100%" cellspacing="0">
                     <tbody class="table-body-warriors">
                        <?php

                           while ($row = $sth->fetch_object()) {
                              echo ' <tr><td>' . __('oldrecord09') . '</td><td align="right">' . $row->oldest . '</td></tr>' . "\n";
                              echo ' <tr><td>' . __('newrecord09') . '</td><td align="right">' . $row->newest . '</td></tr>' . "\n";
                              echo ' <tr><td>' . __('messagecount09') . '</td><td align="right">' . number_format($row->messages) . '</td></tr>' . "\n";
                           }

                        ?>
                     </tbody>
               </table>
            </div>
         </div>
   </div>
</div>