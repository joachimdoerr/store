<?php
/**
 * User: joachimdoerr
 * Date: 05.12.16
 * Time: 08:17
 */


###
# Register EP
#
#                $login_status = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_FAILED', $login_status, array(
#                                   'login_name' => $login_name, 'login_psw' => $login_psw, 'login_stay' => $login_stay, 'logout' => $logout, 'query_extras' => $query_extras)));
###


if (rex::isBackend() && rex::getUser()) {

}