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
 * @package auth_oauth2
 * @copyright 2017 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once('../../config.php');
global $DB, $OUTPUT, $CFG, $PAGE, $SITE;

$PAGE->set_url('/courseteaser_admin/course_order.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'auth_otp'));
echo $OUTPUT->header();
$courseurl = $CFG->wwwroot . "/course/view.php";
$SESSION->wantsurl = $courseurl;
$token = \core\session\manager::get_login_token();
$url = $CFG->wwwroot . "/login/index.php";
?>
<div>
    <style>
        <?php
        include'otp_style.css';
        ?>
    </style>
    <?php
    $PAGE->requires->js_call_amd('auth_otp/intltelInput');
    $PAGE->requires->js_call_amd('auth_otp/utils');
    $PAGE->requires->js_call_amd('auth_otp/implement');
    $PAGE->requires->js_call_amd('auth_otp/otp');
    $usname= !empty($_GET['username']) ? $_GET['username'] : '';
    ?>
<!--    <div class="alert alert-primary" role="alert">-->
<!--        --><?php //$_GET['error'] ? $errors[$_GET['error']] : ''?>
<!--    </div>-->
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
                                    <input type="tel" name="username" value="<?php echo $usname; ?>" placeholder="" required id="phone">

                                    <?php
                                    if(empty($usname)) {
                                        ?>


                                        <div class="display:flex">
                                            <button type="button" id="sendotp">Send</button>
                                            <span id="timer"></span>
                                        </div>

                                        <?php

                                    }
                                    ?>
                                </div>
                                <div class="form-group <?php if(empty($usname)) { echo "d-none";} ?>" id="otp-field">
                                    <label for="password" class="sr-only">Otp</label>
                                    <input type="text" name="password" id="password" value=""
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
                                   data-placement="right" data-content="<div class=&quot;no-overflow&quot;><p>This site uses one session cookie, usually called MoodleSession. You must allow this cookie in your browser to provide continuity and to remain logged in when browsing the site. When you log out or close the browser, this cookie is destroyed (in your browser and on the server).</p>
</div> " data-html="true" tabindex="0" data-trigger="focus">
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

echo "<style>
body {
    margin: 0;
    padding: 0;
    background-color: #f1f1f1;
}

.box {
    width: 1270px;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-top: 25px;
}

#page_list li {
    padding: 16px;
    background-color: #f9f9f9;
    border: 1px dotted #ccc;
    cursor: move;
    margin-top: 12px;
}

#page_list li.ui-state-highlight {
    padding: 24px;
    background-color: #ffffcc;
    border: 1px dotted #ccc;
    cursor: move;
    margin-top: 12px;
}



section#region-main{
    background: transparent;
}
#page-header{
    visibility: hidden;
    background: transparent !important;
}
#page #page-content #region-main{
    border-bottom: none !important;
}
.card{
    width: 50%;
}

@media only screen and (max-width: 640px){
    .card{
        width: 100% !important;
    }
}
@media only screen and (max-width: 1024px){
    .card{
        width: 75% !important;
    }
}
//###############

* {
  box-sizing: border-box;
  -moz-box-sizing: border-box; }



.iti__hide {
  display: none; }

pre {
  margin: 0 !important;
  display: inline-block; }

.token.operator,
.token.entity,
.token.url,
.language-css .token.string,
.style .token.string,
.token.variable {
  background: none; }

input, button {
  height: 35px;
  margin: 0;
  padding: 6px 12px;
  border-radius: 2px;
  font-family: inherit;
  font-size: 100%;
  color: inherit; }
  input[disabled], button[disabled] {
    background-color: #eee; }

input, select {
  border: 1px solid #CCC;
  width: 250px; }

::-webkit-input-placeholder {
  color: #BBB; }

::-moz-placeholder {
  /* Firefox 19+ */
  color: #BBB;
  opacity: 1; }

:-ms-input-placeholder {
  color: #BBB; }

button {
  color: #FFF;
  background-color: #428BCA;
  border: 1px solid #357EBD; }
  button:hover {
    background-color: #3276B1;
    border-color: #285E8E;
    cursor: pointer; }

#result {
  margin-bottom: 100px; }
  
</style>";
