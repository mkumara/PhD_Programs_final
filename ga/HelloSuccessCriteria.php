<?php
class HelloSuccessCriteria implements \PW\GA\SuccessCriteriaInterface
{
    protected $target;

    public function __construct($target)
    {
        $this->target = $target;
    }

    public function validateSuccess(\PW\GA\Chromosome $fittest)
    {
        return implode('', $fittest->getValue()) === $this->target;
    }
}
