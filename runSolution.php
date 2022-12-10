<?php

declare(strict_types=1);

function validateYear(array $argv)
{
    if (!isset($argv[1]) || strlen($argv[1]) !== 4) {
        return false;
    }
    return true;
}

function validateDay(array $argv)
{
    if (!isset($argv[2])) {
        return false;
    }
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
if (validateYear($argv)) {
    $year = $argv[1];
}
if (validateDay($argv)) {
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
