<?php
// Donation deletion is disabled, including direct requests to the old URL.
http_response_code(403);
header('Content-Type: text/plain; charset=UTF-8');
echo 'Deleting donation records is disabled.';
