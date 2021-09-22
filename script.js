$(document).ready(
    function () {
        if ($("#auth_custom_location").length > 0) {
            $("#auth_custom_location").append(buttonsCode);
        } else {
            $formObj = $("input[name='username']").closest("form");
            if ($formObj.length > 0) {
                $($formObj).each(function (i, formItem) {
                    $username = $(formItem).find("input[name='username']").val();
                    $password = $(formItem).find("input[name='password']").val();
                    if($username!="guest" || $password!="guest") {
                        $(formItem).append(buttonsCode);
                    }
                });
            }
        }
    }


)