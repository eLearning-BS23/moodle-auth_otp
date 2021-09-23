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


defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/externallib.php');

class auth_otp_external extends external_api
{

    public static function send_otp_parameters()
    {

        return new external_function_parameters(
            array(
                'phone' => new external_value(PARAM_TEXT, 'phone')
            )
        );
    }

//
    public static function send_otp($phone)
    {
        global $DB, $CFG;
        $params = array(
            'phone' => $phone
        );
        // Validate the params.
        self::validate_parameters(self::send_otp_parameters(), $params);
        // check user exist and last otp time
        $sql = 'select * from {auth_otp_linked_login} where `phone` = ' . $phone;
        $data = $DB->get_record_sql($sql);


        // alreadu exist
        if ($data) {
            $seconds = self::calculateTimeDiffrence($data->otpcreated);
            // Otp Expired Update new otp in table
            if ($seconds >= 300) {
                $res = self::oldUserHandle($phone);
                $message = 'Otp Reset Successfully Please check your phone';
            } else { // already exist otp
                // self::newUserHandle($phone);
                $res = [
                    'otp' => $data->confirmtoken,
                    'otpdatetime' => $data->otpcreated
                ];
                $message = 'You have existing Otp Please Login with that';
            }
        } else { // New User
            $res = self::newUserHandle($phone);
            $message = 'Otp send Successfully Please check your phone';
        }
        $data = [
            'phone' => $phone,
            'otp' => $res['otp'],
            'timeout' => $res['otpdatetime'],
            'message' => $message,
            'warnings' => []
        ];
        return $data;
    }


    public static function send_otp_returns()
    {
        return new external_single_structure(
            array(
                'phone' => new external_value(PARAM_TEXT, 'phone'),
                'otp' => new external_value(PARAM_TEXT, 'otp'),
                'timeout' => new external_value(PARAM_TEXT, 'timeout'),
                'message' => new external_value(PARAM_TEXT, 'Error Message'),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function callOtpFuncction($otp, $phone)
    {
        if (get_config('auth_otp', 'enableaws') && get_config('auth_otp', 'aws_key') && get_config('auth_otp', 'aws_secrect')) {
            $key = get_config('auth_otp', 'aws_key');
            $secrect = get_config('auth_otp', 'aws_secrect');
            $region = get_config('auth_otp', 'aws_region');
            \auth_otp\awsotpservice::sendOtp($otp, $phone, $key, $secrect, $region);
            $_SESSION['auth_otp']['credentials'] = [
                'otp' => $otp,
                'otpdatetime' => date("Y-m-d H:i:s"),
                'username' => $phone
            ];
        } else {
            $_SESSION['auth_otp']['credentials'] = [
                'otp' => $otp,
                'otpdatetime' => date("Y-m-d H:i:s"),
                'username' => $phone
            ];
        }
    }

    public static function generateOtp()
    {
        $digits = 6;
        $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        return $otp;
    }

    public static function newUserHandle($phone)
    {
        global $DB;
        $otp = self::generateOtp();
        $currentdate = date("Y-m-d H:i:s");
        $data = $DB->execute("INSERT INTO {auth_otp_linked_login} (`phone`,`confirmtoken`,`username`,`otpcreated`) VALUES ('" . $phone . "'," . $otp . ",'" . $phone . "','" . $currentdate . "')");
        //Write a function to send otp to the user
        try {
            self::callOtpFuncction($otp, $phone);
        } catch (Exception $e) {
            print_r($e);
        }
        /// remobe code
        $_SESSION['auth_otp']['credentials'] = [
            'otp' => $otp,
            'otpdatetime' => $currentdate,
            'username' => $phone
        ];
        return [
            'phone' => $phone,
            'otpdatetime' => $currentdate,
            'otp' => $otp
        ];
    }

    public static function oldUserHandle($phone)
    {
        global $DB;
        $otp = self::generateOtp();
        $currentdate = date("Y-m-d H:i:s");
        // check phone number in database
        $data = $DB->execute("UPDATE {auth_otp_linked_login} SET `confirmtoken`= " . $otp . ",`otpcreated` = '" . $currentdate . "' where `phone` = '" . $phone . "'");
        //Write a function to send otp to the user
        try {
            self::callOtpFuncction($otp, $phone);
        } catch (Exception $e) {
            print_r($e);
        }

        /// remobe code
        $_SESSION['auth_otp']['credentials'] = [
            'otp' => $otp,
            'otpdatetime' => $currentdate,
            'username' => $phone
        ];
        return [
            'phone' => $phone,
            'otpdatetime' => $currentdate,
            'otp' => $otp
        ];
    }

    public static function calculateTimeDiffrence($otpcreated)
    {

        $start = new DateTime(date("Y-m-d H:i:s"));
        $end = new DateTime(date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($otpcreated))));
        $diff = $end->diff($start);

        $daysInSecs = $diff->format('%r%a') * 24 * 60 * 60;
        $hoursInSecs = $diff->h * 60 * 60;
        $minsInSecs = $diff->i * 60;
        $seconds = $daysInSecs + $hoursInSecs + $minsInSecs + $diff->s;
        return $seconds;
    }
}
