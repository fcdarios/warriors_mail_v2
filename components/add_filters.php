<div class="col-lg-6">
   <div class="card card-wr">
         <div class="card-header card-header-warriors">
            <i class="fas fa-plus mr-1"></i>
            <? echo __('addfilter09');   ?>
         </div>
         <div class="card-body card-body-warriors">
            <form method="post" action="<? echo sanitizeInput($_SERVER['PHP_SELF']); ?> ">
               <div class="row">
                     <div class="col-sm-6">
                     <!-- Columns -->
                        <div class="form-group">
                           <label class="small mb-1 w-label" for="input-type"><?php echo __('type04');?>:</label>
                           <select class="form-control custom-select w-input-text" id="input-type" name="column">
                                 <?php

                                    foreach ($this->columns as $key => $val) {
                                       echo '<option value="' . $key . '"';
                                       //  Use the last value as the default
                                       if ($this->display_last && $key === $this->last_column) {
                                          echo ' SELECTED';
                                       }
                                       echo '>' . $val . '</option>' . "\n";
                                    }
                                 ?>
                           </select>
                        </div>
                     </div>
                     <!-- Operators -->
                     <div class="col-sm-6">
                        <div class="form-group">
                           <label class="small mb-1 w-label" for="input-type"></label>
                           <select class="form-control custom-select w-input-text" id="input-type" name="operator">
                           <?php

                              foreach ($this->operators as $key => $val) {
                                 echo '<option value="' . $key . '"';
                                 //  Use the last value as the default
                                 if ($this->display_last && $key === $this->last_column) {
                                    echo ' SELECTED';
                                 }
                                 echo '>' . $val . '</option>' . "\n";
                              }

                           ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-9">
                        <div class="form-group">
                           <label class="small mb-1 w-label" for="input-type"><?php echo __('tosetdate09'); ?></label>
                           <input 
                              class="form-control w-input-text" 
                              type="text" 
                              size="50" 
                              name="value" 
                              placeholder="Value" 
                              <?php
                                 if ($this->display_last) {
                                    //  Use the last value as the default
                                    echo ' value="' . htmlentities(stripslashes($this->last_value)) . '"';
                                 }
                              ?>
                           />
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group align-items-center">
                           <label class="small mb-1 w-label" for="input-type"></label>
                           <button class="pt-2 btn btn-block btn-wr-red font-weight-bold btn-sm" type="submit" name="action" value="add">
                                 <i class="fas fa-plus mr-1"></i>
                                 <?php echo __('add09');?>
                           </button>
                        </div>
                     </div>
                  <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                  <input type="hidden" name="formtoken" value="<?php echo generateFormToken('/filter.inc.php form token'); ?>">
               </div>
            </form>
         </div>
   </div>
</div>