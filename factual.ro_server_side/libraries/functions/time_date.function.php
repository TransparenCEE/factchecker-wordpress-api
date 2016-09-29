<?php

//26-01-2001 -> 8936509348
function timestamp_romanian_date($date) {
    $date_array = explode('-', $date);
    $timestamp = mktime(7, 0, 0, $date_array[1], $date_array[0], $date_array[2]);
    return $timestamp;
}

//2001-01-26 -> 8936509348
function timestamp_english_date($date) {
    $date_array = explode('-', $date);
    $timestamp = mktime(7, 0, 0, $date_array[1], $date_array[2], $date_array[0]);
    return $timestamp;
}

//2001-01-26 -> 26-01-2001
function romanian_date_from_english_date($date) {
    $date_array = explode('-', $date);
    $timestamp = $date_array[2] . '-' . $date_array[1] . '-' . $date_array[0];
    return $timestamp;
}

//2001-01-26 -> 26 ian 2009
function romanian_month_date_from_english_date($date, $type) {
    global $month_names_short_array, $month_names_long_array, $lang_cfg;
    $date_array = explode('-', $date);
    $timestamp = mktime(7, 0, 0, $date_array[1], $date_array[2], $date_array[0]);
    if ($type == 'long') {
        return date('j', $timestamp) . ' ' . $lang_cfg[strtolower($month_names_long_array["m" . date('m', $timestamp)])] . " " . date('Y', $timestamp);
    } ELSe {
        return date('j', $timestamp) . ' ' . $lang_cfg[strtolower($month_names_short_array["m" . date('m', $timestamp)])] . " " . date('Y', $timestamp);
    }
}

function romanian_month_date_no_day_from_english_date($date, $type) {
    global $month_names_short_array, $month_names_long_array;
    $date_array = explode('-', $date);
    $timestamp = mktime(7, 0, 0, $date_array[1], $date_array[2], $date_array[0]);
    if ($type == 'long') {
        return $month_names_long_array["m" . date('m', $timestamp)] . " " . date('Y', $timestamp);
    } ELSe {
        return $month_names_short_array["m" . date('m', $timestamp)] . " " . date('Y', $timestamp);
    }
}

function romanian_month_date_from_english_datetime($date, $type) {
    global $month_names_short_array, $month_names_long_array;
    $time_array = explode(' ', $date);
    $year_array = explode('-', $time_array[0]);
    $hour_array = explode(':', $time_array[1]);
    $timestamp = mktime($hour_array[0], $hour_array[1], $hour_array[2], $year_array[1], $year_array[2], $year_array[0]);
    if ($type == 'long') {
        return date('j', $timestamp) . ' ' . $month_names_long_array["m" . date('m', $timestamp)] . " " . date('Y', $timestamp);
    } ELSe {
        return date('j', $timestamp) . ' ' . $month_names_short_array["m" . date('m', $timestamp)] . " " . date('Y', $timestamp);
    }
}

function romanian_month_date_from_english_datetime_NO_YEAR($date, $type) {
    global $month_names_short_array, $month_names_long_array;
    $time_array = explode(' ', $date);
    $year_array = explode('-', $time_array[0]);
    $hour_array = explode(':', $time_array[1]);
    $timestamp = mktime($hour_array[0], $hour_array[1], $hour_array[2], $year_array[1], $year_array[2], $year_array[0]);
    if ($type == 'long') {
        return date('j', $timestamp) . ' ' . $month_names_long_array["m" . date('m', $timestamp)];
    } ELSe {
        return date('j', $timestamp) . ' ' . $month_names_short_array["m" . date('m', $timestamp)];
    }
}

function days_between($start, $end) {
    $start = timestamp_english_date($start);
    $end = timestamp_english_date($end);
    $diffInSeconds = abs($end - $start) + 24 * 60 * 60;
    $diffInDays = ceil($diffInSeconds / 86400);
    return $diffInDays;
}

function romanian_interval_from_english_dates($start_date, $end_date, $type) {
    global $month_names_short_array, $month_names_long_array;
    $months_name = $type == 'long' ? $month_names_long_array : $month_names_short_array;

    $start_date_array = explode('-', $start_date);
    $start_date_timestamp = mktime(7, 0, 0, $start_date_array[1], $start_date_array[2], $start_date_array[0]);

    $end_date_array = explode('-', $end_date);
    $end_date_timestamp = mktime(7, 0, 0, $end_date_array[1], $end_date_array[2], $end_date_array[0]);


    $day_before = number_format(date('d', $start_date_timestamp));
    $month_before = $months_name['m' . date('m', $start_date_timestamp)];
    $year_before = date('Y', $start_date_timestamp);

    $day_now = number_format(date('d', $end_date_timestamp));
    $month_now = $months_name['m' . date('m', $end_date_timestamp)];
    $year_now = date('Y', $end_date_timestamp);

    $time_print = '';
    if ($day_before != $day_now || $month_before != $month_now || $year_before != $year_now) {
        $time_print .= ' ' . $day_before;
    }
    if ($month_before != $month_now || $year_before != $year_now) {
        $time_print .= ' ' . $month_before;
    }
    if ($year_before != $year_now) {
        $time_print .= ' ' . $year_before;
    }
    $time_print .= $time_print != '' ? ' - ' : '';
    $time_print .= $day_now . ' ' . $month_now . ' ' . $year_now;
    return $time_print;
}

function romanian_interval_from_timestamp($start_date, $end_date, $type) {
    global $month_names_short_array, $month_names_long_array, $lang_cfg;
    $months_name = $type == 'long' ? $month_names_long_array : $month_names_short_array;

    $start_date_timestamp = $start_date;

    $end_date_timestamp = $end_date;


    $day_before = number_format(date('d', $start_date_timestamp));
    $month_before = $months_name['m' . date('m', $start_date_timestamp)];
    $year_before = date('Y', $start_date_timestamp);

    $day_now = number_format(date('d', $end_date_timestamp));
    $month_now = $months_name['m' . date('m', $end_date_timestamp)];
    //$month_now = $lang_cfg[$months_name['m'.date('m', $end_date_timestamp)]];
    $year_now = date('Y', $end_date_timestamp);

    $time_print = '';
    if ($day_before != $day_now || $month_before != $month_now || $year_before != $year_now) {
        $time_print .= ' ' . $day_before;
    }
    if ($month_before != $month_now || $year_before != $year_now) {
        //$time_print .= ' '.$month_before;
        $time_print .= ' ' . $lang_cfg[strtolower($month_before)];
    }
    if ($year_before != $year_now) {
        $time_print .= ' ' . $year_before;
    }
    $time_print .= $time_print != '' ? ' - ' : '';
    //$time_print .= $day_now.' '.$month_now.' '.$year_now;
    $time_print .= $day_now . ' ' . $lang_cfg[strtolower($month_now)] . ' ' . $year_now;
    return $time_print;
}

function romanian_interval_from_timestamp_no_year($start_date, $end_date, $type) {
    global $month_names_short_array, $month_names_long_array;
    $months_name = $type == 'long' ? $month_names_long_array : $month_names_short_array;

    $start_date_timestamp = $start_date;

    $end_date_timestamp = $end_date;


    $day_before = number_format(date('d', $start_date_timestamp));
    $month_before = $months_name['m' . date('m', $start_date_timestamp)];
    $year_before = date('Y', $start_date_timestamp);

    $day_now = number_format(date('d', $end_date_timestamp));
    $month_now = $months_name['m' . date('m', $end_date_timestamp)];
    $year_now = date('Y', $end_date_timestamp);

    $time_print = '';
    if ($day_before != $day_now || $month_before != $month_now || $year_before != $year_now) {
        $time_print .= '' . $day_before;
    }
    if ($month_before != $month_now || $year_before != $year_now) {
        $time_print .= '' . $month_before;
    }
    // if ($year_before != $year_now){
    //      $time_print .= ' '.$year_before;
    // }
    $time_print .= $time_print != '' ? '-' : '';
    $time_print .= $day_now . '' . $month_now;
    return $time_print;
}

?>