<?php

require "../vendor/autoload.php";

use Undemanding\Difference\Method\RGBColorDistance;
use Undemanding\Difference\State;

$state1 = State::fromImage(__DIR__ . "/fez-1.png");
$state2 = State::fromImage(__DIR__ . "/fez-2.png");

$state3 = $state1->withDifference($state2, new RGBColorDistance());

print "\n" . $state3->average();
print "\n" . $state3->maximum();
print "\n" . $state3->standardDeviation();
print "\n" . print_r($state3->boundary(), true);
