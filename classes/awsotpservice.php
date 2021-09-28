<?php

namespace auth_otp;

use Aws\Exception\AwsException;


require_once $CFG->dirroot . '/auth/otp/vendor/autoload.php';

defined('MOODLE_INTERNAL') || die();

/**
 * awsotpservice configuration.
 *
 * @package    auth_oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class awsotpservice implements otpmethods
{
    private $key;

    private $secrect;

    private $region;

    public function __construct(string $key, string $secrect, string $region)
    {
        $this->key = $key;
        $this->secrect = $secrect;
        $this->region = $region;
    }

    public function sent(string $otp, string $phone)
    {
        $params = array(
            'credentials' => array(
                'key' => $this->key,
                'secret' => $this->secrect,
            ),
            'region' => $this->region, // < your aws from SNS Topic region
            'version' => 'latest'
        );
        $sns = new \Aws\Sns\SnsClient($params);

        $args = [
            'MessageAttributes' => [
                // You can put your senderId here. but first you have to verify the senderid by customer support of AWS then you can use your senderId.
                // If you don't have senderId then you can comment senderId
//                'AWS.SNS.SMS.SenderID' => [
//                    'DataType' => 'String',
//                    'StringValue' => 'OTP',
//                ],
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' => 'Promotional',
                ],
            ],
            'Message' => 'This is your one time password: ' . $otp,
            'PhoneNumber' => $phone,   // Provide phone number with country code
        ];

        try {
            $result = $sns->publish($args);
            return $result;
        } catch (AwsException $e) {
            // output error message if fails
            return $e->getMessage();
        }
    }

    public static function sendOtp(string $otp, string $phone, string $key, string $secrect, string $region = 'us-east-1')
    {
        $service = new awsotpservice($key, $secrect, $region);
        return $service->sent($otp, $phone);
    }
}