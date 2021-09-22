<?php
require_once('../../config.php');
global $DB, $CFG;

$digits = 6;
$otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);


//Write a function to send otp to the user

//check phone number in database

$sql = 'select * from {auth_otp_linked_login} where `phone` = ' . $_POST['phone'];
$data = $DB->get_records_sql($sql);

if ($data) {
    $data = $DB->execute("UPDATE {auth_otp_linked_login} SET `confirmtoken`= " . $otp . ",`otpcreated` = '" . date("Y-m-d H:i:s") . "' where `phone` = '" . $_POST['phone'] . "'");
    callOtpFuncction($otp);
} else {
    $sql = '';
    $data = $DB->execute("INSERT INTO {auth_otp_linked_login} (`phone`,`confirmtoken`,`username`,`otpcreated`) VALUES ('" . $_POST['phone'] . "'," . $otp . ",'" . $_POST['phone'] . "','" . date("Y-m-d H:i:s") . "')");
    self::callOtpFuncction($otp);
}

function callOtpFuncction($otp)
{
    if (get_config('auth_otp', 'enableaws') && get_config('auth_otp', 'aws_key') && get_config('auth_otp', 'aws_secrect')) {
        $key = get_config('auth_otp', 'aws_key');
        $secrect = get_config('auth_otp', 'aws_secrect');
        $region = get_config('auth_otp', 'aws_region');
        \auth_otp\awsotpservice::sendOtp($otp, $_POST['phone'], $key, $secrect, $region);
        $_SESSION['auth_otp']['credentials'] = [
            'otp' => $otp,
            'username' => $_POST['phone']
        ];
    }else{
        $_SESSION['auth_otp']['credentials'] = [
            'otp' => $otp,
            'username' => $_POST['phone']
        ];
    }

    return;
}
// insert otp in database

