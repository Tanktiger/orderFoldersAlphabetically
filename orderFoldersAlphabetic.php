<?php
/**
 * Created by PhpStorm.
 * User: Tanktiger
 * Date: 08.02.2015
 * Time: 19:06
 */
$actualFolder = realpath(NULL);
echo 'Folder from root ["'.$actualFolder.'"]';
$handle = fopen ("php://stdin","r");
$line = preg_replace('/\r|\n/','',trim(fgets($handle)));
$path = '';
if ($line != $actualFolder && $line != '') {
    $path = $line;
    if (!file_exists($line) || !is_dir($line)) {
        echo PHP_EOL . 'entered folder does not exist or is false' . PHP_EOL;
        echo PHP_EOL . $line . PHP_EOL;
        exit();
    }
} else {
    $path = $actualFolder;
}
echo "\n";
$path = rtrim($path,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
echo $path . "\n";
$directories = glob($path . '*' , GLOB_ONLYDIR);
//create alphabetic folder
echo PHP_EOL . 'creating folders alphabeticly' . PHP_EOL;
$alphaFolder = array();
foreach (range('A', 'Z') as $char) {
    if (!file_exists($path . $char)) {
        mkdir($path.$char);
    }
    $alphaFolder[] = $path.$char;
}
echo PHP_EOL . 'creating finished' . PHP_EOL;

echo PHP_EOL . 'Moving all Folders in the right place' . PHP_EOL;
$regexConformPath = str_replace(array('\\', ':', '.', '/', '(', ')', '[', ']', '$', '^', '{', '}', '?'),
                                array('\\\\','\\:', '\\.', '\\/', '\\(', '\\)', '\\[', '\\]', '\\$', '\\^','\\{', '\\}', '\\?'),
                                $path);
foreach ($directories as $dir) {
    if (FALSE === array_search($dir,$alphaFolder)) {
        $matches = null;
        preg_match('/' . $regexConformPath . '([a-zA-Z])/', $dir, $matches);
        if (isset($matches[1])) {
            $dest = $path.strtoupper($matches[1]);
            $destmatches = null;
            preg_match('/' . $regexConformPath . '([a-zA-Z].*)/', $dir, $destmatches);
            if (isset($destmatches[1])) {
                $dest .= DIRECTORY_SEPARATOR . $destmatches[1];
            }
            echo PHP_EOL . $dir . ' --> ' . $dest . PHP_EOL;

            recurse_copy($dir, $dest);
            rrmdir($dir);
        }
    }
}
echo PHP_EOL . 'Finished' . PHP_EOL;
exit();

function recurse_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . DIRECTORY_SEPARATOR . $file) ) {
                recurse_copy($src . DIRECTORY_SEPARATOR . $file,$dst . DIRECTORY_SEPARATOR . $file);
            } else {
                copy($src . DIRECTORY_SEPARATOR . $file,$dst . DIRECTORY_SEPARATOR . $file);
            }
        }
    }
    closedir($dir);
}

function rrmdir($dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file)
            if ($file != "." && $file != "..") rrmdir("$dir/$file");
        rmdir($dir);
    }
    else if (file_exists($dir)) unlink($dir);
}