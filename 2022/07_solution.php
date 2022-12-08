<?php

declare(strict_types=1);

$testInput = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "07_input_test.txt"));
$input = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "07_input.txt"));

function getDirectoryAsStringFromArray(array $directory)
{
    return str_replace('//', '/', implode('/', $directory) . '/');
}

function parseInputToDirectoryStructure(string $input)
{
    $lines = explode("\n", $input);
    $files = [];
    $currentDirectory = [];
    $lastCmd = null;
    for ($i = 0; $i < count($lines); $i++) {
        preg_match('/\$ (\w+)(.*)/', $lines[$i], $matches);
        $cmd = isset($matches[1]) ? trim($matches[1]) : '';
        $arg1 = isset($matches[2]) ? trim($matches[2]) : '';
        if ($cmd == 'ls') {
            $lastCmd = 'ls';
            continue;
        } elseif ($cmd == 'cd') {
            $lastCmd = 'cd';
            if ($arg1 == '..') {
                array_pop($currentDirectory);
            } else {
                $currentDirectory[] = $arg1;
            }
            if (!isset($files[getDirectoryAsStringFromArray($currentDirectory)])) {
                $files[getDirectoryAsStringFromArray($currentDirectory)] = [
                    'isDir' => true,
                    'size'  => 0
                ];
            }
            continue;
        }
        if ($lastCmd == 'ls') {
            // capture files and folders
            preg_match("/(\d+|dir) (.*)/", $lines[$i], $matches);
            if (isset($matches[1]) && $matches[1] == 'dir' && isset($matches[2])) {
                $dirFiles[getDirectoryAsStringFromArray($currentDirectory) . trim($matches[2]) . '/'] = [
                    'isDir' => true,
                    'size'  => 0,
                ];
            } elseif (isset($matches[1]) && isset($matches[2])) {
                $files[getDirectoryAsStringFromArray($currentDirectory) . trim($matches[2])] = [
                    'isDir' => false,
                    'size' => $matches[1],
                ];
                $temp = $currentDirectory;
                for ($y = count($temp); $y > 0; $y--) {
                    $files[getDirectoryAsStringFromArray($temp)]['size'] += $matches[1];
                    array_pop($temp);
                }
            }
        }
    }
    return $files;
}

function getTotalSizeOfDirectoriesWithMaxSize($lines, $maxSize)
{
    $directory = parseInputToDirectoryStructure($lines);
    $totSum = 0;
    foreach ($directory as $filename => $data) {
        if ($data['isDir'] && $data['size'] <= $maxSize) {
            $totSum += $data['size'];
        }
    }
    return $totSum;
}

function tests($lines)
{
    $totSum = getTotalSizeOfDirectoriesWithMaxSize($lines, 100000);
    if ($totSum == 95437) {
        echo "Tests: OK!\n";
    } else {
        echo "Tests: ERROR!\n";
    }
}

tests($testInput);

echo "Part one: " . getTotalSizeOfDirectoriesWithMaxSize($input, 100000) . "\n"; // 1513699

// part two
$directory = parseInputToDirectoryStructure($input);
$actualSize = 0;
foreach ($directory as $filename => $data) {
    if (is_array($data) && $filename == '/') {
        $actualSize = $data['size'];
    }
}
$totalAvailableSize = 70000000;
$totalSizeMax = $totalAvailableSize - 30000000;
$minDirSizeToDelete = $totalAvailableSize;
foreach ($directory as $filename => $data) {
    if ($data['isDir'] && ($actualSize - $data['size'] <= $totalSizeMax) && $data['size'] <= $minDirSizeToDelete) {
        $minDirSizeToDelete = $data['size'];
    }
}
echo "Part two: {$minDirSizeToDelete}" . "\n"; // 7991939
