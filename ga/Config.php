<?php

class Config
{
    public static function getConfig()
    {
        return [
            'descendent'       => false,
            'useLoack'         => true,
            'noOverAllocation' => false,
            'fullyRandom'      => true,
            'population'       => 1000,
            'iterations'       => 10000,
        ];

    }
}

//read this
//http://mathworld.wolfram.com/ConvexFunction.html
//https://en.wikipedia.org/wiki/Convex_optimization

//stats.csv file format
//population count         bestfitness        averageFitness

//GA resource allocation with modified crossover
//https://link.springer.com/article/10.1023/A:1010949931021
//general resource allocation
//http://www-personal.umich.edu/~shicong/papers/algo-resource-allocation.pdf
//https://arxiv.org/pdf/1204.6170.pdf
//https://www.researchgate.net/publication/283719870_Comparison_of_Search-Based_Software_Engineering_Algorithms_for_Resource_Allocation_Optimization
//https://www.researchgate.net/publication/282649931_Project_resource_allocation_optimization_using_search_based_software_engineering_-_A_framework

//ant colony optimization
//https://ieeexplore.ieee.org/document/4129846/
//http://mat.uab.cat/~alseda/MasterOpt/ACO_Intro.pdf
