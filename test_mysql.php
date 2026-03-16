<?php
$sock = @fsockopen('174.138.49.116', 3306, $errno, $errstr, 10);
if ($sock) {
    echo "TCP connection OK\n";
    fclose($sock);
} else {
    echo "FAILED: {$errstr} (errno: {$errno})\n";
}
