<?php

declare(strict_types=1);

function validateYear(string $argv)
{
    if (strlen($argv[1]) !== 4) {
        return false;
    }
    return true;
}

function validateDay(string $argv)
{
    return true;
}

function executeDay(string $year, string $day)
{
    $solutionFile = implode(DIRECTORY_SEPARATOR, [__DIR__, $year, $day, 'solution.php']);
    if (is_file($solutionFile)) {
        echo "=== DAY {$day} SOLUTION ===\n";
        exec("php {$solutionFile}", $output);
        foreach ($output as $line) {
            echo "{$line}\n";
        }
        return true;
    }
    return false;
}

$year = date('Y');
$day = date('d');

if (isset($argv[1]) && validateYear($argv[1])) {
    $year = $argv[1];
}
if (isset($argv[2]) && validateDay($argv[2])) {
    $day = str_pad($argv[2], 2, "0", STR_PAD_LEFT);
} else {
    $day = null;
}

$yearDir = __DIR__ . DIRECTORY_SEPARATOR . $year;
if ($year && is_dir($yearDir) && !$day) {
    $dayDirs = scandir($yearDir);
    foreach ($dayDirs as $dayDir) {
        $output = [];
        executeDay($year, $dayDir);
    }
} elseif ($year && is_dir($yearDir) && $day) {
    if (!executeDay($year, $day)) {
        echo "ERROR, could not find solution for year '{$year}' and day '{$day}'";
    }
}
