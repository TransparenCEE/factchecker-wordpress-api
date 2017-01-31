<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include_once(dirname(__FILE__) . "/administrative/common/constructor.inc.php");
include_once('Pager/Pager_Wrapper.php');
$no_items_per_page = 20;

$request_insert_array = array(
    'q' => $_GET['q'],
    'user' => $_GET['u'],
    'ip' => getip(),
    'request' => $_SERVER['REQUEST_URI'],
);

if (!isset($_REQUEST['q']) || trim($_REQUEST['q']) == '') {
    $api_content_array = array(
        'error' => 'invalide code'
    );
    echo json_encode($api_content_array);
    request_insert($request_insert_array, 'y', 'ic');
    exit();
}

$DISPLAY_custom_factcheck = false;

$api_content_array = array();

$fields_to_get_factchecks = "a.id_factcheck, a.factcheck_link, a.post_datetime, a.context, a.status, a.concluzie, a.url_sursa as sursa";
switch ($_GET['q']) {
    case 'all':
        //$total_items = $res_get_content->numRows();
        $sql_get_content = "SELECT $fields_to_get_factchecks FROM factcheck_content a ORDER BY post_datetime DESC";
        $api_content_array['q'] = 'all';
        break;
    default:
        $sql_get_content = "SELECT $fields_to_get_factchecks, b.snippet as declaratie FROM factcheck_content a "
                . " RIGHT JOIN factcheck_content2links b ON a.id_factcheck=b.id_factcheck"
                . " WHERE b.md5_link_identifier='" . $db_read->escape($_GET['q']) . "' AND b.status='active'"
                . " ORDER BY post_datetime DESC"
        ;
        $api_content_array['q'] = $_GET['q'];
        $DISPLAY_custom_factcheck = true;
}
//$res_get_content = $db_read->query($sql_get_content);
//echo $sql_get_content;

$params = array(
    'perPage' => $no_items_per_page,
    'delta' => 3,
    'append' => true,
    //'fileName' => '%d',
    'clearIfVoid' => true,
    'urlVar' => 'page',
);

$page_data = Pager_Wrapper_MDB2($db_read, $sql_get_content, $params);
$total_pages = $page_data['page_numbers']['total'];
$num_elements = $page_data['totalItems'];

$page_no = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;

if ($_GET['q'] == 'all') {
    if ($num_elements > 0) {
        $api_content_array['total_items'] = $num_elements;
        $api_content_array['total_pages'] = $total_pages;
        $page_no = $page_no > $total_pages ? $total_pages : $page_no;
        $api_content_array['current_page'] = $page_no;
        if ($page_no < $total_pages) {
            $api_content_array['next_page'] = $cfg_array['site_url_front'] . '?q=' . $_GET['q'] . '&page=' . ($page_no + 1);
        }
        if ($page_no > 1) {
            $api_content_array['prev_page'] = $cfg_array['site_url_front'] . '?q=' . $_GET['q'] . '&page=' . ($page_no - 1);
        }
    }
}

if (!empty($page_data) && is_array($page_data) && $page_data['totalItems'] > 0) {
    foreach ($page_data['data'] as $row_get_data) {
        if ($DISPLAY_custom_factcheck) {
            $api_content_array['data'][$row_get_data['id_factcheck']]['declaratie'] = $row_get_data['declaratie'];
        }
        $api_content_array['data'][$row_get_data['id_factcheck']]['context'] = $row_get_data['context'];
        $api_content_array['data'][$row_get_data['id_factcheck']]['status'] = $row_get_data['status'];
        $api_content_array['data'][$row_get_data['id_factcheck']]['concluzie'] = $row_get_data['concluzie'];
        $api_content_array['data'][$row_get_data['id_factcheck']]['sursa'] = $row_get_data['sursa'];
        $api_content_array['data'][$row_get_data['id_factcheck']]['url'] = $row_get_data['factcheck_link'];
        $api_content_array['data'][$row_get_data['id_factcheck']]['date'] = strtotime($row_get_data['post_datetime']);
        if (!$DISPLAY_custom_factcheck) {
            $api_content_array['data'][$row_get_data['id_factcheck']]['links'] = array();
            $sql_get_urls = "SELECT link_content, snippet FROM factcheck_content2links WHERE id_factcheck={$row_get_data['id_factcheck']}";
            $res_get_urls = $db_read->query($sql_get_urls);
            while ($row_get_urls = $res_get_urls->fetchRow()) {
                $api_content_array['data'][$row_get_data['id_factcheck']]['links'][] = array($row_get_urls['link_content'], $row_get_urls['snippet']);
            }
        }
        if ($DISPLAY_custom_factcheck) {
            $table_fields_values = array(
                'id_factcheck' => $row_get_data['id_factcheck'],
                'q' => $_GET['q'],
                'user' => $_GET['u'],
                'ip' => getip(),
            );
            $db_write->extended->autoExecute('api_factchecks', $table_fields_values, MDB2_AUTOQUERY_INSERT);
        }
    }
} else {
    if ($DISPLAY_custom_factcheck) {
        $api_content_array['error'] = 'Code not found';
        echo json_encode($api_content_array);
        request_insert($request_insert_array, 'y', 'nf');
        exit();
    } else {
        request_insert($request_insert_array, 'y', 'em');
    }
}

if (isset($_GET['debug']) && $_GET['debug'] == 'y') {
    print "<pre>";
    print_r($api_content_array);
    request_insert($request_insert_array, 'n', 'k', $num_elements);
} else {
    echo json_encode($api_content_array);
    request_insert($request_insert_array, 'n', 'k', $num_elements);
}
exit();

function request_insert($content, $error = 'N', $message = '', $items = 0) {
    global $db_write, $_SERVER, $_GET;
    $table_fields_values = array(
        'items' => $items,
        'q' => $content['q'],
        'user' => $_GET['u'],
        'ip' => $content['ip'],
        'request' => $content['request'],
        'error' => $error,
        'message' => $message,
        'server' => json_encode($_SERVER),
    );
    $db_write->extended->autoExecute('api_requests', $table_fields_values, MDB2_AUTOQUERY_INSERT);
}

function getip() {
    if (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER["HTTP_CLIENT_IP"])) {
        return trim($_SERVER["HTTP_CLIENT_IP"]);
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $x_forwarded_for_ips = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
        $x_forwarded_for_ips_reverse = array_reverse($x_forwarded_for_ips);
        foreach ($x_forwarded_for_ips_reverse as $ip) {
            if (validip(trim($ip))) {
                return trim($ip);
            }
        }
    }
    if (isset($_SERVER['HTTP_X_FORWARDED']) && validip($_SERVER["HTTP_X_FORWARDED"])) {
        return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR']) && validip($_SERVER["HTTP_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (isset($_SERVER['HTTP_FORWARDED']) && validip($_SERVER["HTTP_FORWARDED"])) {
        return $_SERVER["HTTP_FORWARDED"];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED']) && validip($_SERVER["HTTP_X_FORWARDED"])) {
        return $_SERVER["HTTP_X_FORWARDED"];
    } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return "unknown";
    }
}
