<?php

$ip_to_display_errors_array = array(
    '192.168.0.206',
    '192.168.0.10',
    '192.168.0.22',
);
if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {      //the ip of client
    $user_ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {//the ip of proxy
    $user_ip_address = $_SERVER["REMOTE_ADDR"];
}//end if

function mysql_errors_handler($res_query) {//, $errno, $errmsg, $filename, $linenum, $vars
    global $ip_to_display_errors_array, $user_ip_address;

    $errors_log_dir = dirname(__FILE__) . '/../../errors_log/';

    if (is_dir($errors_log_dir)) {
        //@chmod($errors_log_dir, 0777);
        if (!is_file($errors_log_dir . 'index.html')) {
            $handle = @fopen($errors_log_dir . 'index.html', "w");
            @fclose($handle);
        }
    }


    $errors_file = $errors_log_dir . date("Ymd") . "_mysql_errors.log.php";

    if (!is_file($errors_file) || file_get_contents($errors_file) == "") {
        $err .= '<?php exit("Unauthorised web access to *.ini file.\n\nPermission Denied.\nTerminated."); ?>';
    }
    //echo('<!-- BEGIN ERROR --> <br>'.$res_query->getMessage().'<br>'.$res_query->getCode().'<br>'.$res_query->getUserInfo().'<br>'.$res_query->getDebugInfo().'<!-- END ERROR -->');
    $err .= "<errorentry>\n";
    $err .= "\t<datetime>" . date("F j Y h:i:s") . "</datetime>\n";
    $err .= "\t<scriptname>" . $_SERVER['SCRIPT_FILENAME'] . "</scriptname>\n";
    $err .= "\t<code>" . $res_query->getCode() . "</code>\n";
    $err .= "\t<message>" . $res_query->getMessage() . "</message>\n";
    $err .= "\t<userinfo>" . $res_query->getUserInfo() . "</userinfo>\n";
    $err .= "\t<debug>" . $res_query->getDebugInfo() . "</debug>\n";
    $err .= "</errorentry>\n";

    if (!empty($ip_to_display_errors_array) && in_array($user_ip_address, $ip_to_display_errors_array)) {
        //echo '<pre>'; print_r($res_query->getBacktrace()); echo '</pre>';
        //echo '<pre>'; print_r($res_query->getType()); echo '</pre>';
        echo '\r\n<br><strong>PEAR Error:</strong> ' . $res_query->getMessage() . '<br>' . $res_query->getCode() . '<br>' . $res_query->getUserInfo() . '<br>' . $res_query->getDebugInfo();
    }

    $handle = @fopen($errors_file, "a+");
    @chmod($errors_file, 0777);
    @fwrite($handle, $err);
    @fclose($handle);
}

function php_errors_handler($errno, $errmsg, $filename, $linenum, $vars) {
    global $ip_to_display_errors_array, $user_ip_address;

    if (strstr($errmsg, ' Headers and client library minor version mismatch') === false) {
        if ($_SERVER["HTTP_X_FORWARDED_FOR"] != "") {      //the ip of client
            $user_ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {//the ip of proxy
            $user_ip_address = $_SERVER["REMOTE_ADDR"];
        }//end if

        $errors_log_dir = dirname(__FILE__) . '/../../errors_log/';

        if (is_dir($errors_log_dir)) {
            //@chmod($errors_log_dir, 0777);
            if (!is_file($errors_log_dir . 'index.html')) {
                $handle = @fopen($errors_log_dir . 'index.html', "w");
                @fclose($handle);
            }
        }

        // timestamp for the error entry
        $dt = date("Y-m-d H:i:s (T)");

        // define an assoc array of error string
        // in reality the only entries we should
        // consider are E_WARNING, E_NOTICE, E_USER_ERROR,
        // E_USER_WARNING and E_USER_NOTICE
        $errortype = array(
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parsing Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Runtime Notice',
            E_RECOVERABLE_ERRROR => 'Catchable Fatal Error'
        );
        // set of errors for which a var trace will be saved
        $user_errors = array(E_ERROR, E_WARNING, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, E_USER_ERROR, E_USER_WARNING, E_RECOVERABLE_ERRROR);

        if (in_array($errno, $user_errors)) {
            $err = "";
            $errors_file = $errors_log_dir . date("Ymd") . "_php_errors.log.php";

            if (!is_file($errors_file) || file_get_contents($errors_file) == "") {
                $err .= '<?php exit("Unauthorised web access to *.ini file.\n\nPermission Denied.\nTerminated."); ?>';
            }
            $err .= "<errorentry>\n";
            $err .= "\t<datetime>" . $dt . "</datetime>\n";
            $err .= "\t<errornum>" . $errno . "</errornum>\n";
            $err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
            $err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";
            $err .= "\t<scriptname>" . $filename . "</scriptname>\n";
            $err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";
            //$err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
            $err .= "</errorentry>\n\n";
            if (!empty($ip_to_display_errors_array) && in_array($user_ip_address, $ip_to_display_errors_array)) {
                echo "\r\n<br><strong>" . $errortype[$errno] . "</strong>: " . $errmsg . " in " . $filename . " at line " . $linenum;
            }

            $handle = @fopen($errors_file, "a+");
            @chmod($errors_file, 0777);
            @fwrite($handle, $err);
            @fclose($handle);
        }
    }
}

?>