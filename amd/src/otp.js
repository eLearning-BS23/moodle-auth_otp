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
 * Javascript controller for the "Actions" panel at the bottom of the page.
 *
 * @module     auth_otp/otp
 * @copyright  2021 Brain station 23 <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery','core/notification'], function($) {
    $(document).ready(function ($,notification) {
        $( "#sendotp" ).click(function() {
            var phone = $('#phone').val();
            if(phone){
                var phoneno = /^\d{10}$/;
                if((phoneno.test(phone)))
                {
                    $('#sendotp').attr('disabled','disabled');
                    let timerOn = true;
                    function timer(remaining) {
                        var m = Math.floor(remaining / 60);
                        var s = remaining % 60;
                        m = m < 10 ? '0' + m : m;
                        s = s < 10 ? '0' + s : s;
                        document.getElementById('timer').innerHTML = m + ':' + s;
                        remaining -= 1;
                        if(remaining >= 0 && timerOn) {
                            setTimeout(function() {
                                timer(remaining);
                            }, 1000);
                            return;
                        }
                        if(!timerOn) {
                            // Do validate stuff here
                            return;
                        }
                        // Do timeout stuff here
                        alert('Timeout for otp');
                        $('#sendotp').removeAttr('disabled');
                        document.getElementById('timer').innerHTML ='Resend Code';
                    }
                    timer(300);
                    $.ajax({
                        url: "handleotp.php",
                        method: "POST",
                        data: {phone: phone},
                        success: function (data) {
                            $('#otp-field').removeClass('d-none');
                        }
                    });
                }
                else
                {
                    return false;
                }
            }
        });
    });
});