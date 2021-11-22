<?php

/***************************************************
SQLgrey Web Interface
Filename:	config.inc.php
Purpose: 	Configuration of database and options
Version: 	1.1.8
****************************************************/

/* Database settings */
#$db_hostname	= "/var/lib/sqlgrey/sqlgrey.db";  // path to dbfile for sqlite
$db_hostname 	= "localhost";
$db_db		= "sqlgrey";
$db_user        = "sqlgrey";
$efa_array = preg_grep('/^SQLGREYSQLPWD/', file('/etc/eFa/SQLGrey-Config'));
foreach($efa_array as $num => $line) {
  if ($line) {
    $db_pass = chop(preg_replace('/^SQLGREYSQLPWD:(.*)/','$1',$line));
  }
}
$db_type	= "mysql";	// "mysql", "pg" (postgresql) or experimental "sqlite"

/* Set close_btn to 'yes' to enable the close button in index.php (main menu)
   the button action = ../ which could be a security issue
   default = "no"
*/
$close_btn	= "no";

/* Set no_millisecs to 'no' if your server's dbase shows milliseconds
   and you do want these to be displayed - this will take two lines per entry.
   Also set this to 'no' if you encounter problems with displaying the timestamps
   ('no' used to be the default and leaves the date format untouched).
   When set to 'yes' timestamps will be formatted as 'yyyy-mm-dd hh:mm:ss'
   which doubles the amount of visible entries.
   default = "yes"
*/
$no_millisecs	= "yes";

/* Depending on your PHP version you may have to set default timezone to avoid warnings.
   Remove the comment (//) and change the default to your region.
   See http://www.php.net/manual/en/timezones.php to determine the syntax of your region.
   Examples are: 'America/Los_Angeles', 'Europe/Berlin' etc.
   default = 'UTC'
*/
//date_default_timezone_set('UTC');

?>
