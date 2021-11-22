<?php

/*
 MailWatch for MailScanner
 Copyright (C) 2003-2011  Steve Freegard (steve@freegard.name)
 Copyright (C) 2011  Garrod Alwood (garrod.alwood@lorodoes.com)
 Copyright (C) 2014-2018  MailWatch Team (https://github.com/mailwatch/1.2.0/graphs/contributors)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 In addition, as a special exception, the copyright holder gives permission to link the code of this program
 with those files in the PEAR library that are licensed under the PHP License (or with modified versions of those
 files that use the same license as those files), and distribute linked combinations including the two.
 You must obey the GNU General Public License in all respects for all of the code used other than those files in the
 PEAR library that are licensed under the PHP License. If you modify this program, you may extend this exception to
 your version of the program, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your version.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/lib/password.php';

require __DIR__ . '/login.function.php';

html_head(__('usermgnt12'), 0, false, false);
html_body('other');
echo '<div class="container-fluid">';
   echo '<ol class="breadcrumb my-4 title_page">';
   echo '<li class="breadcrumb-item title_page_li"><a href="other.php">'.__('toolslinks03').'</a></li>';
   echo '<li class="breadcrumb-item title_page_li active"><a style="color: #00000088;" href="user_manager.php">'.__('usermgnt12').'</a></li>';
   echo '</ol>';
echo ' <div class="d-flex flex-column">';

/**
 * @param string $value
 * @param string $type
 * @return string
 */
function getHtmlMessage($value, $type)
{
    switch ($type) {
        case 'error':
            return '<div class="alert alert-danger" role="alert">' . $value . '</div>';

        case 'success':
            return '<div class="alert alert-success" role="alert">' . $value . '</div>';

        default:
            return $value;
    }
}

/**
 * @param string $username
 * @param string $method
 * @return bool|string
 */
function testSameDomainMembership($username, $method)
{
    $parts = explode('@', $username);
    $sql = "SELECT filter FROM user_filters WHERE username = '" . safe_value(stripslashes($_SESSION['myusername'])) . "'";
    $result = dbquery($sql);
    $filter_domain = array();
    for ($i = 0; $i < $result->num_rows; $i++) {
        $filter = $result->fetch_row();
        $filter_domain[] = $filter[0];
    }
    if ($_SESSION['user_type'] === 'D' && count($parts) === 1 && $_SESSION['domain'] !== '') {
        return getHtmlMessage(__('error' . $method . 'nodomainforbidden12'), 'error');
    }

    if ($_SESSION['user_type'] === 'D' && count($parts) === 2 && ($parts[1] !== $_SESSION['domain'] && in_array($parts[1],
                $filter_domain, true) === false)) {
        return getHtmlMessage(sprintf(__('error' . $method . 'domainforbidden12'), $parts[1]), 'error');
    }

    return true;
}

/**
 * @param string $username
 * @param string $userType
 * @param string $oldUserType
 * @return bool|string
 */
function testPermissions($username, $userType, $oldUserType)
{
    if (($_SESSION['user_type'] !== 'A' && $oldUserType === 'A') || ($_SESSION['user_type'] === 'D' && stripslashes($_SESSION['myusername']) !== stripslashes($username) && $userType !== 'U' && (!defined('ENABLE_SUPER_DOMAIN_ADMINS') || ENABLE_SUPER_DOMAIN_ADMINS === false))) {
        return getHtmlMessage(__('erroradminforbidden12'), 'error');
    }

    if ($_SESSION['user_type'] === 'D' && $userType === 'A') {
        return getHtmlMessage(__('errortypesetforbidden12'), 'error');
    }

    return true;
}

/**
 * @param string $username
 * @param string $usertype
 * @param string $oldUsername
 * @return bool|string
 */
function testValidUser($username, $usertype, $oldUsername)
{
    if ($usertype !== 'A' && validateInput($username, 'email') === false && (!defined('ALLOW_NO_USER_DOMAIN') || ALLOW_NO_USER_DOMAIN === false)) {
        return getHtmlMessage(__('forallusers12'), 'error');
    }

    if (!isset($_POST['password'], $_POST['password1'])) {
        return getHtmlMessage(__('dievalidate99'), 'error');
    }

    if ($_POST['password'] === '') {
        return getHtmlMessage(__('errorpwdreq12'), 'error');
    }

    if ($_POST['password'] !== $_POST['password1']) {
        return getHtmlMessage(__('errorpass12'), 'error');
    }

    if ($username === '') {
        return getHtmlMessage(__('erroruserreq12'), 'error');
    }

    if (stripslashes($oldUsername) !== stripslashes($username) && checkForExistingUser($username)) {
        return getHtmlMessage(sprintf(__('userexists12'), sanitizeInput(stripslashes($username))), 'error');
    }

    return true;
}

function testToken()
{
    if (!isset($_POST['token']) && !isset($_GET['token'])) {
        return getHtmlMessage(__('dievalidate99'), 'error');
    }

    if ((isset($_POST['token']) && (false === checkToken($_POST['token'])))
        || (isset($_GET['token']) && (false === checkToken($_GET['token'])))) {
        header('Location: login.php?error=pagetimeout');
        die();
    }

    return true;
}

function getUserById($additionalFields = false)
{
    if (isset($_POST['id'])) {
        $uid = (int)$_POST['id'];
    } elseif (isset($_GET['id'])) {
        $uid = (int)$_GET['id'];
    } else {
        return getHtmlMessage(__('dievalidate99'), 'error');
    }
    if (($uid = deepSanitizeInput($uid, 'num')) < -1) {
        return getHtmlMessage(__('dievalidate99'), 'error');
    }
    $sql = 'SELECT id, username, type' . ($additionalFields ? ', fullname, quarantine_report, quarantine_rcpt, spamscore, highspamscore, noscan, login_timeout, last_login' : '') . " FROM users WHERE id='" . $uid . "'";
    $result = dbquery($sql);
    if ($result->num_rows === 0) {
        audit_log(sprintf(__('auditlogunknownuser12'), $_SESSION['myusername'], $uid));
        return getHtmlMessage(__('accessunknownuser12'), 'error');
    }
    return $result->fetch_object();
}

/**
 * @param string $loggedinUserType Type of logged in User accessing (A, D, U, or R)
 * @param string $action 'edit' or 'new'
 * @param string $uid
 * @param string $lastlogin
 * @param string $username
 * @param string $fullname
 * @param array $type array which has 'selected' as value for selected type
 * @param string|float|int $timeout
 * @param string $quarantine_report 'checked' if box shall be ticked
 * @param string $quarantine_rcpt
 * @param string $noscan checkbox default to 'checked'
 * @param string|float|int $spamscore default 0
 * @param string|float|int $highspamscore default 0
 *
 * @return string
 */

function printUserFormular(
    $loggedinUserType,
    $action,
    $uid = '',
    $lastlogin = '',
    $username = '',
    $fullname = '',
    $type = array('A' => '', 'D' => '', 'U' => 'selected', 'R' => ''),
    $timeout = '',
    $quarantine_report = '',
    $quarantine_rcpt = '',
    $noscan = 'checked',
    $spamscore = '0',
    $highspamscore = '0'
) {
    $returnString = '';

    if ($action === 'edit') {
        $formheader = __('edituser12') . ' ' . $username;
        $password = 'XXXXXXXX';
    } else {
        $formheader = __('newuser12');
        $password = '';
    }
    
    $returnString .= '<div class="col-12">';
    $returnString .= '<div id="formerror" class="hidden" class="small alert alert-dark" role="alert">';
    $returnString .= '</div>';
    $returnString .= '</div>';

    $returnString .= '<div class="flex-lg-row w-100 mb-3">';
    
    $returnString .= '<div class="card card-wr" style="max-width: 600px; min-width: 300px;">';
    $returnString .= '<div class="card-header card-header-warriors">
                        <i class="fas fa-user"></i> ';
    $returnString .=    $formheader; 
    $returnString .= '</div>';
    $returnString .= '<div class="card-body card-body-warriors">';

    $returnString .= '<form method="POST" ACTION="user_manager.php" ONSUBMIT="return validateForm();" autocomplete="off">';
    $returnString .= '	<input type="HIDDEN" name="token" value="' . $_SESSION['token'] . '">';
    $returnString .= '	<input type="HIDDEN" name="action" value="' . $action . '">';
    $returnString .= ' <INPUT TYPE="HIDDEN" NAME="formtoken" VALUE="' . generateFormToken('/user_manager.php ' . $action . ' token') . '">';
    if ($action === 'edit') {
        $returnString .= '<INPUT TYPE="HIDDEN" NAME="id" VALUE="' . $uid . '">' . PHP_EOL;        
    }

    $returnString .= '	<div class="row px-3">';
    if (!defined('ALLOW_NO_USER_DOMAIN') || !ALLOW_NO_USER_DOMAIN) {
        $returnString .= '<div class="col-12 m-0 p-0">';
        $returnString .= '<div class="small alert alert-info mb-1 p-2" role="alert">';
        $returnString .=  __('forallusers12');
        $returnString .= '</div>';
        $returnString .= '</div>';
    }
    if ($action === 'edit') {
        $returnString .= '<div class="col-12 m-0 p-0">';
        $returnString .= '<div class="small alert alert-dark mb-3 p-2" role="alert">';
        $returnString .=    $lastlogin;
        $returnString .= '</div>';
        $returnString .= '</div>';
    }
    $returnString .= '		<div class="col-sm-5 m-0 p-0 align-self-center mb-sm-2">';
    $returnString .= '			<label class="w-label m-0 p-0 m-0 p-0">' . __('username0212') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-sm-7 m-0 p-0 mb-sm-2">';
    $returnString .= '			<input type="text" id="username" name="username" VALUE="' . $username . '" class="form-control w-input-text"/>';
    $returnString .= '		</div>';

    echo $username ;

    $returnString .= '		<div class="col-sm-5 m-0 p-0 align-self-center mb-sm-2">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('name12') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-sm-7 m-0 p-0 mb-sm-2">';
    $returnString .= '			<input type="text" name="fullname" VALUE="' . $fullname . '" class="form-control w-input-text"/>';
    $returnString .= '		</div>';
    
    $returnString .= '		<div class="col-sm-5 m-0 p-0 align-self-center mb-sm-2">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('password12') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-sm-7 m-0 p-0 mb-sm-2">';
    $returnString .= '			<input type="PASSWORD" id="password" name="password"  VALUE="' . $password . '" class="form-control w-input-text"/>';
    $returnString .= '		</div>';
    
    $returnString .= '		<div class="col-sm-5 m-0 p-0 align-self-center mb-sm-2">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('retypepassword12') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-sm-7 m-0 p-0 mb-sm-2">';
    $returnString .= '			<input type="PASSWORD" id="retypepassword" name="password1" VALUE="' . $password . '" class="form-control w-input-text"/>';
    $returnString .= '		</div>';
    
    $returnString .= '		<div class="col-sm-5 m-0 p-0 align-self-center mb-sm-2">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('usertype12') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-sm-7 m-0 p-0 mb-sm-2">';
    $returnString .= '			<select name="type" class="form-control custom-select w-input-text">';
    $returnString .= ($loggedinUserType === 'A' ? '<OPTION ' . $type['A'] . ' VALUE="A">' . __('admin12') . '</OPTION>' : '') .
    '<OPTION ' . $type['D'] . ' VALUE="D">' . __('domainadmin12') . '</OPTION>
<OPTION ' . $type['U'] . ' VALUE="U">' . __('user12') . '</OPTION>
' . ($action === 'edit' ? '<OPTION ' . $type['R'] . ' VALUE="R">' . __('userregex12') . '</OPTION>' : '');
    $returnString .= '			</select>';
    $returnString .= '		</div>';
    
    $returnString .= '		<div class="col-sm-5 m-0 p-0 align-self-center mb-sm-2">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('usertimeout12') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-sm-5 m-0 p-0 mb-sm-2">';
    $returnString .= '			<input type="text" TYPE="TEXT" NAME="timeout" class="form-control w-input-text"
                                VALUE="' . $timeout . '"/>';
    $returnString .= '		</div>';
    $returnString .= '		<div class="col-sm-2 m-0 p-0 mb-sm-2">';
    $returnString .= '          <button class="btn" data-toggle="tooltip" data-placement="top" title="' . __('empty12') . ' = ' . __('usedefault12') . '">';
    $returnString .= '          <i class="fas fa-question-circle text-muted"></i>';
    $returnString .= '          </button>';
    $returnString .= '		</div>   ';
    
    $returnString .= '		<div class="col-5 m-0 p-0 align-self-center mb-sm-2">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('quarrep12') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-5 m-0 p-0 my-sm-2">';
    $returnString .= '			<div class="form-check">';
    $returnString .= '				<input class="form-check-input" TYPE="CHECKBOX" NAME="quarantine_report" ' . $quarantine_report . '>';
    $returnString .= '				<label NAME="quarantine_report" class="form-check-label"> Send Daily Report</label>';
    $returnString .= '			</div>';
    $returnString .= '		</div>';
    $returnString .= '		<div class="col2 m-0 p-0 text-right mb-sm-2 mt-2">';
    $returnString .= '			<button type="submit" name="action" value="sendReportNow" class="btn btn-sm btn-wr-black">' . __('sendReportNow12') . '</button>';
    $returnString .= '		</div>   ';
    $returnString .= '		';
    $returnString .= '		<div class="col-sm-5 col-7 m-0 p-0 align-self-center mb-sm-2 mt-2">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('quarreprec12') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-sm-5 col-5 m-0 p-0 mb-sm-2 mt-2">';
    $returnString .= '			<input type="text" name="quarantine_rcpt" VALUE="' . $quarantine_rcpt . '" class="form-control w-input-text">';
    $returnString .= '		</div>';
    $returnString .= '		<div class="col-sm-2 col-12 m-0 p-0 mb-sm-2 align-self-center">';
    $returnString .= '          <button class="btn" data-toggle="tooltip" data-placement="top" title="' . __('overrec12') . '">';
    $returnString .= '          <i class="fas fa-question-circle text-muted"></i>';
    $returnString .= '          </button>';
    $returnString .= '		</div> ';
    
    $returnString .= '		<div class="col-5 m-0 p-0 align-self-center mb-sm-2">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('scanforspam12') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-7 m-0 p-0 mb-sm-2">';
    $returnString .= '			<div class="form-check">';
    $returnString .= '				<input type="checkbox" NAME="noscan" ' . $noscan . ' class="form-check-input">';
    $returnString .= '				<label class="form-check-label">' . __('scanforspam212') . '</label>';
    $returnString .= '			</div>';
    $returnString .= '		</div>';
    
    $returnString .= '		<div class="col-sm-5 m-0 p-0 align-self-center mb-sm-2">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('pontspam12') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-sm-3 m-0 p-0 mb-sm-2">';
    $returnString .= '			<input type="text" name="spamscore" VALUE="' . $spamscore . '" size="4" class="form-control w-input-text"/>';
    $returnString .= '		</div>';
    $returnString .= '		<div class="col-sm-4 m-0 p-0 mb-sm-2 align-self-center">';
    $returnString .= '          <button class="btn" data-toggle="tooltip" data-placement="top" title="0 = ' . __('usedefault12') . '">';
    $returnString .= '          <i class="fas fa-question-circle text-muted"></i>';
    $returnString .= '          </button>';
    $returnString .= '		</div>   ';
    
    $returnString .= '		<div class="col-sm-5 m-0 p-0 align-self-center mb-sm-2">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('hpontspam12') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-sm-3 m-0 p-0 mb-sm-2">';
    $returnString .= '			<input type="text"  name="highspamscore" VALUE="' . $highspamscore . '" size="4" class="form-control w-input-text"/>';
    $returnString .= '		</div>';
    $returnString .= '		<div class="col-sm-4 m-0 p-0 mb-sm-2 align-self-center">';
    $returnString .= '          <button class="btn" data-toggle="tooltip" data-placement="top" title="0 = ' . __('usedefault12') . '">';
    $returnString .= '          <i class="fas fa-question-circle text-muted"></i>';
    $returnString .= '          </button>';
    $returnString .= '		</div>   ';
    
    $returnString .= '		<div class="col-sm-6 m-0 p-0 align-self-center mt-sm-3">';
    $returnString .= '			<label class="w-label m-0 p-0">' . __('action_0212') . '</label>';
    $returnString .= '		</div>   ';
    $returnString .= '		<div class="col-sm-2 m-0 p-0 mt-sm-3">';
    $returnString .= '			<a href="user_manager.php" class="btn btn-sm btn-wr-black" style="text-decoration: none; color: #eee; ">Cancel</a>';
    $returnString .= '		</div>';
    $returnString .= '		<div class="col-sm-2 m-0 p-0 mt-sm-3">';
    $returnString .= '			<button type="RESET" VALUE="' . __('reset12') . '" class="btn btn-sm btn-wr-gray">Reset</button>';
    $returnString .= '		</div>';
    $returnString .= '		<div class="col-sm-2 m-0 p-0 mt-sm-3">';
    $returnString .= '			<button type="submit" name="submit" class="btn btn-sm btn-wr-red">' . ($action === 'edit' ? __('update12') : __('create12')) . '</button>';
    $returnString .= '		</div>';
    $returnString .= '	</div>';
    $returnString .= '</form>';
    $returnString .= '</div>';
    $returnString .= '</div>';
    $returnString .= '</div>';
    


    return $returnString;
}

function storeUser($n_username, $n_type, $uid, $oldUsername = '', $oldType = '')
{
    if (!isset($_POST['fullname'], $_POST['spamscore'], $_POST['highspamscore'], $_POST['timeout'], $_POST['quarantine_rcpt'])) {
        return getHtmlMessage(__('dievalidate99'), 'error');
    }
    $n_fullname = deepSanitizeInput($_POST['fullname'], 'string');
    if (!validateInput($n_fullname, 'general')) {
        $n_fullname = '';
    }
    $n_password = safe_value(password_hash($_POST['password'], PASSWORD_DEFAULT));

    if (!validateInput($n_type, 'type')) {
        $n_type = 'U';
    }
    $spamscore = deepSanitizeInput($_POST['spamscore'], 'float');
    if (!validateInput($spamscore, 'float')) {
        $spamscore = '0';
    }
    $highspamscore = deepSanitizeInput($_POST['highspamscore'], 'float');
    if (!validateInput($highspamscore, 'float')) {
        $highspamscore = '0';
    }
    $timeout = deepSanitizeInput($_POST['timeout'], 'num');
    if (!validateInput($timeout, 'timeout')) {
        $timeout = '-1';
    }
    $n_quarantine_report = '1';
    if (!isset($_POST['quarantine_report'])) {
        $n_quarantine_report = '0';
    }
    $noscan = '0';
    if (!isset($_POST['noscan'])) {
        $noscan = '1';
    }
    $quarantine_rcpt = deepSanitizeInput($_POST['quarantine_rcpt'], 'string');
    if (!validateInput($quarantine_rcpt, 'user')) {
        $quarantine_rcpt = '';
    }

    $type = array();
    $type['A'] = __('admin12', true);
    $type['D'] = __('domainadmin12', true);
    $type['U'] = __('user12', true);
    $type['R'] = __('user12', true);
    if ($uid === -1) {//new user
        $sql = "INSERT INTO users (username, fullname, password, type, quarantine_report, login_timeout, spamscore, highspamscore, noscan, quarantine_rcpt)
                        VALUES ('" . safe_value(stripslashes($n_username)) . "','$n_fullname','$n_password','$n_type','$n_quarantine_report','$timeout','$spamscore','$highspamscore','$noscan','" . safe_value(stripslashes($quarantine_rcpt)) . "')";
        dbquery($sql);
        audit_log(__('auditlog0112',
                true) . ' ' . $type[$n_type] . " '" . $n_username . "' (" . $n_fullname . ') ' . __('auditlog0212',
                true));
        return getHtmlMessage(sprintf(__('usercreated12'), stripslashes($n_username)), 'success');
    }

    if ($_POST['password'] !== 'XXXXXXXX') {// Password reset required
        $sql = "UPDATE users SET username='" . safe_value(stripslashes($n_username)) . "', fullname='$n_fullname', password='$n_password', type='$n_type', quarantine_report='$n_quarantine_report', spamscore='$spamscore', highspamscore='$highspamscore', noscan='$noscan', quarantine_rcpt='" . safe_value(stripslashes($quarantine_rcpt)) . "', login_timeout='$timeout' WHERE id='$uid'";
    } else {
        $sql = "UPDATE users SET username='" . safe_value(stripslashes($n_username)) . "', fullname='$n_fullname', type='$n_type', quarantine_report='$n_quarantine_report', spamscore='$spamscore', highspamscore='$highspamscore', noscan='$noscan', quarantine_rcpt='" . safe_value(stripslashes($quarantine_rcpt)) . "', login_timeout='$timeout' WHERE id='$uid'";
    }
    dbquery($sql);
    // Update user_filters if username was changed
    if (stripslashes($oldUsername) !== stripslashes($n_username)) {
        $sql = "UPDATE user_filters SET username='" . safe_value(stripslashes($n_username)) . "' WHERE username = '" . safe_value(stripslashes($oldUsername)) . "'";
        dbquery($sql);
    }
    if ($oldType !== $n_type) {
        audit_log(
            __('auditlog0312', true) . " '" . $n_username . "' (" . $n_fullname . ') ' . __('auditlogfrom12',
                true) . ' ' . $type[$oldType] . ' ' . __('auditlogto12', true) . ' ' . $type[$n_type]
        );
    }

    return getHtmlMessage(sprintf(__('useredited12'), stripslashes($oldUsername)), 'success');
}

/**
 * @param string $userType
 * @return bool|string
 */
function newUser($userType)
{
    if (is_string($tokentest = testToken())) {
        return $tokentest;
    }

    if (!isset($_POST['submit'])) {
        return printUserFormular($userType, 'new');
    }

    if (!isset($_POST['formtoken'], $_POST['username'], $_POST['type'])) {
        return getHtmlMessage(__('dievalidate99'), 'error');
    }

    if (false === checkFormToken('/user_manager.php new token', $_POST['formtoken'])) {
        header('Location: login.php?error=pagetimeout');
        die();
    }

    $username = html_entity_decode(deepSanitizeInput($_POST['username'], 'string'));
    $n_type = deepSanitizeInput($_POST['type'], 'url');
    if ($username === false || !validateInput($username, 'user')) {
        $username = '';
    }
    if (false === $n_type) {
        return getHtmlMessage(__('dievalidate99'), 'error');
    }

    if (is_string($membertest = testSameDomainMembership($username, 'create'))) {
        return $membertest;
    }

    if (is_string($permissiontest = testPermissions($username, $n_type, ''))) {
        return $permissiontest;
    }

    if (is_string($validuser = testValidUser($username, $n_type, ''))) {
        return $validuser;
    }

    return storeUser($username, $n_type, -1, '', '');
}

/**
 * @param string $userType
 * @return bool|object|stdClass|string
 */
function editUser($userType)
{
    if (is_string($tokentest = testToken())) {
        return $tokentest;
    }
    // if editing user is domain admin check if he tries to edit a user from the same domain. if we do the update we also have to check the new username
    // Validate id
    if (is_string($user = getUserById(true))) {
        return $user;
    }

    if (is_string($membertest = testSameDomainMembership($user->username, 'edit'))) {
        return $membertest;
    }

    if (!isset($_POST['submit'])) {
        $quarantine_report = '';
        if ((int)$user->quarantine_report === 1) {
            $quarantine_report = 'checked="checked"';
        }
        $noscan = '';
        if ((int)$user->noscan === 0) {
            $noscan = 'checked="checked"';
        }
        $timeout = '';
        if ($user->login_timeout !== '-1') {
            $timeout = $user->login_timeout;
        }

        $types = array();
        if ($userType === 'A') {
            $types['A'] = '';
        }
        $types['D'] = '';
        $types['U'] = '';
        $types['R'] = '';

        $timestamp = (int)$user->last_login;
        $lastlogin = __('never12');
        if ($timestamp >= 0) {
            if (defined('DATE_FORMAT')) {
                $dateformat = preg_replace('/%/', '', DATE_FORMAT);
            } else {
                $dateformat = 'm/d/y';
            }
            if (defined('TIME_FORMAT')) {
                $timeformat = preg_replace('/%/', '', TIME_FORMAT);
            } else {
                $timeformat = 'H:i:s';
            }
            $lastlogin = date($dateformat . ' ' . $timeformat, $timestamp);
        }
        $types[$user->type] = 'SELECTED';

        return printUserFormular($userType, 'edit', $user->id, $lastlogin, $user->username, $user->fullname, $types,
            $timeout,
            $quarantine_report, $user->quarantine_rcpt, $noscan, $user->spamscore, $user->highspamscore);
    }

    if (!isset($_POST['formtoken'], $_POST['username'], $_POST['type'])) {
        return getHtmlMessage(__('dievalidate99'), 'error');
    }

    if (false === checkFormToken('/user_manager.php edit token', $_POST['formtoken'])) {
        header('Location: login.php?error=pagetimeout');
        die();
    }

    // Do update
    $username = html_entity_decode(deepSanitizeInput($_POST['username'], 'string'));
    if (!validateInput($username, 'user')) {
        $username = '';
    }
    $n_type = deepSanitizeInput($_POST['type'], 'url');
    if (false === $n_type) {
        return getHtmlMessage(__('dievalidate99'), 'error');
    }

    if (is_string($membertest = testSameDomainMembership($username, 'to'))) {
        return $membertest;
    }

    if (is_string($permissiontest = testPermissions($username, $n_type, $user->type))) {
        return $permissiontest;
    }

    if (is_string($validusertest = testValidUser($username, $n_type, $user->username))) {
        return $validusertest;
    }

    return storeUser($username, $n_type, $user->id, $user->username, $user->type);
}

/**
 * @return bool|object|stdClass|string
 */
function deleteUser()
{
    if (is_string($tokentest = testToken())) {
        return $tokentest;
    }

    if (is_string($user = getUserById())) {
        return $user;
    }

    if (is_string($membertest = testSameDomainMembership($user->username, 'delete'))) {
        return $membertest;
    }

    if ($_SESSION['user_type'] === 'D' && $user->type !== 'U') {
        return getHtmlMessage(__('erroradminforbidden12'), 'error');
    }

    if ($_SESSION['myusername'] === $user->username) {
        return getHtmlMessage(__('errordeleteself12'), 'error');
    }

    $sql = "DELETE u,f FROM users u LEFT JOIN user_filters f ON u.username = f.username WHERE u.username='" . safe_value(stripslashes($user->username)) . "'";
    dbquery($sql);
    audit_log(sprintf(__('auditlog0412', true), $user->username));
    return getHtmlMessage(sprintf(__('userdeleted12'), $user->username), 'success');
}

/**
 * @return string
 */
function userFilter()
{
    if (is_string($tokentest = testToken())) {
        return $tokentest;
    }

    if (is_string($user = getUserById())) {
        return $user;
    }

    if (is_string($membertest = testSameDomainMembership($user->username, 'filter'))) {
        return $membertest;
    }

    if (is_string($permissiontest = testPermissions($user->username, $user->type, ''))) {
        return $permissiontest;
    }

    $getFilter = '';
    if (isset($_POST['filter'])) {
        if (false === checkFormToken('/user_manager.php filter token', $_POST['formtoken'])) {
            header('Location: login.php?error=pagetimeout');
            die();
        }
        $getFilter = deepSanitizeInput($_POST['filter'], 'url');
        if (!validateInput($getFilter, 'email') && !validateInput($getFilter, 'host')) {
            $getFilter = '';
        }
    }

    if (isset($_POST['new']) && $getFilter !== '') {
        $getActive = deepSanitizeInput($_POST['active'], 'url');
        if (!validateInput($getActive, 'yn')) {
            return getHtmlMessage(__('dievalidate99'), 'error');
        }
        $sql = "INSERT INTO user_filters (username, filter, active) VALUES ('" . safe_value(stripslashes($user->username)) . "','" . safe_value(stripslashes($getFilter)) . "','" . safe_value($getActive) . "')";
        dbquery($sql);
        if (DEBUG === true) {
            echo $sql;
        }
    }

    if (isset($_GET['delete'], $_GET['filter'])) {
        $getFilter = deepSanitizeInput($_GET['filter'], 'url');
        if (!validateInput($getFilter, 'email') && !validateInput($getFilter, 'host')) {
            return getHtmlMessage(__('dievalidate99'), 'error');
        }
        $sql = "DELETE FROM user_filters WHERE username='" . safe_value(stripslashes($user->username)) . "' AND filter='" . safe_value(stripslashes($getFilter)) . "'";
        dbquery($sql);
        if (DEBUG === true) {
            echo $sql;
        }
    }
    if (isset($_GET['change_state'], $_GET['filter'])) {
        $getFilter = deepSanitizeInput($_GET['filter'], 'url');
        if (!validateInput($getFilter, 'email') && !validateInput($getFilter, 'host')) {
            return getHtmlMessage(__('dievalidate99'), 'error');
        }
        $sql = "SELECT active FROM user_filters WHERE username='" . safe_value(stripslashes($user->username)) . "' AND filter='" . safe_value(stripslashes($getFilter)) . "'";
        $result = dbquery($sql);
        $row = $result->fetch_row();
        $active = 'Y';
        if ($row[0] === 'Y') {
            $active = 'N';
        }
        $sql = "UPDATE user_filters SET active='" . $active . "' WHERE username='" . safe_value(stripslashes($user->username)) . "' AND filter='" . safe_value(stripslashes($getFilter)) . "'";
        dbquery($sql);
    }
    $sql = "SELECT filter, CASE WHEN active='Y' THEN '" . __('yes12') . "' ELSE '" . __('no12') . "' END AS active, CONCAT('<a href=\"javascript:delete_filter\(\'" . safe_value($user->id) . "\',',QUOTE(filter),'\)\" class=\"btn btn-sm btn-wr-delete\">" . __('delete12') . "</a>&nbsp;&nbsp;<a href=\"javascript:change_state(\'" . safe_value($user->id) . "\',',QUOTE(filter),')\" class=\"btn btn-sm btn-wr-yellow px-2\">" . __('toggle12') . "</a>') AS actions FROM user_filters WHERE username='" . safe_value(stripslashes($user->username)) . "'";
    $result = dbquery($sql);
    $returnString = '';
    $returnString .= '<div class="d-flex flex-wrap w-100 mb-3">';
    
    if ($_SESSION['user_type'] === 'A' || ($_SESSION['user_type'] === 'D' && stripslashes($user->username) !== stripslashes($_SESSION['myusername']))) {
        $returnString .= '<div class="card card-wr mr-3 mb-2 " style="max-width: 500px;">';
        $returnString .= '<div class="card-header card-header-warriors">
                            <i class="fas fa-user"></i> ';
        $returnString .= __('userfilter12') . ' ' . $user->username;
        $returnString .= '</div>';
        $returnString .= '<div class="card-body card-body-warriors">';
        $returnString .= '<form METHOD="POST" ACTION="user_manager.php">';
        $returnString .= '<INPUT TYPE="HIDDEN" NAME="action" VALUE="filters">';
        $returnString .= '<INPUT TYPE="HIDDEN" NAME="token" VALUE="' . $_SESSION['token'] . '">';
        $returnString .= '<INPUT TYPE="HIDDEN" NAME="id" VALUE="' . $user->id . '">';
        $returnString .= '<INPUT TYPE="HIDDEN" NAME="formtoken" VALUE="' . generateFormToken('/user_manager.php filter token') . '">';
        $returnString .= '<INPUT TYPE="hidden" NAME="new" VALUE="true">';
        $returnString .= '	<div class="row px-3">';    
        $returnString .= '	    <div class="col-6 px-1">';
        $returnString .= '          <div class="form-group">';
        $returnString .= '              <label class="small w-label">' . __('filter12') . '</label>';
        $returnString .= '              <input TYPE="text" NAME="filter" class="form-control w-input-text" />';
        $returnString .= '          </div>';
        $returnString .= '	    </div>';        
        $returnString .= '	    <div class="col-3 px-1">';
        $returnString .= '          <div class="form-group">';
        $returnString .= '              <label class="small w-label">' . __('active12') . '</label>';
        $returnString .= '              <SELECT NAME="active" class="form-control custom-select w-input-text" ><OPTION VALUE="Y">' . __('yes12') . '<OPTION VALUE="N">' . __('no12') . '</SELECT>';
        $returnString .= '          </div>';
        $returnString .= '	    </div>';        
        $returnString .= '	    <div class="col-3 px-1">';
        $returnString .= '          <div class="form-group">';
        $returnString .= '              <label class="small w-label">' . __('action12') . '</label>';
        $returnString .= '              <button TYPE="submit" VALUE="' . __('add12') . '" class="pt-2 btn btn-block btn-wr-red font-weight-bold btn-sm">';
        $returnString .= __('add12');
        $returnString .= '              </button>';
        $returnString .= '          </div>';
        $returnString .= '	    </div>';        
        $returnString .= '	</div>';
        $returnString .= '</form>';
        $returnString .= '</div>';
        $returnString .= '</div>';
    }

    $returnString .= '<div class="card card-wr" style="width: 600px;">';
    $returnString .= '<div class="card-header card-header-warriors">
                        <i class="fas fa-filter"></i> ';
    $returnString .= __('userfilter12') . ' ' . $user->username;
    $returnString .= '</div>';
    $returnString .= '<div class="card-body card-body-warriors pt-0">';

    $returnString .= '<form METHOD="POST" ACTION="user_manager.php">';
    $returnString .= '<INPUT TYPE="HIDDEN" NAME="action" VALUE="filters">';
    $returnString .= '<INPUT TYPE="HIDDEN" NAME="token" VALUE="' . $_SESSION['token'] . '">';
    $returnString .= '<INPUT TYPE="HIDDEN" NAME="id" VALUE="' . $user->id . '">';
    $returnString .= '<INPUT TYPE="HIDDEN" NAME="formtoken" VALUE="' . generateFormToken('/user_manager.php filter token') . '">';
    $returnString .= '<INPUT TYPE="hidden" NAME="new" VALUE="true">';
    $returnString .= '	<div class="row ">';    
    $returnString .= '	    <div class="col-12">';
    $returnString .= '	    <div class="table-responsive table-res-wr">';
    $returnString .= '          <table class="table table-sm table-striped table-wr table-wr-info">';
    $returnString .= '<thead>';
    $returnString .= '<tr>';
    $returnString .= '<th class="m-0 py-1 px-2 ">' . __('filter12');
    $returnString .= '</th>';
    $returnString .= '<th class="m-0 py-1 px-2 ">' . __('active12');
    $returnString .= '</th>';
    $returnString .= '<th class="m-0 py-1 px-2 ">' . __('action12');
    $returnString .= '</th>';
    $returnString .= '</tr>';
    $returnString .= '</thead>';
    $returnString .= '                  <tbody class="table-body-warriors">';
    while ($row = $result->fetch_object()) {
        $returnString .= ' <TR><TD>' . $row->filter . '</TD><TD>' . $row->active . '</TD> ';
        if ($_SESSION['user_type'] === 'D' && stripslashes($user->username) === stripslashes($_SESSION['myusername'])) {
            $returnString .= '<TD>' . __('nofilteraction12') . '</TD></TR>' . PHP_EOL;
        } else {
            $returnString .= '<TD>' . $row->actions . '</TD></TR>' . PHP_EOL;
        }
    }
    $returnString .= '                  </tbody>';
    $returnString .= '          </table>';
    $returnString .= '	    </div>';    
    $returnString .= '	    </div>';        
    $returnString .= '	</div>';
    $returnString .= '</form>';
    $returnString .= '</div>';
    $returnString .= '</div>';
    $returnString .= '</div>';

    return $returnString;
}

function sendReport()
{
    include_once __DIR__ . '/quarantine_report.inc.php';
    $requirementsCheck = Quarantine_Report::check_quarantine_report_requirements();
    if ($requirementsCheck !== true) {
        error_log('Requirements for sending quarantine reports not met: ' . $requirementsCheck);
        return getHtmlMessage(__('checkReportRequirementsFailed12'), 'error');
    }

    if (is_string($user = getUserById())) {
        return $user;
    }

    if (is_string($membertest = testSameDomainMembership($user->username, 'report'))) {
        return $membertest;
    }

    $quarantine_report = new Quarantine_Report();
    $reportResult = $quarantine_report->send_quarantine_reports(array($user->username), true);
    if ($reportResult === -2) {
        return getHtmlMessage(__('noReportsEnabled12'), 'error');
    }

    if ($reportResult['succ'] > 0) {
        return getHtmlMessage(__('quarantineReportSend12'), 'success');
    }

    return getHtmlMessage(__('quarantineReportFailed12'), 'error');
}

function logoutUser()
{
    if (is_string($tokentest = testToken())) {
        return $tokentest;
    }

    if (is_string($user = getUserById())) {
        return $user;
    }

    if (is_string($membertest = testSameDomainMembership($user->username, 'logout'))) {
        return $membertest;
    }

    if (is_string($permissiontest = testPermissions($user->username, $user->type, ''))) {
        return $permissiontest;
    }

    $sql = "UPDATE users SET login_expiry='-1' WHERE id='$user->id'";
    dbquery($sql);
    if (DEBUG === true) {
        echo $sql;
    }

    return getHtmlMessage(sprintf(__('userloggedout12'), stripslashes($user->username)), 'success');
}

?>
    <script>
        function checkPasswords() {
            var pass0 = document.getElementById("password");
            var pass1 = document.getElementById("retypepassword");
            pass0.classList.remove("inputerror");
            pass1.classList.remove("inputerror");
            if (pass0.value !== pass1.value) {
                var errorDiv = document.getElementById("formerror");
                var errormsg = errorDiv.innerHTML;
                errorDiv.innerHTML = errormsg + "<?php echo __('errorpass12');?><br>";
                errorDiv.classList.remove("hidden");
                pass0.classList.add("inputerror");
                pass1.classList.add("inputerror");
                return false;
            } else {
                return true;
            }
        }

        function requiredFields() {
            var valid = true;
            var error = "";
            var username = document.getElementById("username");
            var pass0 = document.getElementById("password");
            username.classList.remove("inputerror");
            pass0.classList.remove("inputerror");
            if (username.value === "") {
                error = error + "<?php echo __('erroruserreq12');?><br>";
                username.classList.add("inputerror");
                valid = false;
            }
            if (pass0.value === "") {
                error = error + "<?php echo __('errorpwdreq12');?><br>";
                pass0.classList.add("inputerror");
                valid = false;
            }
            if (valid === false) {
                var errorDiv = document.getElementById("formerror");
                var errormsg = errorDiv.innerHTML;
                errorDiv.innerHTML = errormsg + error;
                errorDiv.classList.remove("hidden");
            }
            return valid;
        }

        function validateForm() {
            var errorDiv = document.getElementById("formerror");
            errorDiv.innerHTML = "";
            errorDiv.classList.add("hidden");
            var required = requiredFields();
            var checkpwd = checkPasswords();
            return !(checkpwd === false || required === false);
        }
    </script>
<?php
if ($_SESSION['user_type'] === 'A' || $_SESSION['user_type'] === 'D') {
    ?>
    <script type="text/javascript">
        
        function delete_user(id, name) {
            var yesno = confirm("<?php echo ' ' . __('areusuredel12') . ' '; ?>" + name + "<?php echo __('questionmark12'); ?>");
            if (yesno === true) {
                window.location = "?token=" + "<?php echo $_SESSION['token']; ?>" + "&action=delete&id=" + id;
            } else {
                window.location = "?token=" + "<?php echo $_SESSION['token']; ?>";
            }
        }

        function delete_filter(id, filter) {
            var yesno = confirm("<?php echo __('sure12'); ?>");
            if (yesno === true) {
                window.location = "?token=" + "<?php echo $_SESSION['token']; ?>" + "&action=filters&id=" + id + "&filter=" + filter + "&delete=true";
            } else {
                window.location = "?token=" + "<?php echo $_SESSION['token']; ?>" + "&action=filters&id=" + id;
            }
        }

        function change_state(id, filter) {
            var yesno = confirm("<?php echo __('sure12'); ?>");
            if (yesno === true) {
                window.location = "?token=" + "<?php echo $_SESSION['token']; ?>" + "&action=filters&id=" + id + "&filter=" + filter + "&change_state=true";
            } else {
                window.location = "?token=" + "<?php echo $_SESSION['token']; ?>" + "&action=filters&id=" + id;
            }
        }

        function logout_user(id, name) {
            var yesno = confirm("<?php echo ' ' . __('logout12') . ' '; ?>" + name + "<?php echo __('questionmark12'); ?>");
            if (yesno === true) {
                window.location = "?token=" + "<?php echo $_SESSION['token']; ?>" + "&action=logout&id=" + id;
            } else {
                window.location = "?token=" + "<?php echo $_SESSION['token']; ?>";
            }
        }

        
    </script>
    <?php
    if (isset($_POST['action'])) {
        $action = deepSanitizeInput($_POST['action'], 'url');
    } elseif (isset($_GET['action'])) {
        $action = deepSanitizeInput($_GET['action'], 'url');
    }
    if (isset($action)) {
        if ($action !== 'sendReportNow' && !validateInput($action, 'action')) {
            die(getHtmlMessage(__('dievalidate99'), 'error'));
        }
        switch ($action) {
            case 'new':
                echo newUser($_SESSION['user_type']);
                break;
            case 'edit':
                echo editUser($_SESSION['user_type']);
                break;
            case 'delete':
                echo deleteUser();
                break;
            case 'filters':
                echo userFilter();
                break;
            case 'sendReportNow':
                echo sendReport();
                break;
            case 'logout':
                echo logoutUser();
                break;
        }
    }

    echo '<div class="flex-lg-row">';
    echo '<a 
            class="btn btn-wr-red btn-sm" 
            style="color: #fff;" 
            href="?token=' . $_SESSION['token'] . '&amp;action=new"
            >
            <i class="fas fa-user-plus"></i>  ' . __('newuser12') . '</a>';
    echo '</div>';

    $domainAdminUserDomainFilter = '';
    if ($_SESSION['user_type'] === 'D') {
        if ($_SESSION['domain'] === '') {
            //if the domain admin has no domain set we assume he should see only users that has no domain set (no mail as username)
            $domainAdminUserDomainFilter = 'WHERE username NOT LIKE "%@%" AND type <> "A"';
        } else {
            $sql = "SELECT filter FROM user_filters WHERE username = '" . safe_value(stripslashes($_SESSION['myusername'])) . "'";
            $result = dbquery($sql);
            $domainAdminUserDomainFilter = 'WHERE (username LIKE "%@' . $_SESSION['domain'] . '" AND type <> "A")';
            for ($i = 0; $i < $result->num_rows; $i++) {
                $filter = $result->fetch_row();
                $domainAdminUserDomainFilter .= ' OR (username LIKE "%@' . safe_value(stripslashes($filter[0])) . '" AND type = "U")';
            }
        }
    }

    $btn_i_edit = '';    
    $btn_i_edit = '';
    $btn_i_edit = '';
    $btn_i_edit = '';

    $sql = "
        SELECT
          username AS '" . safe_value(__('username12')) . "',
          fullname AS '" . safe_value(__('fullname12')) . "',
        CASE
          WHEN type = 'A' THEN '" . __('admin12') . "'
          WHEN type = 'D' THEN '" . __('domainadmin12') . "'
          WHEN type = 'U' THEN '" . __('user12') . "'
          WHEN type = 'R' THEN '" . __('userregex12') . "'
        ELSE
          '" . __('unknowtype12') . "'
        END AS '" . safe_value(__('type12')) . "',
        CASE
          WHEN noscan = 1 THEN '" . __('noshort12') . "'
          WHEN noscan = 0 THEN '" . __('yesshort12') . "'
        ELSE
          '" . __('yesshort12') . "'
        END AS '" . safe_value(__('spamcheck12')) . "',
          spamscore AS '" . safe_value(__('spamscore12')) . "',
          highspamscore AS '" . safe_value(__('spamhscore12')) . "',
        CASE
          WHEN login_expiry > " . time() . " OR login_expiry = 0 THEN '" . safe_value(__('yes12')) . "'
        ELSE 
          '" . safe_value(__('no12')) . "'
        END AS '" . safe_value(__('loggedin12')) . "',
        CASE
WHEN login_expiry > " . time() . " OR login_expiry = 0 THEN CONCAT('<a class=\"btn btn-sm btn-wr-edit\" href=\"?token=" . $_SESSION['token'] . "&amp;action=edit&amp;id=',id,'\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . safe_value(__('edit12')) . "\"><i class=\"fas fa-edit\"></i></a>&nbsp;&nbsp;<a class=\"btn btn-sm btn-wr-delete\" href=\"javascript:delete_user(\'',id,'\',',QUOTE(username),')\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . safe_value(__('delete12')) . "\"><i class=\"fas fa-trash\"></i></a>&nbsp;&nbsp;<a class=\"btn btn-sm btn-wr-filters\" href=\"?token=" . $_SESSION['token'] . "&amp;action=filters&amp;id=',id,'\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . safe_value(__('filters12')) . "\"><i class=\"fas fa-filter\"></i></a>&nbsp;&nbsp;<a class=\"btn btn-sm btn-wr-logout\" href=\"javascript:logout_user(\'',id,'\',',QUOTE(username),')\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . safe_value(__('logout12')) . "\"><i class=\"fas fa-sign-out-alt\"></i></a>')
        ELSE
          CONCAT('<a class=\"btn btn-sm btn-wr-edit\" href=\"?token=" . $_SESSION['token'] . "&amp;action=edit&amp;id=',id,'\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . safe_value(__('edit12')) . "\"><i class=\"fas fa-edit\"></i></a>&nbsp;&nbsp;<a class=\"btn btn-sm btn-wr-delete\" href=\"javascript:delete_user(\'',id,'\',',QUOTE(username),')\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . safe_value(__('delete12')) . "\"><i class=\"fas fa-trash\"></i></a>&nbsp;&nbsp;<a class=\"btn btn-sm btn-wr-filters\" href=\"?token=" . $_SESSION['token'] . "&amp;action=filters&amp;id=',id,'\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . safe_value(__('filters12')) . "\"><i class=\"fas fa-filter\"></i></a>')
        END AS '" . safe_value(__('action12')) . "'
        FROM
          users " . $domainAdminUserDomainFilter . ' 
        ORDER BY
          username';

// WHEN login_expiry > " . time() . " OR login_expiry = 0 THEN CONCAT('<a class=\"btn btn-sm btn-wr-white\" href=\"?token=" . $_SESSION['token'] . "&amp;action=edit&amp;id=',id,'\">" . safe_value(__('edit12')) . "</a>&nbsp;&nbsp;<a class=\"btn btn-sm btn-wr-delete\" href=\"javascript:delete_user(\'',id,'\',',QUOTE(username),')\">" . safe_value(__('delete12')) . '</a>&nbsp;&nbsp;<a class="btn btn-sm btn-wr-blue" href="?token=' . $_SESSION['token'] . "&amp;action=filters&amp;id=',id,'\">" . safe_value(__('filters12')) . "</a>&nbsp;&nbsp;<a class=\"btn btn-sm btn-wr-yellow\" href=\"javascript:logout_user(\'',id,'\',',QUOTE(username),')\">" . safe_value(__('logout12')) . "</a>')
//         ELSE
//           CONCAT('<a class=\"btn btn-sm btn-wr-white\" href=\"?token=" . $_SESSION['token'] . "&amp;action=edit&amp;id=',id,'\">" . safe_value(__('edit12')) . "</a>&nbsp;&nbsp;<a class=\"btn btn-sm btn-wr-delete\" href=\"javascript:delete_user(\'',id,'\',',QUOTE(username),')\">" . safe_value(__('delete12')) . '</a>&nbsp;&nbsp;<a class="btn btn-sm btn-wr-blue" href="?token=' . $_SESSION['token'] . "&amp;action=filters&amp;id=',id,'\">" . safe_value(__('filters12')) . "</a>')
//         END AS '" . safe_value(__('action12')) . "'
//         FROM
//           users " . $domainAdminUserDomainFilter . ' 
//         ORDER BY
//           username';
        
    
        echo '<div class="card card-wr">
                <div class="card-header card-header-warriors">
                    <i class="far fa-list-alt pr-2"></i>';
        echo __('usermgnt12');            
        echo'   </div>
                <div class="card-body card-body-warriors">';
        dbtable_v2($sql, null, false, false, __('usermgnt12'));
        echo '</div></div>';
         
       
} elseif (!isset($_POST['submit'])) {
    $sql = "SELECT id, username, fullname, type, quarantine_report, spamscore, highspamscore, noscan, quarantine_rcpt FROM users WHERE username='" . safe_value(stripslashes($_SESSION['myusername'])) . "'";
    $result = dbquery($sql);
    $row = $result->fetch_object();
    $quarantine_report = '';
    if ((int)$row->quarantine_report === 1) {
        $quarantine_report = 'checked="checked"';
    }

    $noscan = '';
    if ((int)$row->noscan === 0) {
        $noscan = 'checked="checked"';
    }
    $s[$row->type] = 'selected';
    echo '<div id="formerror" class="hidden"></div>';
    echo '<form method="post" action="user_manager.php" onsubmit="return checkPasswords();">' . PHP_EOL;
    echo '<INPUT TYPE="HIDDEN" NAME="token" VALUE="' . $_SESSION['token'] . '">' . PHP_EOL;
    echo '<input type="hidden" name="action" value="edit">' . PHP_EOL;
    echo '<input type="hidden" name="id" value="' . $row->id . '">' . PHP_EOL;
    echo '<input type="hidden" name="submit" value="true">' . PHP_EOL;
    echo '<INPUT TYPE="HIDDEN" NAME="formtoken" VALUE="' . generateFormToken('/user_manager.php user token') . '">' . PHP_EOL;
    echo '<table class="mail useredit" border="0" cellpadding="1" cellspacing="1">' . PHP_EOL;
    echo ' <tr><td class="heading" colspan=2 align="center">' . __('edituser12') . ' ' . $row->username . '</td></tr>' . PHP_EOL;
    echo ' <tr><td class="heading">' . __('username0212') . '</td><td>' . stripslashes($_SESSION['myusername']) . '</td></tr>' . PHP_EOL;
    echo ' <tr><td class="heading">' . __('name12') . '</td><td>' . $_SESSION['fullname'] . '</td></tr>' . PHP_EOL;
    if ($_SESSION['user_ldap'] !== true && $_SESSION['user_imap'] !== true) {
        echo ' <tr><td class="heading">' . __('password12') . '</td><td><input type="password" id="password" name="password" value="xxxxxxxx" AUTOCOMPLETE="off"></td></tr>' . PHP_EOL;
        echo ' <tr><td class="heading">' . __('retypepassword12') . '</td><td><input type="password" id="retypepassword" name="password1" value="xxxxxxxx" AUTOCOMPLETE="off"></td></tr>' . PHP_EOL;
    }
    echo ' <tr><td class="heading">' . __('quarrep12') . '</td><td><input type="checkbox" name="quarantine_report" value="on" ' . $quarantine_report . '> <span class="font-1em">' . __('senddaily12') . '</span> <button type="submit" name="action" value="sendReportNow">' . __('sendReportNow12') . '</button></td></tr>' . PHP_EOL;
    echo ' <tr><td class="heading">' . __('quarreprec12') . '</td><td><input type="text" name="quarantine_rcpt" value="' . $row->quarantine_rcpt . '"><br><span class="font-1em">' . __('overrec12') . '</span></td>' . PHP_EOL;
    echo ' <tr><td class="heading">' . __('scanforspam12') . '</td><td><input type="checkbox" name="noscan" value="on" ' . $noscan . '> <span class="font-1em">' . __('scanforspam212') . '</span></td></tr>' . PHP_EOL;
    echo ' <tr><td class="heading">' . __('pontspam12') . '</td><td><input type="text" name="spamscore" value="' . $row->spamscore . '" size="4"> <span class="font-1em">0=' . __('usedefault12') . '</span></td></tr>' . PHP_EOL;
    echo ' <tr><td class="heading">' . __('hpontspam12') . '</td><td><input type="text" name="highspamscore" value="' . $row->highspamscore . '" size="4"> <span class="font-1em">0=' . __('usedefault12') . '</span></td></tr>' . PHP_EOL;
    echo '<tr><td class="heading">' . __('action_0212') . '</td><td><input type="reset" value="' . __('reset12') . '">&nbsp;&nbsp;<input type="submit" name="action" value="' . __('update12') . '"></td></tr>' . PHP_EOL;
    echo '</table></form><br>' . PHP_EOL;
    $sql = "SELECT filter, active FROM user_filters WHERE username='" . $row->username . "'";
    $result = dbquery($sql);
} else {
    if (false === checkToken($_POST['token'])
        || false === checkFormToken('/user_manager.php user token', $_POST['formtoken'])) {
        header('Location: login.php?error=pagetimeout');
        die();
    }
    if (!isset($_POST['action'])) {
        echo getHtmlMessage(__('formerror12'), 'error');
    } elseif ($_POST['action'] === 'sendReportNow') {
        include_once __DIR__ . '/quarantine_report.inc.php';
        $requirementsCheck = Quarantine_Report::check_quarantine_report_requirements();
        if ($requirementsCheck !== true) {
            echo getHtmlMessage(__('checkReportRequirementsFailed12'), 'error');
            error_log('Requirements for sending quarantine reports not met: ' . $requirementsCheck);
        } elseif (!isset($_POST['quarantine_report']) || $_POST['quarantine_report'] !== 'on') {
            echo getHtmlMessage(__('noReportsEnabled12'), 'error');
        } else {
            $quarantine_report = new Quarantine_Report();
            $reportResult = $quarantine_report->send_quarantine_reports(array($_SESSION['myusername']));
            if ($reportResult['succ'] === 1) {
                echo getHtmlMessage(__('quarantineReportSend12'), 'error');
            } else {
                echo getHtmlMessage(__('quarantineReportFailed12'), 'error');
            }
        }
    } elseif (isset($_POST['password'], $_POST['password1']) && ($_POST['password'] !== $_POST['password1'])) {
        echo getHtmlMessage(__('errorpass12'), 'error');
    } else {
        $username = safe_value(stripslashes($_SESSION['myusername']));
        if (isset($_POST['password'])) {
            $n_password = safe_value($_POST['password']);
        }
        $spamscore = deepSanitizeInput($_POST['spamscore'], 'float');
        if (!validateInput($spamscore, 'float')) {
            $spamscore = '0';
        }
        $highspamscore = deepSanitizeInput($_POST['highspamscore'], 'float');
        if (!validateInput($highspamscore, 'float')) {
            $highspamscore = '0';
        }
        $n_quarantine_report = '1';
        if (!isset($_POST['quarantine_report'])) {
            $n_quarantine_report = '0';
        }
        $noscan = '0';
        if (!isset($_POST['noscan'])) {
            $noscan = '1';
        }
        $quarantine_rcpt = deepSanitizeInput($_POST['quarantine_rcpt'], 'string');
        if ($quarantine_rcpt !== '' && !validateInput($quarantine_rcpt, 'user')) {
            die(getHtmlMessage(__('dievalidate99'), 'error'));
        }

        if (isset($_POST['password']) && $_POST['password'] !== 'XXXXXXXX') {
            // Password reset required
            $password = password_hash($n_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password='" . $password . "', quarantine_report='$n_quarantine_report', spamscore='$spamscore', highspamscore='$highspamscore', noscan='$noscan', quarantine_rcpt='$quarantine_rcpt' WHERE username='$username'";
            dbquery($sql);
        } else {
            $sql = "UPDATE users SET quarantine_report='$n_quarantine_report', spamscore='$spamscore', highspamscore='$highspamscore', noscan='$noscan', quarantine_rcpt='$quarantine_rcpt' WHERE username='$username'";
            dbquery($sql);
        }

        // Audit
        audit_log(sprintf(__('auditlog0512', true), $username));
        echo getHtmlMessage(__('savedsettings12'), 'success');
    }
}

echo '</div>';
echo '</div>';

// Add footer
html_end_new(); 

// Close any open db connections
dbclose();