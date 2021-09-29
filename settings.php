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
 * Admin settings and defaults

 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use auth_otp\awsotpservice;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading('auth_otp/security',
        new lang_string('security', 'admin'), ''));

    $settings->add(new admin_setting_configcheckbox('auth_otp/enableaws',
        get_string('enableaws', 'auth_otp'),
        get_string('enableaws_help', 'auth_otp'), 0, PARAM_INT));

//    $choices = array();
//    $choices['0'] = get_string('awssettings','auth_otp');
//    $choices['1'] = get_string('twilosettions','auth_otp');
//    $choices['2'] = get_string('sslsmssettings','auth_otp');
//    $settings->add(new admin_setting_configselect('enablesmsservice', get_string('enablesmsservice','auth_otp'),
//        get_string('enablesmsservice_help','auth_otp'), 0, $choices));


    $settings->add(new admin_setting_configtext('auth_otp/aws_key',
        get_string('awskey', 'auth_otp'),
        get_string('awskey_help', 'auth_otp'),  'aws key', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('auth_otp/aws_secrect',
        get_string('awssecrect', 'auth_otp'),
        get_string('awssecrect_help', 'auth_otp'),  'aws secrect key', PARAM_TEXT));
    $settings->add(new admin_setting_configtext('auth_otp/aws_region',
        get_string('awsregion', 'auth_otp'),
        get_string('awsregion_help', 'auth_otp'),'ap-northeast-1', PARAM_TEXT));



    $settings->add(new admin_setting_configtext('auth_otp/revokethreshold',
        get_string('revokethreshold', 'auth_otp'),
        get_string('revokethreshold_help', 'auth_otp'), 3, PARAM_INT));

    $settings->add(new class(
        'auth_otp/minrequestperiod',
        get_string('minrequestperiod', 'auth_otp'),
        get_string('minrequestperiod_help', 'auth_otp')
    ) extends admin_setting_configtext {
        public function __construct($name, $visiblename, $description) {
            $readers = get_log_manager()->get_readers('\core\log\sql_reader');
            $logreader = reset($readers);
            parent::__construct($name, $visiblename, $description, $logreader ? 300 : 0, PARAM_INT);
            if (!$logreader && !empty($this->get_setting())) {
                $this->description .= ' '.get_string('logstorerequired', 'auth_otp',
                        (string)new moodle_url('/admin/settings.php', ['section' => 'managelogging'])
                    );
            }
        }
    });

}
