<?php

use PW\GA\CrossoverMethod\OnePointCrossover;

require_once "Datasource.php";

class CustomOnePointCrossover extends OnePointCrossover
{
    protected $ds;
    protected $resource_count;

    public function __construct()
    {
        $this->ds             = Datasource::singleton();
        $this->resource_count = $this->ds->getResourceCount();
    }
    /**
     * @param array $parentA
     * @param array $parentB
     * @return array
     */
    public function crossover(array $parentA, array $parentB)
    {
        //return [$parentA, $parentB];
        $valueCount = count($parentA);
        $cross      = mt_rand(0, $valueCount - 1);

        $crossoverPoint = $cross - ($cross % $this->resource_count);

        if ($crossoverPoint < 0) {
            $crossoverPoint = 0;
        }

        return [
            array_merge(
                array_slice($parentA, 0, $crossoverPoint),
                array_slice($parentB, $crossoverPoint)
            ),
            array_merge(
                array_slice($parentB, 0, $crossoverPoint),
                array_slice($parentA, $crossoverPoint)
            ),
        ];
    }

}
