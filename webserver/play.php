<?php

function path_join(...$parts) {
    return implode(DIRECTORY_SEPARATOR, $parts);
}

$start_t = microtime(TRUE);
$main_dir = realpath('..');
$dirsep = DIRECTORY_SEPARATOR;

if(empty($_GET["id"])) {
    $cmd = path_join($main_dir, 'applier', 'MWLApplier');
} else {
    if(!ctype_digit($_GET["id"]) || !file_exists("../levels/$_GET[id]_main.mwl")) {
        die("Level doesn't exist");
    }
    if(file_exists("../levels/$_GET[id].bps")) {
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=smwmaker.bps");
        readfile("../levels/$_GET[id].bps");
        return;
    }
    $cmd = path_join($main_dir,"applier","MWLApplier")." $_GET[id]";
}

$newrom_name = tempnam("", "");
$stderr_name = tempnam("", "");
$old_wd = getcwd();
chdir($main_dir);
$mwlapplier_start_t = microtime(TRUE);
exec($cmd.' >'.escapeshellarg($newrom_name)." 2>".escapeshellarg($stderr_name), $unused, $applier_exitcode);
$mwlapplier_end_t = microtime(TRUE);
chdir($old_wd);
$applier_stderr = file_get_contents($stderr_name);
unlink($stderr_name);
if($applier_exitcode !== 0) {
    echo "<pre>Error running applier (code $applier_exitcode):\n";
    echo htmlspecialchars($applier_stderr);
    echo "</pre>";
    return;
}
$output_name = tempnam("", "");

$flips_start_t = microtime(TRUE);
exec(path_join($main_dir,'flips').' -b --exact -c ../clean_smw.sfc '.escapeshellarg($newrom_name)." ".escapeshellarg($output_name)." 2>&1", $flips_out, $flips_exitcode);
$flips_end_t = microtime(TRUE);
if($flips_exitcode !== 0) {
    echo "<pre>Error running flips (code $flips_exitcode):\n";
    echo htmlspecialchars(join("\n", $flips_out));
    echo "</pre>";
    return;
}

$out = file_get_contents($output_name);
unlink($output_name);
unlink($newrom_name);
if(!empty($_GET["id"]))
    file_put_contents("../levels/$_GET[id].bps", $out);
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=smwmaker.bps");
echo $out;
# $end_t = microtime(TRUE);
# echo "Total time: ".number_format($end_t-$start_t,4)."<br>";
# echo "Flips time: ".number_format($flips_end_t-$flips_start_t,4)."<br>";
# echo "Applier time: ".number_format($mwlapplier_end_t-$mwlapplier_start_t,4)."<br>";
# echo "Applier stderr:<pre>".$applier_stderr."</pre>";
