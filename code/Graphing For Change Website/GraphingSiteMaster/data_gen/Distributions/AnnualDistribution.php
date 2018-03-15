<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 12/03/18
 * Time: 9:46 PM
 */

interface AnnualDistribution
{
    //$x is assumed to be an interger in [1:365]
    //This function returns the value of the distribution for day $x
    public function valueFor($x);
}