<div class="col-lg-6">
   <div class="card card-wr">
         <div class="card-header card-header-warriors">
            <i class="fas fa-stream"></i>
            <? echo __('loadsavef09');   ?>
         </div>
         <div class="card-body card-body-warriors">
            <form method="post" action="<? echo sanitizeInput($_SERVER['PHP_SELF']); ?> ">
               <div class="row">
                     <div class="col-sm-9">
                        <div class="form-group">
                           <label class="small mb-1 w-label" for="input_save_as"><?php echo __('saveas09'); ?>:</label>
                           <input class="form-control w-input-text" type="text" size="50" name="save_as" placeholder="Save as"/>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group">
                           <label class="small mb-1 w-label"></label>
                           <button class="btn btn-block btn-outline-wr-red font-weight-bold" type="submit" name="action" value="save">
                                 <i class="fas fa-save"></i>
                                 <?php echo __('save09'); ?>
                           </button>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group">
                           <select class="form-control custom-select w-input-text" name="filter">
                              <?php echo $this->ListSaved(); ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group d-flex align-items-center">
                           <button class="btn  btn-block btn-wr-red font-weight-bold btn-sm" type="submit" name="action" value="load" >
                              <i class="fas fa-undo"></i>
                              <?php echo __('load09'); ?>
                           </button>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group d-flex align-items-center">
                           <button class="btn  btn-block btn-wr-gray font-weight-bold btn-sm" type="submit" name="action" value="save">
                              <i class="fas fa-save"></i>
                              <?php echo __('save09'); ?>
                           </button>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group d-flex align-items-center">
                           <button class="btn  btn-block btn-wr-black font-weight-bold btn-sm" type="submit" name="action" value="delete">
                                 <i class="fas fa-trash-alt"></i>
                                 <?php echo __('delete09'); ?>
                           </button>
                        </div>
                     </div>
                     <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" >
                     <input type="hidden" name="formtoken" value="<?php echo generateFormToken('/filter.inc.php form token') ?>" >
               </div>
            </form>
         </div>
   </div>
</div>