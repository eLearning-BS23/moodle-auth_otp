<?php

namespace auth_otp;

use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;

require_once $CFG->dirroot . '/auth/otp/thirdparty/vendor/autoload.php';

defined('MOODLE_INTERNAL') || die();

/**
 * awsotpservice configuration.
 *
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
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
        /** AWS SNS Access Key ID */
        $access_key_id = $this->key;

        /** AWS SNS Secret Access Key */
        $secret = $this->secrect;

        /** Create SNS Client By Passing Credentials */
        $SnSclient = new SnsClient([
            'region' => $this->region,
            'version' => 'latest',
            'credentials' => [
                'key' => $access_key_id,
                'secret' => $secret,
            ],
        ]);

        /** Message data & Phone number that we want to send */
        $message = 'This is your One time password: '.$otp;

        /** NOTE: Make sure to put the country code properly else SMS wont get delivered */

        try {
            /** Few setting that you should not forget */
            $result = $SnSclient->publish([
                'MessageAttributes' => [
                    /* Pass the SENDERID here */
                    'AWS.SNS.SMS.SenderID' => [
                        'DataType' => 'String',
                        'StringValue' => 'otp',
                    ],
                    /* What kind of SMS you would like to deliver */
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => 'Transactional',
                    ],
                ],
                /* Message and phone number you would like to deliver */
                'Message' => $message,
                'PhoneNumber' => $phone,
            ]);
            /* Dump the output for debugging */
            return $result;
        } catch (AwsException $e) {
            // output error message if fails
            switch ($e->getAwsErrorCode()) {
                case 'EndpointDisabled':

                case 'InvalidParameter':
                    $message = 'Invalid parameter: Empty message';
                    break;

                case 'InvalidClientTokenId':
                    $message = 'The Key included in the request is invalid';
                    break;

                case 'SignatureDoesNotMatch':
                    $message = 'The Secrect token included in the request is invalid';
                    break;

                case 'NotFound':
                    $message = 'Invalid request';
                    break;
            }
            echo $message;
        }

    }

    public static function sendOtp(string $otp, string $phone, string $key, string $secrect, string $region = 'us-east-1')
    {
        $service = new awsotpservice($key, $secrect, $region);
        return $service->sent($otp, $phone);
    }
}