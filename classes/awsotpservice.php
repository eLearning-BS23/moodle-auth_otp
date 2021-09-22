<?php
namespace auth_otp;

use otpmethods;
require './vendor/autoload.php';

defined('MOODLE_INTERNAL') || die();

/**
 * awsotpservice configuration.
 *
 * @package    auth_oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class awsotpservice implements otpmethods {
    private $key;

    private $secrect;

    private $region;

    public function __construct(string $key,string $secrect,string $region)
    {
        $this->key = $key;
        $this->secrect = $secrect;
        $this->region = $region;
    }

    public function sent(string $otp, string $phone){

        $params = array(
            'credentials' => array(
                'key' => $this->key,
                'secret' => $this->secrect,
            ),
            'region' => $this->region, // < your aws from SNS Topic region
            'version' => 'latest'
        );
        $sns = new \Aws\Sns\SnsClient($params);

        $args = array(
            "MessageAttributes" => [
//                'AWS.SNS.SMS.SenderID' => [
//                    'DataType' => 'String',
//                    'StringValue' => 'YOUR_SENDER_ID'
//                ],
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' => 'Transactional'
                ]
            ],
            "Message" => "Your OTP code is ".$otp,
            "PhoneNumber" => $phone
        );

        return $result = $sns->publish($args);
    }

    public static function sendOtp(string $otp,string $phone,string $key,string $secrect,string $region='us-east-1')
    {
        $service = new awsotpservice($key,$secrect,$region);
        return $service->sent($otp,$phone);
    }
}