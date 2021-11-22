<?php

require_once __DIR__ . '/functions.php';
require __DIR__ . '/login.function.php';

html_head(__('services_action'), 0, false, false);

html_body('services');

echo '<script src="public/js/service_actions.js"></script>';
echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li">' . __('services03') . '</li>';
echo '</ol>';
echo '<div class="row justify-content-center justify-content-md-between">';


if ($_SESSION['user_type'] === 'A') {

    function service($action)
    {
        $archive = "/var/www/html/mailscanner/status/daemon.txt";
        $open = fopen($archive, 'w');
        fwrite($open, $action);
        fclose($open);
    }


    if (isset($_POST['scoreSave'])) {
        $scoreNew = $_POST['score'];
        $scoreNew = trim($scoreNew);
        $highScoreNew = $_POST['highScore'];
        $highScoreNew = trim($highScoreNew);

        $archive = "/etc/MailScanner/MailScanner.conf";
        $open = fopen($archive, 'r');
        $contenido = fread($open, filesize($archive));
        fclose($open);
        $contenido = explode("\n", $contenido);
        $line = "Required SpamAssassin Score =";
        $score = "Required SpamAssassin Score = " . $scoreNew;
        for ($i = 0; $i < count($contenido); $i++) {
            $find = strpos($contenido[$i], $line);
            if ($find != 0 or $find === 0) {
                $contenido[$i] = $score;
                break;
            }
        }

        $line2 = "High SpamAssassin Score =";
        $highScore = "High SpamAssassin Score = " . $highScoreNew;
        for ($i = 0; $i < count($contenido); $i++) {
            $find = strpos($contenido[$i], $line2);
            if ($find != 0 or $find === 0) {
                $contenido[$i] = $highScore;
                break;
            }
        }

        $open = fopen($archive, 'w');
        for ($i = 0; $i < count($contenido); $i++) {
            fwrite($open, $contenido[$i] . PHP_EOL);
        }
        fclose($open);
        $action = "reload";
        service($action);
    }

    function engine()
    {
        $archive = "/etc/postfix/header_checks";
        $open = fopen($archive, 'r');
        $contenido = fread($open, filesize($archive));
        fclose($open);
        $contenido = explode("\n", $contenido);

        $status = strpos($contenido[552], "# /^Received:/ HOLD");
        if ($status === 0) {
            $engine_status = 0;  //apagado
        } else {
            $engine_status = 1;  //encendido
        }

        return $engine_status;
    }

    function score()
    {
        $archive = "/etc/MailScanner/MailScanner.conf";
        $open = fopen($archive, 'r');
        $contenido = fread($open, filesize($archive));
        fclose($open);
        $contenido = explode("\n", $contenido);
        $score = "Required SpamAssassin Score =";
        echo '<form action="services.php" method="POST">';
        echo '<div class="d-flex flex-row justify-content-start align-items-center">';
        echo '    <label class="services_label">Score Spam</label>';
        for ($i = 0; $i < count($contenido); $i++) {
            $find = strpos($contenido[$i], $score);
            if ($find != 0 or $find === 0) {
                $aux = str_replace($score, "", $contenido[$i]);
                $aux = trim($aux);
                echo '<input type="text" name="score" value="' . $aux . '" class="form-control w-input-text services_input" pattern="^\d+(?:\.\d{1,2})?$">';
                break;
            }
        }
        echo '</div>';

        
        echo '<div class="d-flex flex-row justify-content-start align-items-center my-3">';
        echo '    <label class="services_label mt-1">Score High Spam</label>';
        $highScore = "High SpamAssassin Score =";
        for ($i = 0; $i < count($contenido); $i++) {
            $find = strpos($contenido[$i], $highScore);
            if ($find != 0 or $find === 0) {
                $aux = str_replace($highScore, "", $contenido[$i]);
                $aux = trim($aux);
                echo '<input type="text" name="highScore" value="' . $aux . '" class="form-control w-input-text services_input" pattern="^\d+(?:\.\d{1,2})?$">';
                break;
            }
        }
        echo '</div>';

        echo '<div class="d-flex flex-row justify-content-start align-items-center">';
        echo '    <button name="scoreSave" class="btn btn_services btn_services_start">'.__('save09').'</button>';
        echo '</div>';

        echo '</form>';
    }
    

?>
    
    <div class="col-xl-4 col-md-6 col-sm-8">
        <div class="card card-wr services_div">
            <div class="card-header card-header-warriors">
                <div class="d-flex flex-row justify-content-between">
                    <span>
                        <i class="fas fa-server"></i>
                        <label><?php echo __('services_engine70'); ?></label>
                    </span>
                    <label id="mta_label_status"><?php echo __('services_running70'); ?></label>
                </div>
            </div>
            <div class="card-body card-body-warriors pt-1">
                <div id="loading_service_mta">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border spinner_loading_sm" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div id="body_service_mta" style="display: none;"></div>
            </div>
        </div>
    </div>
    
    <?php $engine_status = engine(); ?>
    <div class="col-xl-4 col-md-6 col-sm-8">
        <div class="card card-wr services_div">
            <div class="card-header card-header-warriors">
                <div class="d-flex flex-row justify-content-between">
                    <span>
                        <i class="fas fa-tools"></i>
                        <label><?php echo __('services_mta70'); ?></label>
                    </span>
                    <?php if ( $engine_status == 1 ): ?>
                        <label class="engine_label_status engine_label_status_start"><?php echo __('services_running70'); ?></label>
                    <?php else: ?>
                        <label class="engine_label_status engine_label_status_stop"><?php echo __('services_stopped70'); ?></label>
                    <?php endif ?>
                </div>
                
                
            </div>
            <div class="card-body card-body-warriors pt-1">
                <div class="d-flex flex-row justify-content-between">
                    <?php if ( $engine_status == 0 ): ?>
                        <div class="">
                            <form action="services_action.php" method="POST">
                                <input type="hidden" name="token" value="<?php echo $_SESSION["token"]; ?>">
                                <input type="hidden" name="action" value="engine_start">
                                <button name="btn_engine" class="btn btn_services btn_services_start"><?php echo __('services_start70'); ?></button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="">
                            <form action="services_action.php" method="POST">
                                <input type="hidden" name="token" value="<?php echo $_SESSION["token"]; ?>">
                                <input type="hidden" name="action" value="engine_stop">
                                <button name="btn_engine" class="btn btn_services btn_services_stop"><?php echo __('services_stop70'); ?></button>
                            </form>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 col-sm-8">
        <div class="card card-wr services_div">
            <div class="card-header card-header-warriors">
                <div class="d-flex flex-row justify-content-between">
                    <span>
                        <i class="fas fa-database"></i>
                        <label><?php echo __('services_database70'); ?></label>
                    </span>
                </div>
            </div>
            <div class="card-body card-body-warriors pt-1">
                <div class="d-flex flex-row justify-content-between">
                    <form action="services_action.php" method="POST">
                        <input type="hidden" name="action" value="repair">
                        <input type="hidden" name="token" value="<?php echo $_SESSION["token"]; ?>">
                        <button class="btn btn_services btn_services_reload"><?php echo __('services_repair70'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 col-sm-8">
        <div class="card card-wr services_div">
            <div class="card-header card-header-warriors">
                <i class="fas fa-hdd"></i>
                <?php echo __('services_score70'); ?>
            </div>
            <div class="card-body card-body-warriors pt-1">
                <div class="d-flex flex-column">
                <?php 
                
                    score();
                
                ?>
                </div>
            </div>
        </div>
    </div>

    
    <script> get_status_mta('<?php echo $_SESSION['token']; ?>'); </script>
<?php
} else {
    echo ' 
        <div class="col-12 permission-denied">
            <div>
                '.__('permissionsdenied99').'
            </div> 
        </div>';
}

// -------------------------------------
echo '</div>';
echo '</div>';


// Add footer
html_end_new();

// Close any open db connections
dbclose();
?>
