<?php
class Datasource
{

    protected $loackResources;
    protected $loackChromosome;
    private static $instance;

    public function __construct()
    {
        if ($GLOBALS['file'] != null) {
            $this->loadData("data_files/required_" . $GLOBALS['file'] . ".csv", 1);

            $this->loadData("data_files/resource_" . $GLOBALS['file'] . ".csv", 3);

            $this->loadData("data_files/facility_" . $GLOBALS['file'] . ".csv", 2);

        } else {
            $this->loadData("data_files/required.csv", 1);

            $this->loadData("data_files/resource.csv", 3);

            $this->loadData("data_files/facility.csv", 2);
        }
    }

// Hold an instance of the class

    // The singleton method
    public static function singleton()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Datasource();
        }
        return self::$instance;
    }

    //[A=>50, B=>0]

    protected $data_required = array(array(50, 10, 5),
        array(70, 20, 5),
        array(80, 20, 50),
        array(50, 10, 5),
        array(70, 20, 50),
        array(80, 20, 5));

    protected $data_available = array(

        350,
        250,
        300,
    );
//[area,pop]
    protected $data_facility = array(array(20, 200),
        array(100, 1000),
        array(10, 40),
        array(20, 200),
        array(100, 1000),
        array(10, 40));

    public function getdataRequired()
    {
        return $this->data_required;
    }

    public function getdataAvailable()
    {
        return $this->data_available;
    }

    public function getdataFacility()
    {
        return $this->data_facility;
    }

    public function getResourceCount()
    {
        return count($this->data_available);
    }

    /*  public function getRisk($allocated)
    {
    $risk            = 0;
    $allocated_array = $this->getArrayFromChromosome($allocated);

    for ($i = 0; $i < count($allocated_array); $i++) {
    $add = 0;
    for ($j = 0; $j < count($this->data_required[0]); $j++) {
    $mul = abs($allocated_array[$i][$j] - $this->data_required[$i][$j]);

    if (($allocated_array[$i][$j] - $this->data_required[$i][$j]) > 0) {
    $penalty = ($allocated_array[$i][$j] - $this->data_required[$i][$j]) * $GLOBALS['overAllocatingPenalty'];
    } else {
    $penalty = 1;
    }

    for ($k = 0; $k < count($this->data_facility[0]); $k++) {

    $mul *= $this->data_facility[$i][$k] * $penalty;

    }
    $add += ($mul / $this->getTotalArea($GLOBALS['areaColumn']));
    }
    $risk += $add;
    }

    return $risk;
    }*/

    public function getRisk($allocated)
    {
        $risk            = 0;
        $allocated_array = $this->getArrayFromChromosome($allocated);

        $totalRisk = 0;
        for ($i = 0; $i < count($allocated_array); $i++) {
            $add = 0;
            for ($j = 0; $j < count($this->data_required[0]); $j++) {
                $add += abs($allocated_array[$i][$j] - $this->data_required[$i][$j]);

            }

            $add *= $this->data_facility[$i][$GLOBALS['riskColumn']];
            $totalRisk += $add;
        }
        //echo $totalRisk . PHP_EOL;
        return $totalRisk;
    }

//there is a memory leak when using this function
    public function getMaxRisk()
    {
        if (isset($GLOBALS['maxRisk'])) {
            return $GLOBALS['maxRisk'];
        }
        $chromosomeLength  = $this->getChromosomeLength();
        $maxRiskChromosome = array_fill(0, $chromosomeLength, 0);
        $maxRisk           = $this->getRisk($maxRiskChromosome);

        $GLOBALS['maxRisk'] = $maxRisk;

        return $maxRisk;
    }

    public function getChromosome($array)
    {
        $num_facilities = count($this->data_required);
        $num_resources  = count($this->data_available);

        $chromosome = array();

        for ($i = 0; $i < $num_facilities; $i++) {
            for ($j = 0; $j < $num_resources; $j++) {
                $chromosome[] = $array[$i][$j];
            }
        }
        return $chromosome;
    }

    public function getArrayFromChromosome($chrome)
    {
        $array = array();

        $num_resources = count($this->data_available);
        $count         = 0;
        for ($i = 0; $i < count($chrome); $i = $i + $num_resources) {
            for ($j = 0; $j < $num_resources; $j++) {
                $array[$count][$j] = $chrome[$i + $j];

            }
            $count++;
        }

        return $array;
    }

    public function getTotalArea($index)
    {
        $sum = 0;
        for ($i = 0; $i < count($this->data_facility); $i++) {
            $sum += $this->data_facility[$i][$index];
        }
        return $sum;
    }

    public function getString()
    {
        $str = '';
        $req = $this->getdataRequired();
        for ($i = 0; $i < count($req); $i++) {
            for ($j = 0; $j < count($req[0]); $j++) {
                $str .= $req[$i][$j];
            }
        }

        return $str;
    }

    public function loadData($file, $type)
    {
        $ar = array();
        if (($handle = fopen($file, "r")) !== false) {

            switch ($type) {

                case 1:{
                        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                            $ar[] = $data;

                        }
                        $this->data_required = $ar;
                        break;
                    }
                case 2:
                    {
                        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                            $ar[] = $data;
                        }
                        // $this->normalize($ar);
                        $this->data_facility = $ar;
                        break;
                    }
                case 3:
                    {
                        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                            $ar = $data;
                        }
                        $this->data_available = $ar;
                        break;
                    }
            }
            fclose($handle);
        }

    }

    public function normalize(&$ar)
    {
        $total   = array();
        $numRows = count($ar);

        for ($i = 0; $i < $numRows; $i++) {
            $total[$i] = array_sum(array_column($ar, $i));
        }

        for ($i = 0; $i < $numRows; $i++) {
            for ($j = 0; $j < count($ar[$i]); $j++) {
                $ar[$i][$j] = $ar[$i][$j] / $total[$j];

            }
        }
    }

    public function getChromosomeLength()
    {
        return count($this->data_required) * count($this->data_required[0]);
    }

    public function getLockResources()
    {

        if (isset($this->loackResources)) {
            return $this->loackResources;
        }
        $availalbe    = $this->getdataAvailable();
        $required     = $this->getdataRequired();
        $numResources = count($availalbe);
        $column_sum   = 0;

        $lockArray = array_fill(0, $numResources, 0);
        for ($k = 0; $k < $numResources; $k++) {
            $column_sum = array_sum(array_column($required, $k));
            if ($column_sum <= $availalbe[$k]) {
                $lockArray[$k] = 1;
            }

            $column_sum = 0;

        }

        $this->loackResources = $lockArray;
        return $lockArray;
    }

    public function getLockChromosome()
    {
        if (isset($this->loackChromosome)) {
            return $this->loackChromosome;
        }

        $lockedResources = $this->getLockResources();
        $availalbe       = $this->getdataAvailable();
        $numResources    = count($availalbe);

        $loackChromosome = array_fill(0, $this->getChromosomeLength(), 0);

        for ($i = 0; $i < $this->getChromosomeLength(); $i = $i + $numResources) {
            for ($j = 0; $j < $numResources; $j++) {
                if ($lockedResources[$j] == 1) {
                    $loackChromosome[$i + $j] = 1;
                }
            }
        }

        $this->loackChromosome = $loackChromosome;
        return $loackChromosome;
    }

    public function printAllocationResult($allocated)
    {
        $availalbe    = $this->getdataAvailable();
        $required     = $this->getdataRequired();
        $numResources = count($availalbe);
        $requested    = 0;
        $given        = 0;

        for ($i = 0; $i < count($allocated); $i = $i + $numResources) {
            $facility = $i / $numResources;

            echo "Facility Id: " . $facility . PHP_EOL;
            echo "Resource ID \t Requested\t Allocated" . PHP_EOL;
            for ($j = 0; $j < $numResources; $j++) {
                $requested = $required[$i / $numResources][$j];
                $given     = $allocated[$i + $j];
                echo "$j\t$requested\t$given" . PHP_EOL;

            }
            echo PHP_EOL;
        }

    }

//this is used in decendent section
    public function getCloseness($target, $chromosomeB)
    {
        $distance    = 0;
        $maxDistance = 0;
        for ($i = 0; $i < count($target); $i++) {
            $distance += abs($target[$i] - $chromosomeB[$i]);
            $maxDistance += $target[$i];

        }
        return $distance / $maxDistance * 100;
    }

}
