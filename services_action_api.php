<?php 

require_once __DIR__ . '/functions.php';
require __DIR__ . '/login.function.php';

if ( !isset( $_POST['action'] ) || !$_SESSION['user_type'] === 'A' || !isset( $_POST['token'] )) {
    header('Location: services.php');
}
if ( $_POST['token'] != $_SESSION['token'] ) {
    header('Location: services.php');
}


$action = ( isset( $_POST['action'] ) ) ? $_POST['action'] : 'Null';

function service($action){
	$archive = "/var/www/html/mailscanner/status/daemon.txt";
	$open = fopen($archive,'w');
	fwrite($open,$action);
	fclose($open);
}

function clean_files(){
	$archive = "/var/www/html/mailscanner/status/wdm_status.txt";
	$open = fopen($archive,'w');
	fwrite($open,"");
	fclose($open);
}

function service_actions( $actions, $time=10 ){
    
    $data = array();
    service( $actions );
    sleep( $time );

    $archive = ( $actions === 'status') 
            ? "/var/www/html/mailscanner/status/wdm_status.txt"
            : "/var/www/html/mailscanner/status/wdm.txt";

    $open = fopen($archive,'r');
    $contenido = fread($open,filesize($archive));
    fclose($open);

    if ( strlen($contenido) != 0) {
        $contenido = str_replace('MailScanner','WDM',$contenido);
        $contenido = str_replace('mailscanner','WDM',$contenido);
    }else {
        $contenido = "No results - ".$actions;
    }
    clean_files();
    $data['contenido'] = $contenido;
    return $data;
}

function engine_actions( $actions ){
    
    $data = array();
    service($actions);
    sleep( 4 );
    $contenido = ($action == "engine_start") ? "Engine Start" : "Engine Stop";

    $archive = "/etc/postfix/header_checks";
    $open = fopen($archive, 'r');
    $conten = fread($open, filesize($archive));
    fclose($open);
    $conten = explode("\n", $conten);
    
    $num_line = 552;
    $contenido .= "\n"."File: /etc/postfix/header_checks";
    $contenido .= "\n"."Modified line number: ".$num_line;
    $contenido .= "\n"."Line: ".$conten[ $num_line ];
    $contenido .= "\n";
    $contenido .= "\n"."Restarting postfix service...";

    $data['contenido'] = $contenido;
    return $data;
}


function get_status_mta(){
    $data = array();
    shell_exec('cat /dev/null > /var/www/html/mailscanner/status/wdm.txt');
    
    service('status');
    sleep(4);
    
    $contend = array();
    $data['error'] = false;
    try {
        $archive = "/var/www/html/mailscanner/status/wdm_status.txt";
        $open = fopen($archive, 'r');
        $contend = fread($open,filesize($archive));
        fclose($open);
        
        $data['contend'] = $contend;
        $contend = strpos($contend, "running");
    } catch (Exception $e) {
        $data['error'] = $e->getMessage();
    }

    clean_files();

    $status = 0;
    $status_label = '';
    if ($contend > 0) {
        $status = 1;  //encendido
        $status_label = 'Encendido';
    } else {
        $status = 0; //apagado
        $status_label = 'Apagado';
    }

    $data['status'] = $status;
    $data['status_label'] = $status_label;
    $data['labels']['reload'] = __('services_reload70');
    $data['labels']['start'] = __('services_start70');
    $data['labels']['stop'] = __('services_stop70');
    $data['labels']['running'] = __('services_running70');
    $data['labels']['stopped'] = __('services_stopped70');
    $data['labels']['status_'] = __('services_status70');

    return $data;
}


$data = array();
if ( $action == 'status_mta' ) {
    $data = get_status_mta();
} 
else if ( $action == 'reload' or $action == 'start' or $action == 'stop' or $action == 'repair' ) {
    $data = service_actions( $action);
}
else if ( $action == 'status' ) {
    $data = service_actions( $action, 5 );
}
else if ( $action == "engine_start" or $action == "engine_stop" ) {
    $data = engine_actions( $action );
}
else {
    $data['contenido'] = 'No existe la acción';
}

$data['action'] = $action;
echo json_encode($data, true);

?>
