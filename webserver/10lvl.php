<?php
$start_t = microtime(TRUE);
$main_dir = realpath('..');
$dirsep = DIRECTORY_SEPARATOR;
$descriptorspec = array(
    0 => array("pipe", "rb"),
    1 => array("pipe", "wb"),
    2 => array("pipe", "wb")
);
$proc_stdout = "";
$mwlapplier_start_t = microtime(TRUE);
$proc = proc_open($main_dir.$dirsep.'applier'.$dirsep.'MWLApplier', $descriptorspec, $pipes, $main_dir);
if(!is_resource($proc)) {
    echo "Error opening mwlapplier proc";
    return;
}
stream_set_blocking($pipes[1], FALSE);
$status = proc_get_status($proc);
while($status['running']) {
    sleep(0.2); # works nowhere (sleeps for 1sec)
    # time_nanosleep(0, 200000000); # works only on linux
    # usleep(200000); # works on windows php5+ and linux
    $read = fread($pipes[1], 1048576); # try to read 1MB
    $proc_stdout .= $read;
    $status = proc_get_status($proc);
}
$mwlapplier_end_t = microtime(TRUE);
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
$applier_stderr = stream_get_contents($pipes[2]);
fclose($newrom);
proc_close($proc);
$flips_start_t = microtime(TRUE);
$flipsproc = proc_open($main_dir.$dirsep.'flips -b --exact -c ../clean_smw.sfc '.escapeshellarg($newrom_name)." ".escapeshellarg($output_name), $descriptorspec, $flipspipes);
if(!is_resource($flipsproc)) {
    echo "Error opening flips proc";
    return;
}
$status = proc_get_status($flipsproc);
while($status['running']) {
    sleep(0.2); # works nowhere (sleeps for 1sec)
    # time_nanosleep(0, 200000000); # works only on linux
    # usleep(200000); # works on windows php5+ and linux
    $status = proc_get_status($flipsproc);
}
$flips_end_t = microtime(TRUE);
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
# header("Content-Type: application/octet-stream");
# header("Content-Disposition: attachment; filename=smwmaker.bps");
# echo $out;
$end_t = microtime(TRUE);
echo "Total time: ".number_format($end_t-$start_t,4)."<br>";
echo "Flips time: ".number_format($flips_end_t-$flips_start_t,4)."<br>";
echo "Applier time: ".number_format($mwlapplier_end_t-$mwlapplier_start_t,4)."<br>";
echo "Applier stderr:<pre>".$applier_stderr."</pre>";