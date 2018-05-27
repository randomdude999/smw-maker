<?php
if(empty($_GET["id"])) {
    echo "No level id specified";
    return;
}
if(!ctype_digit($_GET["id"])) {
    echo "Invalid level ID";
    return;
}
if(!file_exists("../levels/$_GET[id]_main.mwl")) {
    echo "Level doesn't exist";
    return;
}
$main_dir = realpath('..');
$dirsep = DIRECTORY_SEPARATOR;
$descriptorspec = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w")
);
$proc_stdout = "";
$proc = proc_open($main_dir.$dirsep.'applier'.$dirsep.'MWLApplier '.$_GET["id"], $descriptorspec, $pipes, $main_dir);
if(!is_resource($proc)) {
    echo "Error opening mwlapplier proc";
    return;
}
stream_set_blocking($pipes[1], FALSE);
$status = proc_get_status($proc);
while($status['running']) {
    sleep(0.2);
    $read = fread($pipes[1], 1048576); # try to read 1MB
    $proc_stdout .= $read;
    $status = proc_get_status($proc);
}
if($status["exitcode"] !== 0) {
    echo "<pre>Error running applier (code $status[exitcode]):\n";
    echo stream_get_contents($pipes[2]);
    echo "</pre>";
    proc_close($proc);
    return;
}
$newrom_name = tempnam("", "");
$output_name = tempnam("", "");
$newrom = fopen($newrom_name, "wb");
fwrite($newrom, $proc_stdout);
fclose($newrom);
proc_close($proc);
$flipsproc = proc_open($main_dir.$dirsep.'flips -b -c ../clean_smw.sfc '.escapeshellarg($newrom_name)." ".escapeshellarg($output_name), $descriptorspec, $flipspipes);
if(!is_resource($flipsproc)) {
    echo "Error opening flips proc";
    return;
}
$status = proc_get_status($flipsproc);
while($status['running']) {
    sleep(0.2);
    $status = proc_get_status($flipsproc);
}
if($status["exitcode"] !== 0) {
    echo "<pre>Error running flips (code $status[exitcode]):\n";
    echo stream_get_contents($flipspipes[2]);
    echo "\n";
    echo stream_get_contents($flipspipes[1]);
    echo "</pre>";
    proc_close($flipsproc);
    return;
}
proc_close($flipsproc);
$out = file_get_contents($output_name);
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=smwmaker.bps");
echo $out;