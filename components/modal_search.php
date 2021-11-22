<div class="modal modal-wr fade" id="modal_search" tabindex="-1" role="dialog" aria-labelledby="modal_search_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-wr" role="document">
        <div class="modal-content modal-content-wr">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_search_title"><?php echo __('jumpmessage03'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-body-wr" id="modal_search_body">
                <div class="d-flex flex-column">
                    <div class="d-flex flex-row justify-content-center">
                        <input id="input_search_modal" name="input_search_modal" placeholder="<?php echo __('modal_search_input68'); ?>" class="w-50 form-control form-control-search-wr form-control-search-wr-moodal" >
                        <button type="submit" onclick="search_messages( input_search_modal )" class="btn btn-warrior-search"><i class="fas fa-search"></i></button>
                    </div>

                    <hr>
                    <div id="div-table-search">
                        <div id="body-search"><?php echo __('modal_search_msg68'); ?></div>
                        <div id="body-loading-results" style="display: none;">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div id="body-msg-results" style="display: none;"><?php echo __('modal_no_results68'); ?></div>
                        <div id="body-table-results" class="table-responsive table-res-wr" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

