<div class="col-lg-6">
   <div class="card card-wr">
         <div class="card-header card-header-warriors">
            <i class="fas fa-filter"></i>
            <?php echo __('activefilters09'); ?>
         </div>
         <div class="card-body card-body-warriors">
            <div class="table-responsive table-warriors-div">
            <?php if(count($this->item) > 0 ): ?>
               <table class="table table-striped table-bordered table-warriors" style="width:100%" id="table_active_filters">
                  <thead class="table-head-warriors">
                     <tr>
                        <th>Filter</th>
                        <th>Value</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody class="table-body-warriors">
                  <?php 
                     foreach ($this->item as $key => $val) {
                        echo '<tr>';
                           echo '
                           <td>'.$this->TranslateColumn($val[0]) . ' ' . $this->TranslateOperator($val[1]).'</td>
                           <td>'.stripslashes($val[2]).'</td>
                           <td class="text-center"><a class="btn btn-outline-wr-red font-weight-bold btn-sm" href="' . sanitizeInput($_SERVER['PHP_SELF']) . '?token=' . $_SESSION['token'] . '&amp;action=remove&amp;column=' . $key . '">' . __('remove09') . '</a></td>
                           ';
                        echo '</tr>';
                     }
                  ?>
                  </tbody>
               </table>
            
            <?php else: 
               echo '<tr><td colspan="2">' . __('none09') . '</td></tr>' . "\n";   
             endif ?>
             </div>
         </div>
   </div>
</div>
