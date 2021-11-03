
/**
 * Javascript controller for the "Actions" panel at the bottom of the page.
 *
 * @module     auth_otp/otp
 * @copyright  2021 Brain station 23 <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/ajax', 'core/notification'],
    function ($) {
    return {
        setup: function () {
            $(document).ready(function (){
                let time = $('#otptimeoutval').val();
                console.log(time);
                if (time){
                    timer(time);
                }
            });

            function timer(remaining){
                let timerOn = true;

                var m = Math.floor(remaining / 60);
                var s = remaining % 60;
                m = m < 10 ? '0' + m : m;
                s = s < 10 ? '0' + s : s;
                document.getElementById('timer').innerHTML = m + ':' + s;
                remaining -= 1;
                if (remaining >= 0 && timerOn) {
                    setTimeout(function () {
                        timer(remaining);
                    }, 1000);
                    return;
                }
                if (!timerOn) {
                    // Do validate stuff here
                    return;
                }
                // Do timeout stuff here
                alert('Timeout for otp');
                $('#sendotp').removeAttr('disabled');
                document.getElementById('timer').innerHTML = 'Resend Code';
            }

            return true;
        }
    };
});