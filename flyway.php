<?php

$options = getopt('c:s:t:', ['conf:', 'sql:', 'task:']);
$conf = $options['c'] ?? $options['conf'] ?? null;
$sql  = $options['s'] ?? $options['sql']  ?? null;
$task = $options['t'] ?? $options['task'] ?? '';

if (is_null($conf)) {
	echo 'INI file not specified!' . PHP_EOL;
	exit(1);
}

if (is_null($sql)) {
	echo 'SQL directory not specified!' . PHP_EOL;
	exit(1);
}

if (!file_exists($conf)) {
	echo 'INI file not found!' . PHP_EOL;
	exit(2);
}

if (!file_exists($sql)) {
	echo 'SQL directory not found!' . PHP_EOL;
	exit(2);
}

$db = parse_ini_file($conf);

if (!$db) {
	echo 'An error occurred during the INI file parsing!' . PHP_EOL;
	exit(3);
}

$cmd = 'docker pull flyway/flyway:latest-alpine && docker run -it --network container:' . $db['host'] . ' --rm -v ' . realpath($sql) . ':/flyway/sql flyway/flyway:latest-alpine -url=jdbc:mysql://' . $db['host'] . ':' . $db['port'] . ' -user=' . $db['username'] . ' -password=' . $db['password'] . ' -schemas="' . $db['dbname'] . '" ' . $task;
passthru($cmd);