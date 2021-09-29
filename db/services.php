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
 * Services for the auth_otp plugin.
 *
 * @package    process_otp
 * @copyright  2021 Brain Station 23
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */


defined('MOODLE_INTERNAL') || die;
$functions = array(
    'auth_otp_send_sms' => array(
        'classname' => 'auth_otp_external',
        'methodname' => 'send_otp',
        'classpath'   => 'auth/otp/classes/externallib.php',
        'loginrequired' => false,
        'description' => 'Process otp and send sms.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => '',// TODO: need to add capability.
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);


