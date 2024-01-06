<?php
// Backdoor Scanner

if (isset($argv[1]) && $argv[1] == 'scan') {
    scanBackdoors();
} else {
    echo "Usage: php backdoor_scanner.php scan\n";
}

function scanBackdoors() {
    $target = __DIR__; // Set the target directory (current directory in this case)

    $ceks = array(
        'base64_decode', 'system', 'file_put_contents', 'POST', 'file_get_contents',
        'multipart/form-data', 'passthru', 'popen', 'proc_open', 'exec', 'shell_exec',
        'eval', 'move_uploaded_file', 'NULL', 'htmlspecialchars_decode', 'gzinflate', 'function'
    );

    foreach (listFiles($target) as $key => $file) {
        $nFile = substr($file, -4, 4);

        if ($nFile == ".php" || $nFile == ".phtml" || $nFile == ".phar") {
            if ($file != __FILE__) {
                $ops = @file_get_contents($file);
                $op = strtolower($ops);
                $size = filesize($file);
                $last_modified = filemtime($file);
                $last = date("M-d-Y H:i", $last_modified);

                $found = false;

                foreach ($ceks as $ceker) {
                    if ($ceker !== "" && preg_match("/$ceker/", $op)) {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    echo "Potential backdoor found in file: $file\n";
                    echo "Last modified: $last GMT+9\n";
                    echo "File size: $size byte\n";
                    echo "---------------------------------------------\n";
                }
            }
        }
    }
}

function listFiles($dir) {
    $files = array();
    $inner_files = array();

    if ($dh = opendir($dir)) {
        while ($file = readdir($dh)) {
            if ($file != "." && $file != ".." && $file[0] != '.') {
                $fullPath = $dir . DIRECTORY_SEPARATOR . $file;

                if (is_dir($fullPath)) {
                    $inner_files = listFiles($fullPath);
                    if (is_array($inner_files)) {
                        $files = array_merge($files, $inner_files);
                    }
                } else {
                    array_push($files, $fullPath);
                }
            }
        }

        closedir($dh);
    }

    return $files;
}
?>
