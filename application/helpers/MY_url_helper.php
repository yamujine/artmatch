<?php

function alert_and_redirect($msg = '', $url = '') {
    if (empty($msg)) {
        $msg = '올바른 방법으로 이용해 주십시오.';
    }

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=\"utf-8\">";
    echo "<script type='text/javascript'>alert('{$msg}');";
    if (!empty($url)) {
        echo "location.replace('{$url}');";
    } else {
        echo "history.go(-1);";
    }
    echo "</script>";
    exit;
}

function alert_and_close_popup($msg = '') {
    if (empty($msg)) {
        $msg = '올바른 방법으로 이용해 주십시오.';
    }

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=\"utf-8\">";
    echo "<script type='text/javascript'>alert('{$msg}');";
    echo "window.close();";
    echo "</script>";
    exit;
}
