<?php
/*
 * MailWatch for MailScanner
 * Copyright (C) 2003-2011  Steve Freegard (steve@freegard.name)
 * Copyright (C) 2011  Garrod Alwood (garrod.alwood@lorodoes.com)
 * Copyright (C) 2014-2018  MailWatch Team (https://github.com/mailwatch/1.2.0/graphs/contributors)
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * In addition, as a special exception, the copyright holder gives permission to link the code of this program with
 * those files in the PEAR library that are licensed under the PHP License (or with modified versions of those files
 * that use the same license as those files), and distribute linked combinations including the two.
 * You must obey the GNU General Public License in all respects for all of the code used other than those files in the
 * PEAR library that are licensed under the PHP License. If you modify this program, you may extend this exception to
 * your version of the program, but you are not obligated to do so.
 * If you do not wish to do so, delete this exception statement from your version.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free
 * Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once __DIR__ . '/filter.inc.php';
require_once __DIR__ . '/functions.php';

require __DIR__ . '/login.function.php';

html_head(__('opresult21'));

if ($_SESSION['token'] !== deepSanitizeInput($_POST['token-modal'], 'url')) {
    header('Location: status.php?error=token');
    die();
}
if (false === checkFormToken('/do_message_actions_bw.php form token', $_POST['formtoken-modal'])) {
    header('Location: status.php?error=formtoken');
    die();
}

$type_form = (isset( $_POST['btn_checks'] )) ? 'Boton de checkbox' : 'Boton de likes';
$form_type = (isset( $_POST['btn_checks'] )) ? true : false ;

$result = 0;
if ( $form_type ) {
    $ids = array();
    $list = '';
    if( !isset($_POST['list']) ){
        header('Location: status.php?modal_error='. __('error071'));
        die();
    } else $list = ( $_POST['list'] == 'w' ) ? 'whitelist' : 'blacklist';

    if ( isset($_POST['list']) ) {

        foreach ($_POST as $key => $value) {
            if (preg_match('/^ID-(.+)$/', $value, $Regs)) { 
                array_push( $ids, $Regs[1]);
            }
        }
        if ( sizeof($ids) == 0 ) {
            $msg = __('error_selected_mail_05');
            $msg = str_replace(' ', '-', $msg);
            header('Location: status.php?modal_error='.$msg);
            die();
        }
    }
    if( !isset($_POST['sender_list_email']) && 
        !isset($_POST['sender_list_domain']) && 
        !isset($_POST['recipient_list_email']) && 
        !isset($_POST['recipient_list_domain']) 
        ){
        $msg = __('error_options_05');
        $msg = str_replace(' ', '-', $msg);
        header('Location: status.php?modal_error='.$msg);
        die();
    }

    $sender_list_email = isset( $_POST['sender_list_email'] ) ? true : false;
    $sender_list_domain = isset( $_POST['sender_list_domain'] ) ? true : false;
    $recipient_list_email = isset( $_POST['recipient_list_email'] ) ? true : false;
    $recipient_list_domain = isset( $_POST['recipient_list_domain'] ) ? true : false;

    $sql_select = 'SELECT id, from_address, from_domain, to_address, to_domain FROM maillog WHERE id IN ( ';
       foreach ($ids as $key => $id) {
          if ( (count($ids) - 1) !== $key ) {
             $sql_select .= "'".$id."',";
          }else {
             $sql_select .= "'".$id."'";
          }
       }
    $sql_select .= ' );';
    $result = dbquery($sql_select);
    $data = $result->fetch_all();
    $sql_replace = 'REPLACE INTO ' . $list . ' (to_address, to_domain, from_address) VALUES ';
    foreach ($data as $key => $row) {
        // ( "recipient@correo.com", "@correo.com", "sender@correo.com")
        if ( $sender_list_email ) {
            $sql_replace .= "( ";
            $sql_replace .= ( $recipient_list_email ) ? '"'.$row[3].'", ' : '"", ';
            $sql_replace .= ( $recipient_list_domain ) ? '"@'.$row[4].'", ' : '"", ';
            $sql_replace .= ( $sender_list_email ) ? '"'.$row[1].'" ' : '""';
            $sql_replace .= ( (count($ids) - 1) !== $key ) ? " )," : ( $sender_list_domain ) ? " )," : " )";
        }
        // ( "recipient@correo.com", "@correo.com", "@correo.com")
        if ( $sender_list_domain ) {
            $sql_replace .= "( ";
            $sql_replace .= ( $recipient_list_email ) ? '"'.$row[3].'", ' : '"", ';
            $sql_replace .= ( $recipient_list_domain ) ? '"'.$row[4].'", ' : '"", ';
            $sql_replace .= ( $sender_list_domain ) ? '"@'.$row[2].'" ' : '""';
            $sql_replace .= ( (count($ids) - 1) !== $key ) ? " )," : " )";
        }
        // ( "recipient@correo.com", "@correo.com", "@correo.com")
        if ( !$sender_list_domain && !$sender_list_email) {
            $sql_replace .= "( ";
            $sql_replace .= ( $recipient_list_email ) ? '"'.$row[3].'", ' : '"", ';
            $sql_replace .= ( $recipient_list_domain ) ? '"'.$row[4].'", ' : '"", ';
            $sql_replace .= ( $sender_list_domain ) ? '"@'.$row[2].'" ' : '""';
            $sql_replace .= ( (count($ids) - 1) !== $key ) ? " )," : " )";
        }
    }
    $sql_replace .= ";";

    $result = dbquery($sql_replace);
    // Audit Log
    foreach ($data as $key => $row) {
        $to_address = $row[3];
        $to_domain = '@'.$row[4];
        $from_address = $row[3];
        $from_domain = '@'.$row[4];

        // ( "recipient@correo.com", "@correo.com", "sender@correo.com")
        if ( $sender_list_email || $recipient_list_email || $recipient_list_domain ) {
            audit_log(sprintf(__('auditlogadded07', true), $from_address, $to_address, $list));
        }
        // ( "recipient@correo.com", "@correo.com", "@correo.com")
        if ( $sender_list_domain ) {
            audit_log(sprintf(__('auditlogadded07', true), $from_domain, $to_address, $list));
        }
    }
}else {

    $btn_select = null;
    // Recupera el btn seleccionado
    foreach ($_POST as $key => $value) {
        if (preg_match('/^BTN-(.+)$/', $value, $Regs)) { 
            $btn_select =  $Regs[1];
        }
    }
    if ( !$btn_select ) {
        $msg = __('error_options_05');
        $msg = str_replace(' ', '-', $msg);
        header('Location: status.php?modal_error='.$msg);
        die();
    }
    // Se recupera el id y la tabla correspondiente
    $btn_select = explode('-', $btn_select);
    $id = $btn_select[1];
    $list = ( $btn_select[0] === 'w' ) ? 'whitelist' : 'blacklist';
    
    // Se recuperan los datos del correo mediante el id
    $sql_select = 'SELECT id, from_address, from_domain, to_address, to_domain, spamwhitelisted, spamblacklisted FROM maillog WHERE id = "'.$id.'" ;';
    $result = dbquery($sql_select);
    $data = $result->fetch_row();

    // Se insertan en la tabla correspondiente de whitelist o blacklist
    $sql_replace = 'REPLACE INTO ' . $list . ' (to_address, to_domain, from_address) VALUES ';
    $sql_replace .= '("", "", "'.$data[1].'");';
    $result = dbquery($sql_replace);

    // Se actualiza la información en la tabla de maillog
    $whitelist = ( $list === 'whitelist' ) ? 1 : 0;
    $blacklist = ( $list === 'blacklist' ) ? 1 : 0;
    $sql_update = 'UPDATE maillog SET spamwhitelisted = '.$whitelist.', spamblacklisted = '.$blacklist.' WHERE id = "'.$data[0].'";';
    $result = dbquery($sql_update);

    audit_log(sprintf(__('auditlogadded07', true), $data[1], $data[3], $list));
}
dbclose();
if( $result == 1 ) {
    $msg = __('success_added_wb1_05').$list.__('success_added_wb2_05');
    $msg = str_replace(' ', '-', $msg);
    header('Location: status.php?modal_success='.$msg);
    die();
}else {
    $msg = __('error_unexpected_05');
    $msg = str_replace(' ', '-', $msg);
    header('Location: status.php?modal_error='.$msg);
    die();
}

html_body('status');

// Add footer
html_end_new(); 

