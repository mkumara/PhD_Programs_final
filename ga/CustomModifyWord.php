<?php

use PW\GA\MutateMethod\ModifyWord;

require_once "Config.php";

class CustomModifyWord extends ModifyWord
{
    protected $ds;

    public function __construct(array $alphabet)
    {
        parent::__construct($alphabet);
        $this->ds = DataSource::singleton();
    }
    /**
     * @param mixed[] $value
     * @param float $entropy
     * @return mixed[]
     */
    public function mutate(array $value, $entropy)
    {
        $available     = $this->ds->getdataAvailable();
        $resourceCount = count($available);
        $config        = Config::getConfig();
        $descendent    = $config['descendent'];

        $target   = $this->ds->getChromosome($this->ds->getDataRequired());
        $distance = $this->ds->getCloseness($target, $value);
        //  echo $distance . PHP_EOL;

        $loackChromosome = $this->ds->getLockChromosome();

        $valueLength = count($value);
        $charChanges = ceil($entropy * ($valueLength / 2));

        $alphabet = $this->alphabet;
        for ($i = 0; $i < $charChanges; $i++) {

            // change character
            //get unlocked positions
            $unlockedResources = array_keys($loackChromosome, 0);

            //$charIndex = mt_rand(0, $valueLength - 1);
            $charIndexKey = array_rand($unlockedResources, 1);
            $charIndex    = $unlockedResources[$charIndexKey];

            if ($descendent) {
                $randomMutation = rand(0, ($value[$charIndex]) * $distance);
            } else {
                $randomMutation = rand(0, $value[$charIndex]);
            }

            if ($value[$charIndex] - $randomMutation > 0) {
                $value[$charIndex] = $value[$charIndex] - $randomMutation;
            } else {
                $value[$charIndex] = 0;
            }

            if (isset($value[$charIndex - $resourceCount])) {
                if ($config['noOverAllocation']) {
                    if (($value[$charIndex - $resourceCount] + $randomMutation) < $target[$charIndex - $resourceCount]) {
                        $value[$charIndex - $resourceCount] = $value[$charIndex - $resourceCount] + $randomMutation;
                    } else {
                        $value[$charIndex - $resourceCount] = $target[$charIndex - $resourceCount];
                    }

                } else {
                    $value[$charIndex - $resourceCount] = $value[$charIndex - $resourceCount] + $randomMutation;
                }

            } else {

                if ($config['noOverAllocation']) {
                    if (($value[$charIndex + $resourceCount] + $randomMutation) < $target[$charIndex + $resourceCount]) {
                        $value[$charIndex + $resourceCount] = $value[$charIndex + $resourceCount] + $randomMutation;
                    } else {
                        $value[$charIndex + $resourceCount] = $target[$charIndex + $resourceCount];

                    }
                } else {
                    $value[$charIndex + $resourceCount] = $value[$charIndex + $resourceCount] + $randomMutation;
                }

            }
            return $value;
        }

    }
}
