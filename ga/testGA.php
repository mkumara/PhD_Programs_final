<?php
require_once 'vendor/autoload.php';
require_once 'HelloFitnessCalculator.php';
require_once 'HelloSuccessCriteria.php';
require_once "CustomWord.php";
require_once "CustomModifyWord.php";
require_once "CustomOnePointCrossover.php";

require_once "Datasource.php";
require_once "Config.php";

if (isset($argv[1])) {
    $GLOBALS['file'] = $argv[1];
} else {
    $GLOBALS['file'] = null;
}
$GLOBALS['overAllocatingPenalty'] = 1000000;
$GLOBALS['riskColumn']            = 1;
$GLOBALS['areaColumn']            = 0;

$ds     = Datasource::singleton();
$config = Config::getConfig();

//die();
error_reporting(E_ERROR);
$required = $ds->getdataRequired();
$chrome   = $ds->getChromosome($required);
$avail    = $ds->getdataAvailable();
echo "Available" . PHP_EOL;
var_export($avail);
echo "Loacked Resources" . PHP_EOL;
var_export($ds->getLockResources());

$target   = $ds->getString(); //'50105702058020505010570205080205';
$alphabet = array_unique(array_merge(range('0', '100'), str_split($target))); // not used by this program

$gaEngine = new \PW\GA\GeneticAlgorithm(
    new HelloFitnessCalculator($target),
    new CustomOnePointCrossover(),
    new CustomModifyWord($alphabet),
    new \PW\GA\Config([
        \PW\GA\Config::SORT_DIR         => \PW\GA\GeneticAlgorithm::SORT_DIR_DESC,
        \PW\GA\Config::POPULATION_COUNT => $config['population'],
        \PW\GA\Config::CHURN_ENTROPY    => 0.9,
        \PW\GA\Config::MUTATE_ENTROPY   => 0.9,
        \PW\GA\Config::WEIGHTING_COEF   => 0.5,
    ])
);

//check if availability is abundant so that no need a resource allocation before executing
$start = microtime(true);
$gaEngine->initPopulation(new CustomWord($alphabet, $ds->getChromosomeLength()))
    ->optimiseUntil(new HelloSuccessCriteria($target), $config['iterations']);
$end = microtime(true);

$fittest = $gaEngine->getFittest()->getValue();
echo "Allcated" . PHP_EOL;
$ds->printAllocationResult($fittest);
//echo "Required".PHP_EOL;
//var_export($chrome);
echo PHP_EOL;
var_export(["Time" => ($end - $start)]);

//Points
//Consider only resources that are less than required
//but if you do all it may save resources as facilities may request more than it needs.
