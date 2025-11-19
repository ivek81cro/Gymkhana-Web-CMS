<?php
echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . "<br>\n";
echo 'post_max_size: ' . ini_get('post_max_size') . "<br>\n";
echo 'max_file_uploads: ' . ini_get('max_file_uploads') . "<br>\n";
echo 'memory_limit: ' . ini_get('memory_limit') . "<br>\n";
echo 'CONTENT_LENGTH (zadnji POST): ' . ($_SERVER['CONTENT_LENGTH'] ?? 'n/a') . " bytes<br>\n";
?>
