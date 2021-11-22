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

// Require the functions page
require_once __DIR__ . '/functions.php';

// Require the login function code
require __DIR__ . '/login.function.php';

// Set the Memory usage
ini_set('memory_limit', MEMORY_LIMIT);

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $url_id = $_POST['id'];
}

if (isset($_POST['list_url']) && !empty($_POST['list_url'])) {
    $listurl = $_POST['list_url'];
}


// get perfromed actions PENDIENTE
//$sql = "SELECT released,salearn FROM `maillog` WHERE `id` = '$url_id'";

if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'detail':
            get_detail_data($url_id);
            break;
        case 'is_mcp':
            get_is_mcp();
            break;
    }
}

function get_is_mcp(){
    echo json_encode(get_conf_truefalse('mcpchecks')); 
}

function get_detail_data($url_id)
{
    // Setting the yes and no variable
    $yes = '<span id="Yes">&nbsp;' . __('yes04') . '&nbsp;</span>';
    $no = '<span id="No">&nbsp;' . __('no04') . '&nbsp;</span>';

    // The sql command to pull the data
    $detailsql = "
    SELECT
    id AS '" . __('id04') . "',
    from_address AS '" . __('from04') . "',
    to_address AS '" . __('to04') . "',
    subject AS '" . __('subject04') . "',
    size AS '" . __('size04') . "',
    DATE_FORMAT(timestamp, '" . DATE_FORMAT . ' ' . TIME_FORMAT . "') AS '" . __('receivedon04') . "',
    hostname AS '" . __('receivedby04') . "',
    clientip AS '" . __('receivedfrom04') . "',
    headers '" . __('receivedvia04') . "',
    headers AS '" . __('msgheaders04') . "',
    archive AS 'Archive',
    '" . __('hdrantivirus04') . "' AS 'HEADER',
    CASE WHEN virusinfected>0 THEN '$yes' ELSE '$no' END AS '" . __('virus04') . "',
    CASE WHEN nameinfected>0 THEN '$yes' ELSE '$no' END AS '" . __('blkfile04') . "',
    CASE WHEN otherinfected>0 THEN '$yes' ELSE '$no' END AS '" . __('otherinfec04') . "',
    report AS '" . __('report04') . "',
    '" . __('spamassassin04') . "' AS 'HEADER',
    CASE WHEN isspam>0 THEN '$yes' ELSE '$no' END AS '" . __('spam04') . "',
    CASE WHEN ishighspam>0 THEN '$yes' ELSE '$no' END AS '" . __('hscospam04') . "',
    CASE WHEN issaspam>0 THEN '$yes' ELSE '$no' END AS '" . __('spamassassinspam04') . "',
    CASE WHEN isrblspam>0 THEN '$yes' ELSE '$no' END AS '" . __('listedrbl04') . "',
    CASE WHEN spamwhitelisted>0 THEN '$yes' ELSE '$no' END AS '" . __('spamwl04') . "',
    CASE WHEN spamblacklisted>0 THEN '$yes' ELSE '$no' END AS '" . __('spambl04') . "',
    spamreport AS '" . __('saautolearn04') . "',
    sascore AS '" . __('sascore04') . "',
    spamreport AS '" . __('spamrep04') . "',
    '" . __('hdrmcp04') . "' AS 'HEADER',
    CASE WHEN ismcp>0 THEN '$yes' ELSE '$no' END AS 'MCP:',
    CASE WHEN ishighmcp>0 THEN '$yes' ELSE '$no' END AS '" . __('highscomcp04') . "',
    CASE WHEN issamcp>0 THEN '$yes' ELSE '$no' END AS '" . __('spamassassinmcp04') . "',
    CASE WHEN mcpwhitelisted>0 THEN '$yes' ELSE '$no' END AS '" . __('mcpwl04') . "',
    CASE WHEN mcpblacklisted>0 THEN '$yes' ELSE '$no' END AS '" . __('mcpbl04') . "',
    mcpsascore AS '" . __('mcpscore04') . "',
    mcpreport AS '" . __('mcprep04') . "',
    rblspamreport AS rblspamreport
    FROM
    maillog
    WHERE
    " . $_SESSION['global_filter'] . "
    AND
    id = '" . $url_id . "'
    ";
    //Dberiamos desde aqui ya filtrar los registros que no son 
    $result = dbquery($detailsql);
    $data = $result->fetch_array();

    if (LISTS) {
        $output =
            '<div class="d-flex justify-content-between"> 
            <div class="align-self-center">'
            . $data[__('receivedfrom04')] .
            '</div>
            <div class="row mr-2 align-items-center">
                        
                    </div>
                </div>';
        $data[__('receivedfrom04')] = $output;
    }
    $output = '<div class="table-responsive">' . "\n";
    $output .= '<table width="100%" class="table-bordered table-warriors-rep dataTable">' . "\n";
    $output .= ' <thead class="table-head-warriors-rep">' . "\n";
    $output .= ' <tr>' . "\n";
    $output .= ' <th class="text-center">' . __('ipaddress04') . '</th>' . "\n";
    $output .= ' <th class="text-center">' . __('hostname04') . '</th>' . "\n";
    $output .= ' <th class="text-center">' . __('country04') . '</th>' . "\n";
    $output .= ' <th class="text-center noprint">RBL</th>' . "\n";
    $output .= ' <th class="text-center noprint">Spam</th>' . "\n";
    $output .= ' <th class="text-center noprint" >Virus</th>' . "\n";
    $output .= ' <th class="text-center noprint">' . __('all04') . '</th>' . "\n";
    $output .= ' </tr>' . "\n";
    $output .= ' </thead>' . "\n";
    $output .= ' <tbody class="table-body-warriors-rep">' . "\n";
    if (is_array($relays = get_mail_relays($data[__('receivedvia04')]))) {
        foreach ($relays as $relay) {
            $output .= ' <tr>' . "\n";
            $output .= ' <td>' . $relay . '</td>' . "\n";
            // check if ipv4 has a port specified (e.g. 10.0.0.10:1025), strip it if found
            $relay = stripPortFromIp($relay);
            //check if address is in private IP space
            $isPrivateNetwork = ip_in_range($relay, false, 'private');
            $isLocalNetwork = ip_in_range($relay, false, 'local');
            if ($isPrivateNetwork === true) {
                $output .= ' <td>' . __('privatenetwork04') . "</td>\n";
            } elseif ($isLocalNetwork === true) {
                $output .= ' <td>' . __('localhost04') . "</td>\n";
            }
            // Reverse lookup on address. Possibly need to remove it.
            elseif (($host = gethostbyaddr($relay)) !== $relay) {
                $output .= " <td>$host</td>\n";
            } else {
                $output .= ' <td>' . __('reversefailed04') . "</td>\n";
            }
            // Do GeoIP lookup on address
            if (true === $isPrivateNetwork) {
                $output .= ' <td>' .  __('privatenetwork04') . "</td>\n";
            } elseif ($isLocalNetwork === true) {
                $output .= ' <td>' . __('localhost04') . "</td>\n";
            } elseif (!version_compare(phpversion(), '5.4.0', '>=')) {
                $output .= ' <td>' . __('geoipnotsupported04') . "</td>\n";
            } elseif ($geoip_country = return_geoip_country($relay)) {
                $output .= ' <td>' . $geoip_country . '</td>' . "\n";
            } else {
                $output .= ' <td>' . __('geoipfailed04') . '</td>' . "\n";
            }
            // Link to RBL Lookup
            $output .= ' <td class="noprint" align="center">[<a class="received-a" href="http://multirbl.valli.org/lookup/' . $relay . '.html">&nbsp;&nbsp;</a>]</td>' . "\n";
            // Link to Spam Report for this relay
            $output .= ' <td class="noprint" align="center">[<a class="received-a" href="rep_message_listing.php?token=' . $_SESSION['token'] . '&amp;relay=' . $relay . '&amp;isspam=1">&nbsp;&nbsp;</a>]</td>' . "\n";
            // Link to Virus Report for this relay
            $output .= ' <td class="noprint" align="center">[<a class="received-a" href="rep_message_listing.php?token=' . $_SESSION['token'] . '&amp;relay=' . $relay . '&amp;isvirus=1">&nbsp;&nbsp;</a>]</td>' . "\n";
            // Link to All Messages Report for this relay
            $output .= ' <td class="noprint" align="center">[<a class="received-a" href="rep_message_listing.php?token=' . $_SESSION['token'] . '&amp;relay=' . $relay . '">&nbsp;&nbsp;</a>]</td>' . "\n";
            // Close table
            $output .= ' </tr>' . "\n";
        }
        $output .= ' </tbody>' . "\n";

        $output .= '</table>' . "\n";
        $output .= '</div>' . "\n";
        $data[__('receivedvia04')] = $output;
    } else {
        $data[__('receivedvia04')] = '127.0.0.1'; // Must be local mailer (Exim)
    }


    if (version_compare(PHP_VERSION, '5.4', '>=')) {
        $data[__('msgheaders04')] = nl2br(
            str_replace(array("\n", "\t"), array('<br>', '&nbsp; &nbsp; &nbsp;'), htmlentities($data[__('msgheaders04')], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE))
        );
    } else {
        $data[__('msgheaders04')] = nl2br(
            str_replace(array("\n", "\t"), array('<br>', '&nbsp; &nbsp; &nbsp;'), htmlentities($data[__('msgheaders04')]))
        );
    }
    if (function_exists('iconv_mime_decode')) {
        $data[__('msgheaders04')] = iconv_mime_decode(utf8_decode($data[__('msgheaders04')]), 2, 'UTF-8');
    }
    $data[__('msgheaders04')] = preg_replace("/<br \/>/", '<br>', $data[__('msgheaders04')]);


    $data[__('from04')] = htmlentities($data[__('from04')]);
    if (LISTS) {
        $output = '<div class="d-flex justify-content-between">
                        <div class="align-self-center">'
            . $data[__('from04')] .
            '</div>                     
                        <div class="row mr-2 align-items-center">
                            
                        </div>
                    </div>';
        $output .= "\n";

        $data[__('from04')] = $output;
    }


    $data[__('to04')] = htmlspecialchars($data[__('to04')]);
    $data[__('to04')] = str_replace(',', '<br>', $data[__('to04')]);

    $data[__('subject04')] = htmlspecialchars(getUTF8String(decode_header($data[__('subject04')])));

    $data[__('size04')] = formatSize($data[__('size04')]);


        $data[__('report04')] = nl2br(str_replace(',', '<br>', htmlentities($data[__('report04')])));
        $data[__('report04')] = preg_replace("/<br \/>/", '<br>', $data[__('report04')]);
        $data[__('report04')] = preg_replace('/ <br>/', '<br>', $data[__('report04')]);
    

        $data[__('spamrep04')] = format_spam_report($data[__('spamrep04')] );
    

        if (($autolearn = sa_autolearn($data[__('saautolearn04')])) !== false) {
            $data[__('saautolearn04')] = $yes . " ($autolearn)";
        } else {
            $data[__('saautolearn04')] = $no;
        }
    
    if (!DISTRIBUTED_SETUP) {
        // Display actions if spam/not-spam
        if ($data[__('spam04')] === $yes) {
            $data[__('spam04')] = $data[__('spam04')] . '&nbsp;&nbsp;' . __('actions04') . ' ' . str_replace(' ', ', ', get_conf_var('SpamActions'));
        } else {
            $data[__('spam04')] = $data[__('spam04')] . '&nbsp;&nbsp;' . __('actions04') . ' ' . str_replace(
                ' ',
                ', ',
                get_conf_var('NonSpamActions')
            );
        }
    }

    if ($data[__('hscospam04')] === $yes) {
        // Display actions if high-scoring
        $data[__('hscospam04')] = $data[__('hscospam04')]. '&nbsp;&nbsp;' . __('actions04') . ' ' . str_replace(
            ' ',
            ', ',
            get_conf_var('HighScoringSpamActions')
        );
    }

    if ($data[__('listedrbl04')] === $yes) {
        $data[__('listedrbl04')]  = $data[__('listedrbl04')]  . ' (' . $data['rblspamreport'] . ')';
    }

   
    $data[__('mcprep04')] = format_mcp_report($data[__('mcprep04')]);
    


    
    /**************************************************************************** */
    $data['antivirus_protection'] = __('hdrantivirus04');
    $data['engine'] = __( 'mailscanner03');
    $data['mcpprotection'] = __('hdrmcp04');
    /**************************************************************************** */

    $data['token'] = ''; 
    dbclose();
    echo json_encode($data);
}


function get_relayinfo_data($url_id)
{
    // Setting what Mail Transfer Agent is being used
    $mta = get_conf_var('mta');
    // Display the relay information only if there are matching
    // rows in the relay table (maillog.id = relay.msg_id)...
    $sqlcheck = "SHOW TABLES LIKE 'mtalog_ids'";

    //version for postfix
    $postsql = "
    SELECT
    DATE_FORMAT(m.timestamp,'" . DATE_FORMAT . ' ' . TIME_FORMAT . "') AS 'Date/Time',
    m.host AS 'Relayed by',
    m.relay AS 'Relayed to',
    m.delay AS 'Delay',
    m.status AS 'Status'
    FROM
    mtalog AS m
            LEFT JOIN mtalog_ids AS i ON (i.smtp_id = m.msg_id)
    WHERE
    i.smtpd_id='" . $url_id . "'
    AND
    m.type='relay'
    ORDER BY
    m.timestamp DESC";

    //version for sendmail
    $sendmsql = "
    SELECT
    DATE_FORMAT(timestamp,'" . DATE_FORMAT . ' ' . TIME_FORMAT . "') AS 'Date/Time',
    host AS 'Relayed by',
    relay AS 'Relayed to',
    delay AS 'Delay',
    status AS 'Status'
    FROM
    mtalog
    WHERE
    msg_id='" . $url_id . "'
    AND
    type='relay'
    ORDER BY
    timestamp DESC";


    $tablecheck = dbquery($sqlcheck);
    if (($mta === 'postfix' || $mta === 'msmail') && $tablecheck->num_rows > 0) { //version for postfix
        $result = dbquery($postsql);
    } else { //version for sendmail
        $result = dbquery($sendmsql);
    }

    $data = $result->fetch_array();
    dbclose();
    echo json_encode($data);
}
