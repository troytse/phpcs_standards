<?php
$content = file_get_contents(__DIR__ . '/Dicts/en.txt');
$content = explode("\n", $content);
$content = array_map('trim', $content);
$content = array_combine($content, array_fill(0, count($content), 0));
file_put_contents(__DIR__ . '/Dicts/en.dat', serialize($content));
echo "OK\n";
exit;
