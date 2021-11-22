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

require_once __DIR__ . '/functions.php';

disableBrowserCache();
session_regenerate_id(true);

$_SESSION['token'] = generateToken();

if (file_exists('conf.php') && isset($_GET['error'])) {

  $loginerror = deepSanitizeInput($_GET['error'], 'url');
  if (false === validateInput($loginerror, 'loginerror')) {
    header('Location: login.php');
  }
}

?>

<!doctype html>
<html>

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="shortcut icon" href="public/images/favicon.png">
  <link href="public/lib/Bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="public/css/styles.css" rel="stylesheet" />
  <link href="public/css/warriors-style.css" rel="stylesheet" />

  <title><?php echo __('mwloginpage01') ?></title>
</head>

<body>
  <script>
    setInterval(function() {
      var len1 = document.getElementById("myusername").value.length;
      var len2 = document.getElementById("mypassword").value.length;

      var prev1 = document.getElementById("myusername_length").value;
      var prev2 = document.getElementById("mypassword_length").value;

      if (len1 === prev1 && len2 === prev2) {
        location.reload();
      } else {
        document.getElementById("myusername_length").value = len1;
        document.getElementById("mypassword_length").value = len2;
      }
    }, 60000);
    //if session could be timed out display a message to reload the page and hide login form
    function enableTimeoutNotice(timeout) {
      setTimeout(function() {
        timeoutnotice = document.getElementById("sessiontimeout");
        timeoutnotice.setAttribute("class", timeoutnotice.getAttribute("class").replace("hidden", ""));
        loginfieldset = document.getElementById("loginfieldset");
        loginfieldset.setAttribute("class", loginfieldset.getAttribute("class") + " hidden");
      }, timeout * 1000 * 0.95);
    };
    <?php ((defined('SESSION_TIMEOUT') && SESSION_TIMEOUT > 0) ? 'enableTimeoutNotice(' . SESSION_TIMEOUT . ');' : ''); ?>
  </script>

  <div class="bg-login-warriors">
    <div id="layoutAuthentication">
      <div id="layoutAuthentication_content">
        <main>
          <div class="container">
            <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
                <div class="card card-wr" style="width: 400px;">
                  <div class="card-header card-header-warriors text-center">
                    <img class="py-2 w-50" src="public/images/logon.png">
                  </div>
                  <div class="card-body">

                    <?php
                    if (isset($_GET['error'])) {
                      $error = __('errorund01');
                      switch ($loginerror) {
                        case 'baduser':
                          $error = __('badup01');
                          break;
                        case 'emptypassword':
                          $error = __('emptypassword01');
                          break;
                        case 'timeout':
                          $error = __('sessiontimeout01');
                          break;
                        case 'pagetimeout':
                          $error = __('pagetimeout01');
                          break;
                        case 'token':
                          $error = "Error de token";
                          break;
                      }
                      echo '
                            <div class="alert alert-danger" role="alert">
                              ' . $error . '
                            </div>
                          ';
                    }
                    ?>
                    <form name="loginform" class="loginform" method="post" action="checklogin.php">
                      <div class="form-group">
                        <label class="small mb-1 w-label" for="myusername"><?php echo __('username'); ?></label>
                        <input class="form-control py-4 w-input-text" id="myusername" type="text" placeholder="<?php echo __('username'); ?>" name="myusername" autofocus />
                        <input type="hidden" id="myusername_length" name="myusername_length">
                      </div>
                      <div class="form-group">
                        <label class="small mb-1 w-label" for="mypassword"><?php echo __('password'); ?></label>
                        <input class="form-control py-4 w-input-text" name="mypassword" type="password" id="mypassword" placeholder="<?php echo __('password'); ?>" />
                        <input type="hidden" id="mypassword_length" name="mypassword_length">
                      </div>
                      <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                        <a></a>
                        <button type="submit" name="Submit" value="loginSubmit" class="btn btn-wr-red w-50 font-weight-bold">
                          <?php echo __('login01'); ?>
                        </button>
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                      </div>
                    </form>
                    <?php

                    if (defined('PWD_RESET') && PWD_RESET === true) {
                      echo '
                            <div class="pwdresetButton">
                              <a href="password_reset.php?stage=1">' . __('forgottenpwd01') . '</a>
                            </div>';
                    }
                    ?>
                  </div>
                  <div class="card-footer text-muted text-center">
                    <?php echo __('footer03') . '- &copy; 2006-' . date('Y'); ?>
                  </div>
                </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>

  <script src="public/lib/JQuery/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
  <script src="public/lib/Bootstrap/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>