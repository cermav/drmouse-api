<?php
/**
 * Created by PhpStorm.
 * User: burt
 * Date: 13.05.2019
 * Time: 11:44
 */


// add some security
// var_dump($_SERVER['REMOTE_ADDR']);die();

if ($_SERVER['REMOTE_ADDR'] == 'ss::1') {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $database = 'drmouse_old';
    $user = 'develop';
    $pass = 'develop';

    $fileName = dirname(__FILE__) . '/dump.sql';

    exec("mysqldump --user={$user} --password={$pass} {$database} --result-file={$fileName} 2>&1", $output);


    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Cache-Control: public"); // needed for internet explorer
    header("Content-Type: application/text");
    header("Content-Transfer-Encoding: Binary");
    header("Content-Length:".filesize($fileName));
    header("Content-Disposition: attachment; filename=dump.sql");

    readfile($fileName);

    unlink($fileName);

} else {

    header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");

}


