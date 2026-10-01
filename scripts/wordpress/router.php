<?php
$root=dirname(__DIR__,2).'/wordpress-runtime/wordpress';
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)??'/');
$file=realpath($root.$path);
if($file&&str_starts_with(str_replace('\\','/',$file),str_replace('\\','/',$root).'/')&&(is_file($file)||(is_dir($file)&&is_file($file.'/index.php'))))return false;
$_SERVER['SCRIPT_NAME']='/index.php';require $root.'/index.php';
