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
 * Phone OTP authentication plugin.
 *
 * @see self::user_login()
 * @see self::get_user_field()
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once("$CFG->libdir/formslib.php");

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/authlib.php');

use core\output\notification;


/**
 * Phone OTP authentication plugin.
 *
 * @see self::user_login()
 * @see self::get_user_field()
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_otp extends auth_plugin_base
{

    /**
     * The name of the component. Used by the configuration.
     */
    const COMPONENT_NAME = 'auth_otp';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->authtype = 'otp';
        $this->config = get_config(self::COMPONENT_NAME);
    }

    /**
     * Hook for overriding behaviour of login page.
     *  */
    function loginpage_hook()
    {
        global $PAGE, $OUTPUT, $CFG, $frm, $user;
//        $renderable =  new tool_policy\output\guestconsent();
//        echo $renderable->render();
        $PAGE->requires->jquery();
        $PAGE->requires->js_init_code("buttonsAddMethod = 'auto';");
        $content = str_replace(array("\n", "\r"), array("\\\n", "\\\r",), $this->get_buttons_string());
        $PAGE->requires->js_init_code("buttonsCode = '$content';");
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . "/auth/otp/script.js"));

    }

    private function get_buttons_string()
    {
        global $CFG;
        $content = <<<HTML
            <!-- Elcentra content starts -->
            <div class="moreproviderlink">
                <a class="btn btn-primary btn-block mt-3" href="{$CFG->wwwroot}/auth/otp/login.php">Otp Login</a> <br>
            </div>
            <!-- Elcentra content ends -->
        HTML;
        return $content;
    }

    /**
     * Matches only valid email from allowed domains. Validates credentials and
     * password if exists in current session or generates ones for session time
     * on empty password treated as one-time password request.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password)
    {
        global $CFG, $DB;
        $username = substr($username, -10);
        $phone = $username;
        if (empty($phone) || empty($password)) {
            return false;
        }
        // OTP already generated and base credentials matches.
        if (isset($_SESSION[self::COMPONENT_NAME])) {
            if (empty($password)) {
                return (bool)$this->redirect($username, 'otpsent', notification::NOTIFY_INFO);
            } else {
                $sql = 'select * from {auth_otp_linked_login} where `phone` = ' . $username . ' AND `confirmtoken` = ' . $password;
                $data = $DB->get_record_sql($sql);

                if ($data) {
                    $rec = $DB->get_record('user', array('username' => $username, 'auth' => 'otp'));
                    if (!$rec) {
                        $user = new stdClass();
                        $user->auth = $this->authtype;
                        $user->confirmed = 1;
                        $user->firstaccess = 0;
                        $user->timecreated = time();
                        $user->username = $username;
                        $user->firstname = '';
                        $user->lastname = '';
                        $user->password = '';
                        $user->mnethostid = 1;
                        $user->email = '';
                        $this->create_otp_user($user);
                    }
                    unset($_SESSION['auth_otp']['credentials']);
                    $this->resetOtp($phone);
                    return true;
                } else {// otp missmatch
                    isset($_SESSION['login_failed_count']) ?  $_SESSION['login_failed_count']=$_SESSION['login_failed_count'] : $_SESSION['login_failed_count']= 0;
                    $_SESSION['login_failed_count'] += 1;

                    // count faild login
                    if (!empty($this->config->revokethreshold) &&
                        $_SESSION['login_failed_count'] >= $this->config->revokethreshold) {
                        unset($_SESSION['login_failed_count']);
                        unset($_SESSION['auth_otp']['credentials']);
                        $this->resetOtp($phone);
                        return (bool)$this->redirect($username, 'otprevoked', notification::NOTIFY_INFO);
                    }

                    return (bool)$this->redirect($username, 'otpmissmatch', notification::NOTIFY_INFO);
                }
            }
        }
//        return false;
    }

    /**
     * get_user_field
     *
     * @param string $username
     * @param string $field
     * @return mixed
     * @see moodle_database::get_field()
     */
    protected function get_user_field(string $username, string $field)
    {
        global $CFG, $DB;
        return $DB->get_field('user', $field, array(
            'username' => $username,
            'mnethostid' => $CFG->mnet_localhost_id,
            'auth' => $this->authtype,
            'deleted' => 0,
        ));
    }

    /**
     * redirect
     *
     * @param string $username
     * @param string $msg
     * @return void
     */
    protected function redirect(string $username, string $msg, string $level)
    {
        global $CFG;
        $url = "$CFG->wwwroot/auth/otp/login.php";
        redirect($url . '?username=' . urlencode($username),
            get_string($msg, self::COMPONENT_NAME), null, $level);
    }

    public function resetOtp($phone)
    {
        global $DB;
        $data = $DB->execute("UPDATE {auth_otp_linked_login} SET `confirmtoken`= null,`otpcreated` = null where `phone` = '" . $phone . "'");
        return true;
    }

    public function create_user($user)
    {
        global $CFG, $PAGE, $OUTPUT, $DB;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->dirroot . '/user/editlib.php');

        if (empty($user->calendartype)) {
            $user->calendartype = $CFG->calendartype;
        }

        $user->id = user_create_user($user, false, false);
        $user = signup_setup_new_user($user);
        $user->auth = 'otp';
        user_update_user($user, false, false);

        // Trigger event.
        \core\event\user_created::create_from_userid($user->id)->trigger();
        $DB->set_field("user", "confirmed", 1, array("id" => $user->id));
    }
}
