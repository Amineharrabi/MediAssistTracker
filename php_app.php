<?php
// Set appropriate timeout values
set_time_limit(300); // 5 minutes
ini_set('max_execution_time', 300);
ini_set('default_socket_timeout', 300);

header('Location: /php_app/public/index.php');
exit;
?>
