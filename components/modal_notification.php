<form name="actions-add" action="do_message_actions_bw.php" method="POST">
   <input type="hidden" name="token-modal" value="<?php echo $_SESSION['token']; ?>">
   <input type="hidden" name="formtoken-modal" VALUE="<?php echo generateFormToken('/do_message_actions_bw.php form token'); ?>">
          
   <div class="modal modal-wr fade" id="msg_notification_modal" tabindex="-1" role="dialog" aria-labelledby="msg_notification_modal_label" aria-hidden="true">
      <div class="modal-dialog modal-dialog-wr" role="document">
         <div class="modal-content modal-content-wr">
               <div class="modal-header">
                  <h5 class="modal-title" id="msg_notification_modal_title"><?php echo __('addwlbl07'); ?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body modal-body-wr" id="msg_notification_modal_body">

                     <div id="modal-item-select-body">

                     <div id="modal_message_bw_id" class="col-12 tx-11 h-25">
                        <h6>No emails selected</h6>
                     </div>

                     <div id="modal_add_bw_id" class="col-12 tx-11">
                        <b>Add to:</b><br>	
                        <div class="row pl-md-4 pl-4 pb-2 ">
                           <div class="col-xl-5 col-md-5 col-sm-6 col-6">
                              <input type="radio" value="w" name="list" class="form-check-input" id="radio-white" 
                              <?php echo $w; ?> />
                              <div></div>
                              <label class="form-check-label" for="radio-white">
                              <?php echo __('wl07'); ?>
                              </label>
                           </div>
                           <div class="col-xl-5 col-md-5 col-sm-6 col-6">
                              <input type="radio" value="b" name="list" class="form-check-input" id="radio-black" 
                              <?php echo $b; ?> />
                              <label class="form-check-label" for="radio-black">
                              <?php echo __('bl07'); ?>
                              </label>
                           </div>       
                        </div>
                        <hr>
                     </div>

                    

                     <div id="modal_option_bw_id" class="col-12 tx-11">
                        <b>Options:</b><br>	
                        <input type="checkbox" name="sender_list_email" id="sender_list_email_id" value="1"> Sender Email Address (user@domain.com)<br>
                        <input type="checkbox" name="sender_list_domain" id="sender_list_domain_id" value="1"> Sender Domain Emails (*@domain.com)<br>
                        <input type="checkbox" name="recipient_list_email" id="recipient_list_email_id" value="1"> Recipient Email Address  (user@domain.com)<br>
                        <input type="checkbox" name="recipient_list_domain" id="recipient_list_domain_id" value="1"> Recipient Domain Email (*@domain.com)<br>
                        <hr>
                     </div>


                     <div id="modal_table_bw_id" class="col-12">
                        <div class="table-responsive table-overflow table-res-wr">
                           <table width="100%" class="table table-sm table-striped table-wr">
                              <thead>
                                 <tr>
                                    <th>-</th>
                                    <th> Sender Email Address </th>
                                    <th> Sender Domain Emails </th>
                                    <th> Recipient Email Address </th>
                                    <th> Recipient Domain Emails </th>
                                 </tr>
                              </thead>
                              <tbody id="table-body-warriors-rep_id">
                              </tbody>
                           </table>
                        </div>
                     </div>
               </div>
               <div class="modal-footer">
               <button type="submit" name="btn_checks" id="bw_accept_action" class="btn btn-sm btn-wr-red">Aceptar</button>
               <button type="button"  data-dismiss="modal" class="btn btn-sm btn-wr-black">Cancelar</button>
         </div>
         </div>
      </div>
   </div>

</form>

