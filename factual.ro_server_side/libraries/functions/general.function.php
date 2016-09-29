<?php

if (!function_exists('str_ireplace')) {

    function str_ireplace($find, $replace, $string) {
        /// This does a search and replace, ignoring case
        /// This function is only here because one doesn't exist yet in PHP
        /// Unlike str_replace(), this only works on single values (not arrays)

        $parts = explode(strtolower($find), strtolower($string));
        $pos = 0;
        foreach ($parts as $key => $part) {
            $parts[$key] = substr($string, $pos, strlen($part));
            $pos += strlen($part) + strlen($find);
        }

        return (join($replace, $parts));
    }

}

if (!function_exists(get_unique_filename)) {

    function get_unique_filename($destination_dir, $destination_file) {
        $filename = $destination_file;
        $dotIndex = strrpos($destination_file, '.');
        $ext = '';
        if (is_int($dotIndex)) {
            $ext = substr($destination_file, $dotIndex);
            $base = substr($destination_file, 0, $dotIndex);
            $base = ereg_replace(" +", "_", $base);
        }
        $counter = 0;
        while (is_file($destination_dir . $filename)) {
            $counter++;
            //if($ext==strtolower('.gif')){$ext = '.png';	}
            $filename = $base . '_' . $counter . $ext;
        }
        return $filename;
    }

}
?>