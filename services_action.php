<?php 


require_once __DIR__ . '/functions.php';
require __DIR__ . '/login.function.php';

if ( !isset( $_POST['action'] ) || !$_SESSION['user_type'] === 'A' || !isset( $_POST['token'] ) ) {
    header('Location: services.php');
}
if ( $_POST['token'] != $_SESSION['token'] ) {
    header('Location: services.php');
}


$action = $_POST['action'];
$token = $_POST['token'];


html_head(__('services_action'), 0, false, false);
html_body('services');

echo '<script src="public/js/service_actions.js"></script>';
echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li">' . __('services03') . '</li>';
echo '</ol>';
echo '<div class="row">';
?>


<div id="loading_service" class="col-12">
    <div class="d-flex justify-content-center">
        <div class="spinner-border spinner_loading" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>

<div id="card_service" class="col-12" style="display: none;">
    <div class="card card-wr">
        <div class="card-header card-header-warriors">
            <div class="d-flex flex-row justify-content-between">
                <span>
                    <i class="fas fa-terminal"></i>
                <?php if ( $action == 'reload' ): ?>
                    <label><?php echo __('services_reload70');?></label>
                <?php elseif( $action == 'start' ): ?>
                    <label><?php echo __('services_start70');?></label>
                <?php elseif( $action == 'stop' ): ?>
                    <label><?php echo __('services_stop70');?></label>
                <?php elseif( $action == 'status' ): ?>
                    <label><?php echo __('services_status70');?></label>
                <?php elseif( $action == 'repair' ): ?>
                    <label><?php echo __('services_repair70');?></label>
                <?php elseif( $action == 'engine_start' or $action == 'engine_stop' ): ?>
                    <label><?php echo __('services_engine70');?></label>
                <?php endif ?>

                </span>
                <a href="services.php" class="btn btn-sm px-2 rounded-circle btn_return"><i class="fas fa-arrow-left"></i></a>
            </div>
        </div>
        <div id="body_service_reload" class="card-body card-body-warriors pt-2 px-4">
        </div>
    </div>
</div>
<script> service_actions('<?php echo $action; ?>', '<?php echo $token; ?>'); </script>



<?php

// -------------------------------------
echo '</div>';
echo '</div>';

// Add footer
html_end_new();
// Close any open db connections
dbclose();




?>

