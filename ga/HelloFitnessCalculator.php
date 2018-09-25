<?php

class HelloFitnessCalculator implements \PW\GA\FitnessCalculatorInterface
{
    protected $target;

    public function __construct($target)
    {
        $this->target = $target;
    }

    public function calculateFitness(array $value)
    {
        $ds   = Datasource::singleton();
        $risk = $ds->getRisk($value);
        return (1 / $risk) * 100;
        /*
    $stringValue = implode('', $value);
    $target      = $this->target;
    similar_text($stringValue, $target, $percent);
    return $percent / 100;
     */

    }
}
