<?php

declare(strict_types=1);

function tests()
{
    $input = <<<INPUT
    seeds: 79 14 55 13

    seed-to-soil map:
    50 98 2
    52 50 48

    soil-to-fertilizer map:
    0 15 37
    37 52 2
    39 0 15

    fertilizer-to-water map:
    49 53 8
    0 11 42
    42 0 7
    57 7 4

    water-to-light map:
    88 18 7
    18 25 70

    light-to-temperature map:
    45 77 23
    81 45 19
    68 64 13

    temperature-to-humidity map:
    0 69 1
    1 0 69

    humidity-to-location map:
    60 56 37
    56 93 4
    INPUT;

    if (solve($input) == 35) {
        echo "TEST ONE OK\n";
    } else {
        echo "TEST ONE FAIL\n";
    }

    if (solve($input, 'two') == 46) {
        echo "TEST TWO OK\n";
    } else {
        echo "TEST TWO FAIL\n";
    }
}

function solve(string $input, $part = 'one')
{
    $seeds = [];
    $keySorted = [
        'seed-to-soil map',
        'soil-to-fertilizer map',
        'fertilizer-to-water map',
        'water-to-light map',
        'light-to-temperature map',
        'temperature-to-humidity map',
        'humidity-to-location map',
    ];
    $map = [
        'seed-to-soil map'              => [],
        'soil-to-fertilizer map'        => [],
        'fertilizer-to-water map'       => [],
        'water-to-light map'            => [],
        'light-to-temperature map'      => [],
        'temperature-to-humidity map'   => [],
        'humidity-to-location map'      => [],
    ];
    $lines = explode("\n", $input);
    $mapKey = null;
    for ($i = 0; $i < count($lines); $i++) {
        if ($i === 0) {
            if ($part === 'one') {
                $seeds = substr($lines[$i], strlen('seeds: '));
                $seeds = explode(' ', $seeds);
            } elseif ($part === 'two') {
                $seeds = substr($lines[$i], strlen('seeds: '));
                $seeds = explode(' ', $seeds);
                for ($y = 0; $y < count($seeds);) {
                    $seeds[$y] = [
                        'start'     => $seeds[$y],
                        'length'    => $seeds[$y + 1],
                    ];
                    unset($seeds[$y + 1]);
                    $y += 2;
                }
            }
        }
        foreach (array_keys($map) as $key) {
            if (strpos($lines[$i], $key) !== false) {
                $mapKey = $key;
                continue 2;
            }
        }
        if ($mapKey) {
            preg_match('/(\d+)\s+(\d+)\s+(\d+)/', $lines[$i], $matches);
            if (isset($matches[0])) {
                $map[$mapKey][] = [
                    'destination'   => $matches[1],
                    'source'        => $matches[2],
                    'length'        => $matches[3],
                ];
            }
        }
    }
    $destinations = [];
    foreach ($seeds as $seed) {
        if ($part === 'one') {
            $seedKey = $seed;
            foreach ($keySorted as $mapKey) {
                $seed = findDestination($map[$mapKey], $seed);
            }
            $destinations[$seedKey] = $seed;
        } elseif ($part === 'two') {
            for ($i = $seed['start']; bccomp(bcadd($seed['start'], bcsub($seed['length'], '1')), $i) == 1; $i = bcadd($i, '1')) {
                $current = $i;
                foreach ($keySorted as $mapKey) {
                    $current = findDestination($map[$mapKey], $current);
                }
                $destinations[$i] = $current;
            }
        }
    }
    usort($destinations, function ($a, $b) {
        return bccomp($a, $b);
    });
    return $destinations[0];
}

function findDestination($mapToSearch, $source)
{
    $destination = $source;
    foreach ($mapToSearch as $data) {
        $high = bcadd($data['source'], $data['length']);
        $low = $data['source'];
        if (bccomp($source, $low) >= 0 && bccomp($source, $high) <= 0) {
            $steps = bcsub($source, $data['source']);
            return bcadd($data['destination'], $steps);
        }
    }
    return $destination;
}

tests();


$input1 = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'input.txt');
$solution = solve($input1);
echo "PART ONE: {$solution}\n";

$input1 = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'input.txt');
$solution = solve($input1, 'two');
echo "PART TWO: {$solution}\n";
