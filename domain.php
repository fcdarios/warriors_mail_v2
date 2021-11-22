<?php
header('Location: status.php');
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

require __DIR__ . '/login.function.php';

html_head(__('domain03'), 0, false, false);


html_body('domain');

echo '<div class="container-fluid">';
   echo '<ol class="breadcrumb my-4 title_page">';
   echo '<li class="breadcrumb-item title_page_li">'.__('domain03').'</li>';
   echo '</ol>';
echo '<div class="row">';


if ($_SESSION['user_type'] == 'A' || $_SESSION['user_type'] === 'D') {

   function service($action)
   {
      $archive = "/var/www/html/mailscanner/status/daemon.txt";
      $open = fopen($archive,'w');
      fwrite($open,$action);
      fclose($open);
   }
   
   if (isset($_POST['save']))
   {

      $domain = $_POST['domain'];
      $delete_domain = $_POST['delete'];

      function addDomain($domain)
      {	
         $domain = trim($domain);
         $cat = shell_exec('cp /etc/MailScanner/domain/filename.rules.conf /etc/MailScanner/domain/filename.rules.'.$domain.'.conf');
         $permisos = shell_exec('chmod 666 /etc/MailScanner/domain/filename.rules.'.$domain.'.conf');
         $nuevaLinea = "FromOrTo: *@".$domain." /etc/MailScanner/domain/filename.rules.".$domain.".conf";
         $archive = "/etc/MailScanner/filename.rules";
         $open = fopen($archive,'r');
         $contenido = fread($open,filesize($archive));
         fclose($open);
         $contenido = explode("\n",$contenido);
         array_pop($contenido);
         array_push($contenido,$nuevaLinea); 
         $open = fopen($archive,'w');
         for ($i = 0; $i < count($contenido); $i++)
         {
            fwrite($open,$contenido[$i].PHP_EOL);
         }
         fclose($open);
         $action = "reload";
         service($action);
      }
   
   
      function deleteDomain($delete_domain)
      {
         $deleteFile = shell_exec('rm -fr /etc/MailScanner/domain/filename.rules.'.$delete_domain.'.conf');
         $archive = "/etc/MailScanner/filename.rules";
         $open = fopen($archive,'r');
         $contenido = fread($open,filesize($archive));
         fclose($open);
         $contenido = explode("\n",$contenido);
         for ($i = 0; $i < count($contenido); $i++)
         {
            $find = strpos($contenido[$i], $delete_domain);
            if ($find != 0 )
            {
               unset($contenido[$i]);
            }
         }
         $open = fopen($archive,'w');
         for ($i = 0; $i < count($contenido); $i++)
         {
            if (!empty($contenido[$i]))
            {
               fwrite($open,$contenido[$i].PHP_EOL);	
            }
            
         }
         fclose($open);
         
         $action = "reload";
         service($action);
   
      }
   
      if ($domain != "")
      {
          addDomain($domain);
      }
      if ($delete_domain != "")
      {
         deleteDomain($delete_domain);
      }
   }
   
   function dominios()
   {
      $dominios = shell_exec('ls /etc/MailScanner/domain/ | grep "filename.rules.*.conf"');
      $dominio = explode("\n",$dominios);
      array_pop($dominio);
      echo '<div class="table-responsive">';
      echo '<table  role="table" id="wr-table" class="dark-table warrior-tooltable table_sortable {sortlist: [[0,0]]}" width="100%" cellspacing="0">    
               <tr class="wr-tabletr">
                  <th class="wr-tableheading2">Domain</th>
                  <th class="wr-tableheading2">Action</th>
               </tr>';
      for ($i = 0; $i < count($dominio); $i++)
      {
      
         if ($dominio[$i] == "filename.rules.allowall.conf" or $dominio[$i] == "filename.rules.conf" )
         {
   
         }
         else
         {
            $aux = str_replace("filename.rules.","",$dominio[$i]);
            $aux = str_replace(".conf","",$aux);
            echo '<form action="domain.php" method="POST">
                     <tr class="wr-tabletr">
                        <td>
                           <input type="hidden" name="delete" value="'.$aux.'">
                           <a href="domain_details.php?archive='.$dominio[$i].'">'.$aux.'</a>
                        </td>
                        <td>
                           <button class="btn btn-danger" type="submit" name="save"><i class="fas fa-trash-alt mr-1"></i> Delete</button>
                        </td>
                     </tr>
                  </form>';
         }
         
      }
      echo "</table></div>";
   }
   
   
   echo '<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">';
   echo     '<div class="card card-wr">';
   echo        '<div class="card-header card-header-warriors">';
   echo           '<i class="fas fa-plus mr-1"></i>';
   echo              'Add Domain';
   echo        '</div>';
   echo        '<div class="card-body card-body-warriors">';

               echo '<div class="template__table_static template__table_responsive">';
               echo '<div class="scrollable scrollbar-macosx">';     
               echo '<form action="domain.php" method="POST">';
               echo '<div class="row">';
               echo '<div class="col-xs-1 d-flex align-items-center justify-content-center">
                        <label>
                           <strong>@</strong>
                        </label>
                     </div>';
               echo '<div class="col-xl-6 col-lg-6 col-md-6 col-sm-10 col-10 mb-2">';    
               echo '<input type="text" name="domain" class="form-control form-control-sm w-input-text">';    
               echo '</div>';
               echo '<div class="col-xl-3 col-lg-3 col-md-2 col-sm-3 col-3">';
               echo '<button class="btn  btn-sm btn-wr-red " type="submit" name="save">Save</button>';
               echo '</div>'; 
               echo '</div>';
               echo '</form>';
               echo '</div>';
               echo '</div>';
   echo '</div>';
   echo '</div>';
   echo '</div>';

   echo '<div class="col-xl-8 col-lg-10 col-md-12 col-sm-12 col-12">';
   
   echo '<div class="card card-wr">';
   echo '    <div class="card-header card-header-warriors">';
   echo '       <i class="fas fa-globe mr-1"></i>';
   echo '            Added Domains';
   echo '    </div>';
   echo '<div class="card-body card-body-warriors">';
   echo '<div class="template__table_static template__table_responsive">';
   echo '<div class="scrollable scrollbar-macosx">';     
   dominios();
   echo '</div>';
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
   }
   else {
      echo "<center><h1>Permission denied</h1></center>";
   }

// -----------------------------------
echo '</div>';
echo '</div>';

// Add footer
html_end_new(); 

// Close any open db connections
dbclose();
