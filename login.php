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
 * Open ID authentication. This file is a simple login entry point for OAuth identity providers.
 *
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
global $USER, $DB, $OUTPUT, $CFG, $PAGE, $SITE, $SESSION;

if(isloggedin()){
    return redirect($CFG->wwwroot.'/my');
}
$PAGE->set_url('/courseteaser_admin/course_order.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'auth_otp'));
echo $OUTPUT->header();
$courseurl = $CFG->wwwroot . "/my/";
$SESSION->wantsurl = $courseurl;
$token = \core\session\manager::get_login_token();
$url = $CFG->wwwroot . "/login/index.php";
?>
    <div>
        <style>
            <?php
            require_once ('otp_style.css');
            ?>
        </style>
        <?php
        $PAGE->requires->js_call_amd('auth_otp/intltelInput');
//        $PAGE->requires->js_call_amd('auth_otp/utils');
        $PAGE->requires->js_call_amd('auth_otp/implement');
        $PAGE->requires->js_call_amd('auth_otp/otp');
        $PAGE->requires->js_call_amd('auth_otp/timer', 'setup', array());

        $timeout = true;
        $otptimeoutval = '';
        if (isset($_SESSION['auth_otp']['credentials'])) {
            $start = new DateTime(date("Y-m-d H:i:s"));
            $end = new DateTime(
                    date('Y-m-d H:i:s',
                        strtotime('+5 minutes',
                        strtotime($_SESSION['auth_otp']['credentials']['otpdatetime']))
                    )
            );
            $diff = $end->diff($start);
            $daysinsecs = $diff->format('%r%a') * 24 * 60 * 60;
            $hoursinsecs = $diff->h * 60 * 60;
            $minsinsecs = $diff->i * 60;
            $seconds = $daysinsecs + $hoursinsecs + $minsinsecs + $diff->s;
            if ($diff->invert == 1 &&
                $seconds <= get_config('auth_otp', 'minrequestperiod')) {
                $timeout = false;
                $otptimeoutval = $seconds;
            } else {
                unset($_SESSION['auth_otp']['credentials']);
            }
        }
        $usname = !empty($_SESSION['auth_otp']['credentials']['username']) ? $_SESSION['auth_otp']['credentials']['country'] . '' . $_SESSION['auth_otp']['credentials']['username'] : '';
        ?>
        <div class="d-flex justify-content-center">
            <div class="card">
                <div class="card-block">
                    <h2 class="card-header text-center"><?= $SITE->fullname; ?> login</h2>
                    <div class="card-body">
                        <div class="sr-only">
                            <a href="<?= $CFG->wwwroot; ?>/login/signup.php">Skip to create new account</a>
                        </div>


                        <div class="row justify-content-md-center">

                            <div class="col-md-5">
                                <form class="mt-3" action="<?= $url; ?>" method="post"
                                      id="login">
                                    <input id="anchor" type="hidden" name="anchor" value="">
                                    <script>document.getElementById('anchor').value = location.hash;</script>
                                    <input type="hidden" name="logintoken" value="<?= $token ?>">
                                    <div class="form-group">
                                        <label for="username" class="sr-only">
                                            Username
                                        </label>
                                        <input type="tel" name="phone" id="phone" class="form-control"
                                               value="<?php echo $usname; ?>" placeholder="phone" autocomplete="phone">
                                        <input type="hidden" name="username" value="<?php echo $usname; ?>"
                                               placeholder="" required id="username">

                                        <div class="display:flex">
                                            <button class="<?php if (!empty($usname)) {
                                                echo "d-none";
                                            } ?>" type="button" id="sendotp">Send
                                            </button>
                                            <span id="timer"></span>
                                            <input type="hidden" name="timeout" id="otptimeoutval"
                                                   value="<?php echo $otptimeoutval; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group <?php if (empty($usname)) {
                                        echo "d-none";
                                    } ?>" id="otp-field">
                                        <label for="password" class="sr-only">Otp</label>
                                        <input type="text" required name="password" id="password" value=""
                                               class="form-control"
                                               placeholder="OTP">
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block mt-3" id="loginbtn">Log in
                                    </button>
                                </form>
                            </div>

                            <div class="col-md-5">
                                <div class="forgetpass mt-3">
                                    <p><a href="<?= $CFG->wwwroot ?>/login/forgot_password.php">Forgotten your
                                            username or password?</a></p>
                                </div>
                                <div class="mt-3">
                                    Cookies must be enabled in your browser
                                    <a class="btn btn-link p-0" role="button" data-container="body"
                                       data-toggle="popover"
                                       data-placement="right" data-content="<div class=&quot;no-overflow&quot;>
                                       <p>This site uses one session cookie, usually called MoodleSession.
                                       You must allow this cookie in your browser to provide continuity and
                                       to remain logged in when browsing the site. When you log out or
                                       close the browser, this cookie is destroyed
                                       (in your browser and on the server).</p></div>"
                                       data-html="true" tabindex="0" data-trigger="focus">
                                        <i class="icon fa fa-question-circle text-info fa-fw "
                                           title="Help with Cookies must be enabled in your browser"
                                           aria-label="Help with Cookies must be enabled in your browser"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
        </div>

    </div>
<?php
echo $OUTPUT->footer();
