<?php

if (!isset($argv[1]) || !isset($argv[2]) || !isset($argv[3])) {
    echo 'Usage script.php noOfFacilities noOfResources noOfAttribute';
    die();
}

$noOfFacilities = $argv[1];
$noOfResources  = $argv[2];
$noOfAttribute  = $argv[3];

$resources = [];
$facility  = [];
$required  = [];

for ($i = 0; $i < $noOfResources; $i++) {
    $amount         = rand(100, 10000);
    $resources[0][] = $amount;
}

//random risk allocation to facilities
$randomNumbers = [];
for ($i = 0; $i < $noOfFacilities; $i++) {
    $randomNumbersFacilities[] = rand(1, 100);
}

$randomSumFacilities = array_sum($randomNumbersFacilities);

for ($i = 0; $i < $noOfFacilities; $i++) {
    for ($j = 0; $j < $noOfAttribute; $j++) {
        if ($j == 0) //first attribute to be area of the facility serves
        {
            $amount = rand(100000, 500000);
        } elseif ($j == 1) //risk
        {
            $amount = rand(1000, 5000);
            //$amount = round(($randomNumbersFacilities[$i] / $randomSumFacilities) * 10000);
        } else {
            $amount = rand(100, 1000);
        }

        $facility[$i][$j] = $amount;
    }
}

//dividing noOfResources among facilities.
$randomNumbers = [];
for ($i = 0; $i < $noOfFacilities; $i++) {
    $randomNumbers[] = rand(1, 100);
}

$randomSum = array_sum($randomNumbers);

for ($i = 0; $i < $noOfResources; $i++) {

    if (mt_rand(0, 1)) {
        $resourceMax = $resources[0][$i] * 1.5;
    } else {
        $resourceMax = $resources[0][$i];
    }

    for ($j = 0; $j < $noOfFacilities; $j++) {
        $required[$j][$i] = round(($randomNumbers[$j] / $randomSum) * $resourceMax);
    }

}

writeCSV("resource_$noOfFacilities$noOfResources.csv", $resources);
writeCSV("facility_$noOfFacilities$noOfResources.csv", $facility);
writeCSV("required_$noOfFacilities$noOfResources.csv", $required);

function writeCSV($file, $array)
{
    $file = fopen('data_files/' . $file, "w");

    foreach ($array as $line) {
        fputcsv($file, $line);
    }

    fclose($file);
}
