<?php
use PW\GA\Chromosome;
use \PW\GA\ChromosomeGenerator\Word;

require_once "Config.php";

class CustomWord extends Word
{
    public function __construct($alphabet, $wordLength)
    {
        parent::__construct($alphabet, $wordLength);
    }

    /**
     * @param int $numberOfChromosomes
     * @return Chromosome[]
     */
    public function generateChromosomes_old($numberOfChromosomes)
    {
        echo "Generator started..." . PHP_EOL;
        $ds = Datasource::singleton();

        $alphabet    = $this->alphabet;
        $chromosomes = [];
        $count       = 0;
        while ($count < $numberOfChromosomes) {
            shuffle($alphabet);
            $value = array_slice($alphabet, 0, $this->wordLength);

            //check for constraints
            if ($this->checkConstraints($value, $ds)) {
                $chromosomes[] = new Chromosome($value);
                $count++;
            }

        }
        echo "Generator done..." . PHP_EOL;
        return $chromosomes;
    }

    public function generateChromosomes($numberOfChromosomes)
    {

        echo "Generator started..." . PHP_EOL;
        $ds            = Datasource::singleton();
        $availalbe     = $ds->getdataAvailable();
        $required      = $ds->getdataRequired();
        $locakResource = $ds->getLockResources();

        $numResources  = count($availalbe);
        $numFacilities = count($required);
        $allocated     = array_fill(0, $numResources, 0);

        $config            = Config::getConfig();
        $useLockChromosome = $config['useLoack'];

        $chromosomes = [];
        $count       = 0;
        $amount      = 0;
        $shuffled    = range(0, $numResources - 1);

        /***************************************************************************************/
        /***************************************************************************************/
        if ($config['fullyRandom']) {

            for ($i = 0; $i < $numberOfChromosomes; $i++) {
                $value = array_fill(0, $this->wordLength, 0);

                for ($j = 0; $j < $this->wordLength; $j = $j + $numResources) {
                    shuffle($shuffled); //fair allocation of resources
                    for ($k = 0; $k < $numResources; $k++) {
                        $l = $shuffled[$k];
                        if ($locakResource[$l] == 1 && $useLockChromosome) // do not change as we have abundant amount of resources
                        {
                            $amount = $required[$j / $numResources][$l];
                        } else {
                            $amount = rand(0, $required[$j / $numResources][$l]);

                        }

                        $value[$j + $l] = $amount;
                    }
                }
                $allocated     = array_fill(0, $numResources, 0);
                $chromosomes[] = new Chromosome($value);
            }
            echo $ds->getRisk($chromosomes[0]->getValue()) . PHP_EOL;
            echo $ds->getRisk($chromosomes[50]->getValue()) . PHP_EOL;
            echo $ds->getRisk($chromosomes[99]->getValue());
            return $chromosomes;
        }

        /***************************************************************************************/
        /***************************************************************************************/
        /***************************************************************************************/
        /***************************************************************************************/

        for ($i = 0; $i < $numberOfChromosomes; $i++) {
            $value = array_fill(0, $this->wordLength, 0);

            for ($j = 0; $j < $this->wordLength; $j = $j + $numResources) {
                shuffle($shuffled); //fair allocation of resources
                for ($k = 0; $k < $numResources; $k++) {
                    $l = $shuffled[$k];
                    if ($locakResource[$l] == 1 && $useLockChromosome) // do not change as we have abundant amount of resources
                    {
                        $amount = $required[$j / $numResources][$l];
                    } else {
                        // echo "Diff = ".($this->wordLength - $numResources). " And J =".$j.PHP_EOL;
                        if ($j == ($this->wordLength - $numResources)) {
                            if (($availalbe[$l] - $allocated[$l]) > $required[$j / $numResources][$l]) {
                                $amount = $required[$j / $numResources][$l];
                            } else {
                                $amount = ($availalbe[$l] - $allocated[$l]);
                            }

                            // echo "Last Element".PHP_EOL;
                        } else {

                            $amount = rand($required[$j / $numResources][$l] / 2, $required[$j / $numResources][$l]);
                            //echo "More to go".PHP_EOL;
                            if (($availalbe[$l] - $allocated[$l]) < $amount) {
                                $amount = $availalbe[$l] - $allocated[$l];

                            }

                        }

                        $allocated[$l] = $allocated[$l] + $amount;
                    }

                    $value[$j + $l] = $amount;
                }

            }
            $allocated     = array_fill(0, $numResources, 0);
            $chromosomes[] = new Chromosome($value);

        }
        //var_export($chromosomes);
        echo "Generator done..." . PHP_EOL;
        if ($this->checkAllocation($value, $ds)) {
            echo "Allocation done";
            $ds->printAllocationResult($value);
            exit();
        }

        return $chromosomes;
    }

    public function checkAllocation($chromosome, $ds)
    {
        $risk = $ds->getRisk($chromosome);
        if ($risk == 0) {
            return true;
        } else {
            return false;
        }

    }

}
