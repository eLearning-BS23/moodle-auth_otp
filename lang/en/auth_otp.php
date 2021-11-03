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
 * Strings for component 'auth_otp', language 'en'.
 *
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'OTP';
$string['eventotpgenerated'] = 'Password generated';
$string['enablesmsservice'] = 'Enable sms Service';
$string['enablesmsservice_help'] = 'Enable sms Service from this option';
$string['enableaws'] = 'Enable AWS SNS Service';
$string['enableaws_help'] = 'Enable AWS SNS Service';
$string['awssettings'] = 'Aws SNS';
$string['twilosettions'] = 'Twilo SMS';
$string['sslsmssettings'] = 'SSL Sms';
$string['awsregion'] = 'Aws SnS Region';
$string['awsregion_help'] = 'Put Your Aws SnS Region';
$string['awskey_help'] = 'AWS Access Key that you get in aws sms service';
$string['awskey'] = 'AWS Access Key';
$string['awssecrect'] = 'AWS secrect Key that you get in aws sms service';
$string['awssecrect_help'] = 'AWS secrect Key';
$string['eventotprevoked'] = 'Password revoked';
$string['otpgeneratedsubj'] = 'One-time password';
$string['otpmissmatch'] = 'Your Otp Is not correct Please recheck it again';
$string['otpgeneratedtext'] = 'One-time password for current session: {$a->password}';
$string['otpsentsuccess'] = 'One-time password send Successfully Please check your phone';
$string['otpsenterror'] = 'An error occurred while sending one-time password.';
$string['otpsentinfo'] = 'One-time password for current session was already generated Please check.';
$string['otprevoked'] = 'Previously generated password has been revoked due to exceeding the login failure threshold.';
$string['otpperiodwarning'] = 'Minimum period after which another password can be generated not preserved. Try again later.';
$string['revokethreshold'] = 'Revoke threshold';
$string['revokethreshold_help'] = 'Login failures limit causing revoke of the generated password (0 - unlimited).';
$string['minrequestperiod'] = 'Minium period';
$string['minrequestperiod_help'] = 'A time in seconds after which another password can be generated (0 - unrestricted). Enabled logstore required.';
$string['logstorerequired'] = '<b>Notice: no working logstore! <a href="{$a}">Enable logstore</a> or set time to 0.</b>';
$string['fieldsmapping'] = 'User profile fields mapping on signup';
$string['fieldsmapping_pattern'] = 'Pattern';
$string['fieldsmapping_pattern_help'] = 'Capturing groups PCRE pattern.';
$string['fieldsmapping_mapping'] = 'Mapping';
$string['fieldsmapping_mapping_help'] = 'Mapping expressions.';
$string['awssenderid'] = 'AWS Sendeer id.';
$string['awssenderid_help'] = 'User get sms with which identity';
$string['enabletwilio'] = 'Enable Twilio SMS Service';
$string['enabletwilio_help'] = 'Enable Twilio SMS Service and stop other services';
$string['twiliossid'] = 'Twilio ssid';
$string['twiliossid_help'] = 'Twilio ssid';
$string['twiliotoken'] = 'Twilio Token';
$string['twiliotoken_help'] = 'Twilio Token';
$string['twilionumber'] = 'Twilio Number';
$string['twilionumber_help'] = 'Twilio Registered Number';
$string['InvalidParameter'] = 'Invalid parameter: Empty message';
$string['InvalidClientTokenId'] = 'The Key included in the request is invalid';
$string['SignatureDoesNotMatch'] = 'The Secrect token included in the request is invalid';
$string['NotFound'] = 'Invalid request';
$string['IncompleteSignature'] = 'Wrong Credential please check aws credentials';
