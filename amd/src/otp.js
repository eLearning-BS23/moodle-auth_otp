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
define(['jquery', 'core/ajax', 'core/notification'], function ($, Ajax, Notification) {

    $("#sendotp").click(function () {
        var countrycode =$('.iti__selected-dial-code').html();
        var fullphone =  countrycode + $('#phone').val();
        $('input[name="full_number"]').val(fullphone);
        var phone = $('#phone').val();
        // ;
        if (phone) {
            var phoneno = /^\d{10}$/;
            if (phoneno.test(phone)) {
                let timerOn = true;
                function timer(remaining) {
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
                    $('#sendotp').removeClass('d-none');
                }
                // API Call
                var wsfunction = 'auth_otp_send_sms';
                var params = {
                    'phone': phone,
                    'countrycode' : countrycode
                };

                var request = {
                    methodname: wsfunction,
                    args: params
                };

                try {
                    Ajax.call([request])[0].done(function (data) {
                        if (data.success == 1) {
                            $('#otp-field').removeClass('d-none');
                            $('#sendotp').attr('disabled', 'disabled');
                            $('#phone').attr('disabled', 'disabled');
                            $('#phone').val(phone);
                            $('#username').val(phone);
                            timer(300);

                            Notification.addNotification({
                                message: data.message,
                                type: 'success'
                            });

                        } else {
                            Notification.addNotification({
                                message: 'Something went wrong to send otp Please Try again !!',
                                type: 'error'
                            });
                        }
                    }).fail(Notification.exception);
                } catch (e) {
                    console.log(e);
                }
            } else {
                Notification.addNotification({
                    message: 'insert Valid Phone number !!',
                    type: 'error'
                });
            }
        } else {
            Notification.addNotification({
                message: 'phone number can\'t be empty',
                type: 'error'
            });

        }
        return false;
    });
});