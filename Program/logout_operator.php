<?php
setcookie("id", "", time() - 12 * 30 * 24 * 3600, "/");
setcookie("hash", "", time() - 12 * 30 * 24 * 3600, "/");
unset($_SESSION['kod_operatora']);
unset($_SESSION['administrator']);
unset($_SESSION['fio_operatora']);
unset($_SESSION['telephone']);
unset($_SESSION['login_operatora']);
unset($_SESSION['tip_operatora']);
redir2('userLogin');
?>
