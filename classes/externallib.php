<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use auth_otp\awsotpservice;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/externallib.php');

class auth_otp_external extends external_api
{
    /**
     * @return external_function_parameters
     */
    public static function send_otp_parameters()
    {
        return new external_function_parameters(
            array(
                'phone' => new external_value(PARAM_TEXT, 'phone'),
                'countrycode' => new external_value(PARAM_TEXT, 'countrycode')
            )
        );
    }

    /**
     * @param $phone
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function send_otp($phone, $countrycode)
    {
        global $DB, $CFG;
        $params = array(
            'phone' => $phone,
            'countrycode' => $countrycode,
        );
        // Validate the params.
        self::validate_parameters(self::send_otp_parameters(), $params);

        $fullphone = strval($countrycode) . '' . strval($phone);

        // check user exist and last otp time
        $sql = 'select * from {auth_otp_linked_login} where `phone` = ' . $phone;
        $data = $DB->get_record_sql($sql);
        $otp = null;
        $currentdate = '';
        // alreadu exist
        if ($data) {
            if ($data->otpcreated) {
                $seconds = self::calculateTimeDiffrence($data->otpcreated);
                // Otp exist not expired
                if ($seconds['invert'] == 1 && $seconds['seconds'] <= get_config('auth_otp', 'minrequestperiod')) {
                    $res = [
                        'otp' => $data->confirmtoken,
                        'otpdatetime' => $data->otpcreated
                    ];
                    $_SESSION['auth_otp']['credentials'] = [
                        'otp' => $data->confirmtoken,
                        'otpdatetime' => $currentdate,
                        'username' => $phone,
                        'country' => $countrycode
                    ];
                    $status = 1;
                    $message = get_string('otpsentinfo', 'auth_otp');

                } else { // already exist otp but expired
                    $smsStatus = self::callOtpFuncction($fullphone);
                    if ($smsStatus['status']) {
                        $currentdate = date("Y-m-d H:i:s");
                        $otp = $smsStatus['otp']; // get otp from message response
                        // create new user
                        self::oldUserHandle($phone, $otp,$countrycode);
                        $message = get_string('otpsentsuccess', 'auth_otp');
                        $status = 1;
                    } else {
                        $message = $smsStatus['message'];
                        $status = 0;
                    }
                }
            } else {
                $smsStatus = self::callOtpFuncction($fullphone);
                if ($smsStatus['status']) {
                    $currentdate = date("Y-m-d H:i:s");
                    $otp = $smsStatus['otp']; // get otp from message response
                    // create new user
                    self::oldUserHandle($phone, $otp,$countrycode);
                    $message = get_string('otpsentsuccess', 'auth_otp');
                    $status = 1;
                } else {
                    $message = $smsStatus['message'];
                    $status = 0;
                }
            }

        } else { // New User
            $smsStatus = self::callOtpFuncction($fullphone);

            if ($smsStatus['status']) {
                $currentdate = date("Y-m-d H:i:s");
                $otp = $smsStatus['otp']; // get otp from message response
                // create new user
                self::newUserHandle($phone, $otp, $countrycode);
                $message = get_string('otpsentsuccess', 'auth_otp');
                $status = 1;
            } else {
                $message = $smsStatus['message'];
                $status = 0;
            }
        }
        $data = [
            'phone' => $phone,
            'otp' => $otp,
            'timeout' => $currentdate,
            'message' => $message,
            'success' => $status,
            'warnings' => []
        ];
        return $data;
    }

    /**
     * @return external_single_structure
     */
    public static function send_otp_returns()
    {
        return new external_single_structure(
            array(
                'phone' => new external_value(PARAM_TEXT, 'phone'),
                'otp' => new external_value(PARAM_TEXT, 'otp'),
                'timeout' => new external_value(PARAM_TEXT, 'timeout'),
                'message' => new external_value(PARAM_TEXT, 'Error Message'),
                'success' => new external_value(PARAM_INT, 'Success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Call Otp sender service
     *
     * @param $otp
     * @param $phone
     * @throws dml_exception
     */
    public static function callOtpFuncction($phone)
    {
        $otp = self::generateOtp();
        // if set aws credentials
        if (get_config('auth_otp', 'enableaws') && get_config('auth_otp', 'aws_key') && get_config('auth_otp', 'aws_secrect')) {
            $key = get_config('auth_otp', 'aws_key');
            $secrect = get_config('auth_otp', 'aws_secrect');
            $region = get_config('auth_otp', 'aws_region');
            try {
                $sms = \auth_otp\awsotpservice::sendOtp($otp, $phone, $key, $secrect, $region);
//                if (isset($sms['isOptedOut']) && $sms['isOptedOut'] == true){
                    return ['status' => true, 'otp' => $otp, 'message' => get_string('otpsentsuccess', 'auth_otp')];
//                }
//                else{
//                    return ['status' => false, 'otp' => $otp, 'message' => get_string('otpsenterror', 'auth_otp')];
//                }
            } catch (Exception $e) {
                print_r($e);
                return ['status' => false, 'otp' => $otp, 'message' => get_string('otpsenterror', 'auth_otp')];
            }

        } else {// no sms credential found
            return ["status" => false, 'otp' => '', 'message' => get_string('otpsenterror', 'auth_otp')];
        }
    }

    /**
     * @return int
     */
    public static function generateOtp()
    {
        $digits = 6;
        $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        return strval($otp);
    }

    /**
     * Create new user and otp
     *
     * @param $phone
     * @return array
     * @throws dml_exception
     */
    public static function newUserHandle($phone, $otp, $countrycode)
    {
        global $DB;
        $currentdate = date("Y-m-d H:i:s");
        //Write a function to send otp to the user
        $data = $DB->execute("INSERT INTO {auth_otp_linked_login} (`phone`,`confirmtoken`,`username`,`otpcreated`,`fullphone`,`countrycode`) VALUES ('" . $phone . "'," . $otp . ",'" . $phone . "','" . $currentdate . "','" . $countrycode . ' ' . $phone . "','" . $countrycode . "')");

        /// remobe code
        $_SESSION['auth_otp']['credentials'] = [
            'otp' => $otp,
            'otpdatetime' => $currentdate,
            'username' => $phone,
            'country' => $countrycode,
        ];
        $authplugin = get_auth_plugin('otp');
        $user = new stdClass();
        $user->auth = 'otp';
        $user->confirmed = 1;
        $user->firstaccess = 0;
        $user->timecreated = time();
        $user->username = $phone;
        $user->firstname = $countrycode;
        $user->lastname = $phone;
        $user->password = '';
        $user->mnethostid = 1;
        $user->email = $phone . '@otp.com';

        $authplugin->create_user($user);
        return [
            'phone' => $phone,
            'otpdatetime' => $currentdate,
            'otp' => $otp
        ];
    }

    /**
     * Update Old user otp token
     *
     * @param $phone
     * @return array
     * @throws dml_exception
     */
    public static function oldUserHandle($phone, $otp,$countrycode)
    {
        global $DB;
        $currentdate = date("Y-m-d H:i:s");
        $data = $DB->execute("UPDATE {auth_otp_linked_login} SET `confirmtoken`= " . $otp . ",`otpcreated` = '" . $currentdate . "' where `phone` = '" . $phone . "'");

        /// remobe code
        $_SESSION['auth_otp']['credentials'] = [
            'otp' => $otp,
            'otpdatetime' => $currentdate,
            'username' => $phone,
            'country' => $countrycode,
        ];
        return [
            'phone' => $phone,
            'otpdatetime' => $currentdate,
            'otp' => $otp
        ];
    }

    /**
     * Calculate time diffrence between otp generated time to current time
     * @param $otpcreated
     * @return array
     * @throws Exception
     */
    public static function calculateTimeDiffrence($otpcreated)
    {
        $start = new DateTime(date("Y-m-d H:i:s"));
        $end = new DateTime(date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($otpcreated))));
        $diff = $end->diff($start);
        $daysInSecs = $diff->format('%r%a') * 24 * 60 * 60;
        $hoursInSecs = $diff->h * 60 * 60;
        $minsInSecs = $diff->i * 60;
        $seconds = $daysInSecs + $hoursInSecs + $minsInSecs + $diff->s;
        return ['invert' => $diff->invert, 'seconds' => $seconds];
    }

}
