<?php

set_time_limit(120);
require_once("./functions.php");

$param = $_POST['data_function'];
$settings = array();

$numResult;

// Se elige que funcion se va a ejecutar
switch ($param) {
    case 'get_data_ram':
        get_data_ram();
    break;
    case 'get_data_cpu':
        get_data_cpu();
    break;
    case 'get_mails_last_week':
        get_mails_last_week();
    break;
  
    case 'get_top_mail_relays':
        get_top_mail_relays();
    break;
    case 'get_top_viruses': 
        get_top_viruses();
    break;
    case 'get_top_senders_by_volumen':
        get_top_senders_by_volumen();
    break;
    case 'get_top_senders_by_quantity': 
        get_top_senders_by_quantity();
    break;
    case 'get_top_recipients_by_quantity': 
        get_top_recipients_by_quantity();
    break;
    case 'get_top_recipients_by_volumen': 
        get_top_recipients_by_volumen();
    break;
    case 'get_top_senders_domain_by_quantity': 
        get_top_senders_domain_by_quantity();
    break;
    case 'get_top_senders_domain_by_volumen':
        get_top_senders_domain_by_volumen();
    break;
    case 'get_top_recipients_domain_by_volumen': 
        get_top_recipients_domain_by_volumen();
    break;
    case 'get_top_recipients_domain_by_quantity': 
        get_top_recipients_domain_by_quantity();
    break;

    default:
        echo json_encode(" 'msg': 'Funcion no definida ");
    break;
}

function date_last_month(){
    $date_today = date("Y-m-d");
    $date_month = date("Y-m-d", strtotime($date_today . "- 1 month"));
    
    return $date_month;
}


function get_data_cpu()
{
    $data = array();    
    
    $CPU_total = 100;
    $CPU = shell_exec("top -bn1 -d 5 | grep '%Cpu'");

    $CPU = explode(" ", $CPU);
    $cpu_temp = array();

    foreach ($CPU as $key => $value) {
        if( is_numeric( $value) ) array_push($cpu_temp, $value );
        if ( sizeof( $cpu_temp ) == 2) break;
    }

    $CPU = $cpu_temp;

    $CPU_used_user = (float)$CPU[0];
    $CPU_used_system = (float)$CPU[1];
    $CPU_used_total = (float)number_format($CPU_used_user + $CPU_used_system, 2);
    $CPU_available = (float)number_format($CPU_total - $CPU_used_total, 2);

    $data['labels'][0] = 'Total';
    $data['labels'][1] = 'Total';
    $data['labels'][2] = 'User';
    $data['labels'][3] = 'System';
    $data['labels'][4] = 'Available';
    
    $data['CPU_total'] = $CPU_total;
    $data['CPU_used_total'] = $CPU_used_total;
    $data['CPU_used_user'] = $CPU_used_user;
    $data['CPU_used_system'] = $CPU_used_system;
    $data['CPU_available'] = $CPU_available;

    echo json_encode($data);
}

function get_data_ram()
{
    $data = array();    
    
    // free | grep Mem | awk '{ print $2 }'
    $RAM_total = (int)shell_exec("free | grep Mem | awk '{ print $2 }'");
    $RAM_used = (int)shell_exec("free | grep Mem | awk '{ print $3 }'");
    $RAM_available = $RAM_total - $RAM_used;

    $RAM_total_Percent = "100%";
    $RAM_used_Percent = $RAM_used * 100 / $RAM_total;
    $RAM_available_Percent = $RAM_available * 100 / $RAM_total;

    $RAM_total = $RAM_total / 1024 / 1024;
    $RAM_total = number_format($RAM_total, 2).'GB';
    $RAM_used = $RAM_used / 1024 / 1024;
    $RAM_used = number_format($RAM_used, 2).'GB';
    $RAM_available = $RAM_available / 1024 / 1024;
    $RAM_available = number_format($RAM_available, 2).'GB';
    
    $RAM_used_Percent = (float)number_format($RAM_used_Percent, 2);
    $RAM_available_Percent = (float)number_format($RAM_available_Percent, 2);

    $data['labels'][0] = 'Total';
    $data['labels'][1] = 'Used';
    $data['labels'][2] = 'Available';

    $data['RAM_total'] = $RAM_total;
    $data['RAM_used'] = $RAM_used;
    $data['RAM_available'] = $RAM_available;

    $data['RAM_total_Percent'] = $RAM_total_Percent;
    $data['RAM_used_Percent'] = $RAM_used_Percent;
    $data['RAM_available_Percent'] = $RAM_available_Percent;

    echo json_encode($data);
}


function get_mails_last_week()
{
    $date_today = date("Y-m-d");
    //resto 7 días
    $date_week = date("Y-m-d", strtotime($date_today . "- 7 days"));

    $sql = "
        SELECT
        DATE_FORMAT(date, '%d/%m/%y') AS xaxis,
        COUNT(*) AS total_mail,
        SUM(CASE WHEN virusinfected>0 THEN 1 ELSE 0 END) AS total_virus,
        SUM(
        CASE WHEN (
                isspam>0
                AND (virusinfected=0 OR virusinfected IS NULL)
                AND (nameinfected=0 OR nameinfected IS NULL)
                AND (otherinfected=0 OR otherinfected IS NULL)
        ) THEN 1 ELSE 0 END
        ) AS total_spam
        FROM
        maillog
        WHERE
        ".$_SESSION['global_filter']."
        AND
        date >= '" . $date_week . "'
        GROUP BY
        xaxis
        ORDER BY
        date;
    ";

    $sqlColumns = array(
        'xaxis',
        'total_mail',
        'total_virus',
        'total_spam',
    );

    $graphColumns = array(
        'labelColumn' => 'xaxis',
        'dataLabels' => array(
            array('Emails', 'virus', 'spam'),
        ),
        'dataNumericColumns' => array(
            array('total_mail', 'total_virus', 'total_spam'),
        ),
        'dataFormattedColumns' => array(
            array('total_mail', 'total_virus', 'total_spam'),
        ),
        'xAxeDescription' => 'Date',
        'yAxeDescriptions' => array('Mails'),
        'fillBelowLine' => array('true', 'true')
    );

    $data = getDataDB($sql, $sqlColumns);

    if ( !is_array($data) ) {
        $data['msg'] = __('diemysql99');
        $data['error'] = true;
    }else {
        $data['error'] = false;
        $data = formatData($data, $graphColumns);    
    }

    echo json_encode($data);
}

function checkdatafromdb(&$data){
    if(!is_array($data))
        if ( strlen( $data ) == 0  ) {
            $data['msg'] = __('diemysql99');
            $data['error'] = true;
            return true;
        }else {
            $data['error'] = false;
        }
    return false;

}

function formatData($data, $graphColumns){
    $data_temp = [];
    $numericData = "";
    $formattedData = "";
    $dataLabels="";
    $graphTypes="";
    $colors="";

    $data_temp['xaxis'] = $data['xaxis'];
    $data_temp['labels'] = $graphColumns['dataLabels'][0];
    $data_temp['size'] = sizeof($graphColumns['dataLabels'][0]);


    for ($i=0; $i < $data_temp['size']; $i++) { 
        // $data_temp['dataset'][$i] = $data[ $graphColumns['dataNumericColumns'][0][$i] ];
        $data_temp['dataset'][$i] = array_map(
            function ($val) {
                return (int)$val;
            },
            $data[ $graphColumns['dataNumericColumns'][0][$i] ]
        );
    }

    return $data_temp;
}



function get_top_mail_relays(){

    $month = date_last_month();

    $sqlQuery = "
    SELECT
    clientip,
    count(*) AS count,
    sum(virusinfected) AS total_viruses,
    sum(isspam) AS total_spam,
    sum(size) AS size
    FROM
    maillog
    WHERE
    ".$_SESSION['global_filter']."
    AND
    date > '".$month."'
    GROUP BY
    clientip
    ORDER BY
    count DESC
    LIMIT 10";

    $sqlColumns = array(
        'clientip',
    'count',
    'total_viruses',
    'total_spam',
    'size'
    );

    $valueConversion = array(
        'clientip' => 'hostnamegeoip',
        'count' => 'number',
        'total_viruses' => 'number',
        'total_spam' => 'number',
        'size' => 'scale',
    );

    $data = getDataDB($sqlQuery, $sqlColumns);
    if(!checkdatafromdb($data))
    runConversions($valueConversion,$data);

    echo json_encode($data);
}

function get_top_viruses(){

    $month = date_last_month();
    
    $sqlQuery = "
    SELECT
    report
    FROM
    maillog
    WHERE
    ".$_SESSION['global_filter']."
    AND
    date > '".$month."'
    AND
    virusinfected = 1
    AND
    report IS NOT NULL
    ";

    $sqlColumns = array(
        'report'
    );

    $valueConversion = array(
        'report' => 'countviruses', 
        'viruscount' => 'number'
    );

    $data = getDataDB($sqlQuery, $sqlColumns);
    if(!checkdatafromdb($data))
    runConversions($valueConversion,$data);
    $data['msg'] = $sqlQuery;
    echo json_encode($data);
}

function get_top_senders_by_volumen(){

    $month = date_last_month();

    $sqlQuery = '
    SELECT
    from_address as `name`,
    COUNT(*) as `count`,
    SUM(size) as `size`
    FROM
    maillog
    WHERE
    '.$_SESSION['global_filter'].'
    AND
    date > "'.$month.'"
    AND
    from_address <> "" 		-- Exclude delivery receipts
    AND
    from_address IS NOT NULL     	-- Exclude delivery receipts
    GROUP BY
    from_address
    ORDER BY
    size DESC
    LIMIT 10
';

    $sqlColumns = array(
        'name',
        'count',
        'size'
    );

    $valueConversion = array(
        'size' => 'scale',
        'count' => 'number'
    );

    $data = getDataDB($sqlQuery, $sqlColumns);
    if(!checkdatafromdb($data))
            runConversions($valueConversion,$data);
    echo json_encode($data);
}

function get_top_senders_by_quantity(){

    $month = date_last_month();

    $sqlQuery = '
    SELECT
    from_address as `name`,
    COUNT(*) as `count`,
    SUM(size) as `size`
    FROM
    maillog
    WHERE
    '.$_SESSION['global_filter'].'
    AND
    date > "'.$month.'"
    AND
    from_address <> "" 		-- Exclude delivery receipts
    AND
    from_address IS NOT NULL     	-- Exclude delivery receipts
    GROUP BY
    from_address
    ORDER BY
    count DESC
    LIMIT 10
';

    $sqlColumns = array(
        'name',
        'count',
        'size'
    );

    $valueConversion = array(
        'size' => 'scale',
        'count' => 'number'
    );

    $data = getDataDB($sqlQuery, $sqlColumns);
    if(!checkdatafromdb($data))
    runConversions($valueConversion,$data);
    echo json_encode($data);
}

function get_top_recipients_by_quantity(){
    
    $month = date_last_month();

    $sqlQuery = '
    SELECT
    REPLACE(to_address,",",", ") as `name`,
    COUNT(*) as `count`,
    SUM(size) as `size`
    FROM
    maillog
    WHERE
    '.$_SESSION['global_filter'].'
    AND
    date > "'.$month.'"
    AND
    from_address <> "" 		-- Exclude delivery receipts
    AND
    from_address IS NOT NULL     	-- Exclude delivery receipts
    GROUP BY
    to_address
    ORDER BY
    count DESC
    LIMIT 10
';

    $sqlColumns = array(
        'name',
        'count',
        'size'
    );

    $valueConversion = array(
        'size' => 'scale',
        'count' => 'number'
    );

    $data = getDataDB($sqlQuery, $sqlColumns);
    if(!checkdatafromdb($data))
    runConversions($valueConversion,$data);
        echo json_encode($data);
}

function get_top_recipients_by_volumen(){
    
    $month = date_last_month();

    $sqlQuery = '
    SELECT
REPLACE(to_address,",",", ") as `name`,
COUNT(*) as `count`,
SUM(size) as `size`
FROM
maillog
WHERE
'.$_SESSION['global_filter'].'
AND
    date > "'.$month.'"
    AND
from_address <> "" 		-- Exclude delivery receipts
AND
from_address IS NOT NULL     	-- Exclude delivery receipts
GROUP BY
to_address
ORDER BY
size DESC
LIMIT 10
';

    $sqlColumns = array(
        'name',
        'count',
        'size'
    );

    $valueConversion = array(
        'size' => 'scale',
        'count' => 'number'
    );

    $data = getDataDB($sqlQuery, $sqlColumns);
    if(!checkdatafromdb($data))
    runConversions($valueConversion,$data);
    echo json_encode($data);
}

function get_top_senders_domain_by_quantity(){
    
    $month = date_last_month();

    $sqlQuery = '
    SELECT
    from_domain AS `name`,
    COUNT(*) as `count`,
    SUM(size) as `size`
    FROM
    maillog
    WHERE
    '.$_SESSION['global_filter'].'
    AND
    date > "'.$month.'"
    AND
    from_domain <> "" 		-- Exclude delivery receipts
    AND
    from_domain IS NOT NULL     	-- Exclude delivery receipts
    GROUP BY
    from_domain
    ORDER BY
    count DESC
    LIMIT 10
    ';

    $sqlColumns = array(
        'name',
        'count',
        'size'
    );

    $valueConversion = array(
        'size' => 'scale',
        'count' => 'number'
    );

    $data = getDataDB($sqlQuery, $sqlColumns);
    if(!checkdatafromdb($data))
    runConversions($valueConversion,$data);
    echo json_encode($data);
}

function get_top_senders_domain_by_volumen(){
    
    $month = date_last_month();

    $sqlQuery = '
    SELECT
    from_domain as `name`,
    COUNT(*) as `count`,
    SUM(size) as `size`
    FROM
    maillog
    WHERE
    '.$_SESSION['global_filter'].'
    AND
    date > "'.$month.'"
    AND
    from_domain <> "" 		-- Exclude delivery receipts
    AND
    from_domain IS NOT NULL     	-- Exclude delivery receipts
    GROUP BY
    from_domain
    ORDER BY
    size DESC
    LIMIT 10
    ';

    $sqlColumns = array(
        'name',
        'count',
        'size'
    );

    $valueConversion = array(
        'size' => 'scale',
        'count' => 'number'
    );

    $data = getDataDB($sqlQuery, $sqlColumns);
    if(!checkdatafromdb($data))
    runConversions($valueConversion,$data);
    echo json_encode($data);
}

function get_top_recipients_domain_by_volumen(){
    
    $month = date_last_month();

    $sqlQuery = '
    SELECT
    to_domain AS `name`,
    COUNT(*) as `count`,
    SUM(size) as `size`
    FROM
    maillog
    WHERE
    '.$_SESSION['global_filter'].'
    AND
    date > "'.$month.'"
    AND
    from_address <> "" 		-- Exclude delivery receipts
    AND
    from_address IS NOT NULL     	-- Exclude delivery receipts
    GROUP BY
    to_domain
    ORDER BY
    size DESC
    LIMIT 10
    ';

    $sqlColumns = array(
        'name',
        'count',
        'size'
    );

    $valueConversion = array(
        'size' => 'scale',
        'count' => 'number'
    );

    $data = getDataDB($sqlQuery, $sqlColumns);

    if(!checkdatafromdb($data))
    runConversions($valueConversion,$data);
    echo json_encode($data);
}

function get_top_recipients_domain_by_quantity(){
    
    $month = date_last_month();

    $sqlQuery = '
        SELECT
        to_domain AS `name`,
        COUNT(*) as `count`,
        SUM(size) as `size`
    FROM
        maillog
    WHERE
    '.$_SESSION['global_filter'].'
    AND
    date > "'.$month.'"
    AND
        from_address <> "" 		-- Exclude delivery receipts
    AND
        from_address IS NOT NULL     	-- Exclude delivery receipts
    GROUP BY
        to_domain
    ORDER BY
        count DESC
    LIMIT 10
    ';

    $sqlColumns = array(
        'name',
        'count',
        'size'
    );

    $valueConversion = array(
        'size' => 'scale',
        'count' => 'number'
    );

    $data = getDataDB($sqlQuery, $sqlColumns);
    if(!checkdatafromdb($data))
    runConversions($valueConversion,$data);

    echo json_encode($data);
}



// Obtiene los datos de la BD ordenado en columnas
function getDataDB($sql, $sqlColumns)
{
    include_once "./functions.php";
    $result = dbquery($sql);
    $data = array();
    $numResult = $result->num_rows;
    if ($numResult <= 0 ) {
        return false;
    }
    while ($row = $result->fetch_assoc()) {
        foreach ($sqlColumns as $columnName) {
            $data[$columnName][] = $row[$columnName];
        }
    }
    dbclose();
    return $data;
}

function runConversions($valueConversion, &$data)
{
    foreach ($valueConversion as $column => $conversion) {
        switch ($conversion) {
            case 'scale':
                convertScale($column,$data);
                break;
            case 'number':
                convertNumber($column,$data);
                break;
            case 'generatetimescale':
                generateTimeScale($data);
                break;
            case 'timescale':
                convertToTimeScale($column,$data);
                break;
            case 'hostnamegeoip':
                convertHostnameGeoip($column,$data);
                break;
            case 'countviruses':
                convertViruses($column,$data);
                break;
        }
    }
}

/**
 * Converts the data from $this->data[$column] to numbers
 *
 * @param string $column the data column that shall be converted
 * @return void
 */
function convertNumber($column,&$data)
{
    $data[$column . 'conv'] = array_map(
        function ($val) {
            return number_format($val);
        },
        $data[$column]
    );
}

/**
 * Converts the data from $this->data[$column] so that so that it is scaled in kB, MB, GB etc
 *
 * @param string $column the data column that shall be converted
 * @return void
 */
function convertScale($column,&$data)
{
    // Work out best size
    $data[$column . 'conv'] = $data[$column];
    format_report_volume($data[$column . 'conv'], $size_info);
    $scale = $size_info['formula'];
    foreach ($data[$column . 'conv'] as $key => $val) {
        $data[$column . 'conv'][$key] = formatSize($val * $scale);
    }
}

/**
 * Converts the data (ip address) from $this->data[$column] so that the hostname and geoip lookup are generated in $this->data['hostname'] and $this->data['geoip']
 *
 * @param string $column the data column that shall be converted
 * @return void
 */
function convertHostnameGeoip($column,&$data)
{
    $data['hostname'] = array();
    $data['geoip'] = array();
    foreach ($data[$column] as $ipval) {
        $hostname = gethostbyaddr($ipval);
        if ($hostname === $ipval) {
            $data['hostname'][] = __('hostfailed64');
        } else {
            $data['hostname'][] = $hostname;
        }
        if ($geoip = return_geoip_country($ipval)) {
            $data['geoip'][] = $geoip;
        } else {
            $data['geoip'][] = __('geoipfailed64');
        }
    }
}

/**
 * Converts the data from $this->data[$column] so that virus names and counter are inserted in $this->data['virusname'] and $this->data['viruscount']
 *
 * @param string $column the data column that shall be converted
 * @return void
 */
function convertViruses($column,&$data)
{
    $viruses = array();
    foreach ($data[$column] as $report) {
        $virus = getVirus($report);
        if ($virus !== null) {
            if (isset($viruses[$virus])) {
                $viruses[$virus]++;
            } else {
                $viruses[$virus] = 1;
            }
        }
    }
    arsort($viruses);
    reset($viruses);
    $count = 0;
    $data['virusname'] = array();
    $data['viruscount'] = array();
    foreach ($viruses as $key => $val) {
        $data['virusname'][] = $key;
        $data['viruscount'][] = $val;
        if (++$count >= 10) {
            break;
        }
    }
    $numResult = $count;
}

    /**
 * Generates $this->data['time'] with the time beginning with $this->settings['timeInterval'] and
 * in steps of $this->settings['timeScale']
 *
 * @return void
 */
function generateTimeScale(&$data)
{
    if (!isset($this->settings['timeInterval']) || !isset($this->settings['timeScale'])
            || !isset($this->settings['timeFormat'])) {
        throw new \Exception('timeInterval or timeScale not set');
    }
    $interval = $this->settings['timeInterval'];
    $scale = $this->settings['timeScale'];
    $format = str_replace('%', '', $this->settings['timeFormat']);

    $now = new DateTime();
    $this->settings['now'] = $now;
    $date = clone $now;
    $date = $date->sub(new DateInterval($interval));
    $dates = array($date->format($format));
    $count = 1;
    while ($date < $now) {
        //get the next interval and create the label for it
        $date = $date->add(new DateInterval($scale));
        $dates[] = $date->format($format);
        $count++;
    }
    //store the time scales and define the result count
    $data['time'] = $dates;
    $numResult = $count;
}

/**
 * Converts the data from $this->data[$column] so that it is mapped to an time scale
 *
 * @param string $column the data column that shall be converted
 * @return void
 */
function convertToTimeScale($column,&$data)
{
    if (!isset($this->settings['timeInterval'], $this->settings['timeScale'], $this->settings['timeFormat'])) {
        throw new \Exception('timeInterval or timeScale not set');
    }
    $interval = $this->settings['timeInterval'];
    $scale = $this->settings['timeScale'];
    $format = $this->settings['timeGroupFormat'];

    $now = $this->settings['now'];
    $start = clone $now;
    $start = $start->sub(new DateInterval($interval));
    $oldest = clone $start;
    //initialize the time scales with zeros
    $convertedData = array(($start->format($format)) => 0);
    while ($start < $now) {
        $convertedData[$start->add(new DateInterval($scale))->format($format)] = 0;
    }
    //get the values from the sql result and assign them to the correct time scale part
    $count = isset($data['xaxis']) ? count($data['xaxis']) : 0;
    for ($i=0; $i<$count; $i++) {
        // get the value from data and add it to the corresponding hour
        $time = new DateTime($data['xaxis'][$i]);
        //recheck if the entry is inside the value range
        if ($time >= $oldest && $time < $now) {
            $convertedData[$time->format($format)] += $data[$column][$i];
        }
    }
    //we only need the value and not the keys
    $data[$column . 'conv'] = array_values($convertedData);
}
