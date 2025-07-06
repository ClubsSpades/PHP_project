<?php
session_start();
session_unset();
session_destroy();
header('Location: /php/php_project/frontend/index.html');
exit;
?>