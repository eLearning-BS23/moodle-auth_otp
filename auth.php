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
 * Authentication Plugin: One time password (OTP) Authentication
 * Send One time password to user's Mobile or email and check against otp to locally stored otp.
 *
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 Ltd. <brainstation-23.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * One time password (OTP) Authentication plugin.
 *
 * @package    auth
 * @subpackage otp
 * @copyright  2021 Brain Station 23 Ltd. <brainstation-23.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_otp extends auth_plugin_base {

    /**
     * This is the primary method that is used by the authenticate_user_login()
     * function in moodlelib.php.
     *
     * This method should return a boolean indicating
     * whether or not the username and password authenticate successfully.
     *
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     *
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password)
    {
        
    }
    
}