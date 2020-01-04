<?php
$content = file_get_contents(__DIR__ . '/Dicts/en.dat');
$content = unserialize($content);
$content = array_keys($content);
file_put_contents(__DIR__ . '/Dicts/en.txt', implode("\n", $content));
echo "OK\n";
exit;
