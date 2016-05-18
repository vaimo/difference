<?php

require __DIR__ . "/../vendor/autoload.php";

use Undemanding\Difference\ConnectedDifferences;
use Undemanding\Difference\Image;
use Undemanding\Difference\Method\EuclideanDistance;

$image1 = new Image(__DIR__ . "/images/fez-1.png"); // original
$image2 = new Image(__DIR__ . "/images/fez-2.png"); // with text
$image3 = new Image(__DIR__ . "/images/fez-3.png"); // 1% transparent, with text
$image4 = new Image(__DIR__ . "/images/white.png");
$image5 = new Image(__DIR__ . "/images/black.png");

$difference1 = $image1->difference($image2, new EuclideanDistance());
$boundary1 = $difference1->boundary();

print "boundary: (with text)\n";
print "  width → " . $boundary1["width"] . "\n";
print "  height → " . $boundary1["height"] . "\n";
print "  left → " . $boundary1["left"] . "\n";
print "  top → " . $boundary1["top"] . "\n";

print "percentage: " .  $difference1->percentage() . "\n";

$difference2 = $image1->difference($image3, new EuclideanDistance());
$boundary2 = $difference2->withReducedStandardDeviation()->boundary();

print "boundary: (1% transparent, with text)\n";
print "  width → " . $boundary2["width"] . "\n";
print "  height → " . $boundary2["height"] . "\n";
print "  left → " . $boundary2["left"] . "\n";
print "  top → " . $boundary2["top"] . "\n";

print "percentage: " .  $difference2->percentage() . "\n";

$difference3 = $image1->difference($image4, new EuclideanDistance());
$difference4 = $image1->difference($image5, new EuclideanDistance());

print "white difference: " .  $difference3->percentage() . "\n";
print "black difference: " .  $difference4->percentage() . "\n";

// and now, for something completely different...

$image1 = new Image(__DIR__ . "/images/spot-1.png");
$image2 = new Image(__DIR__ . "/images/spot-2.png");

$difference1 = $image1->difference($image2, new EuclideanDistance())->withReducedStandardDeviation();
$connected1 = new ConnectedDifferences($difference1);

$handle = imagecreatefrompng(__DIR__ . "/images/spot-2.png");
$color = imagecolorallocate($handle, 0, 0, 0);

// foreach ($connected1->withJoinedBoundaries()->boundaries() as $boundary) {
foreach ($connected1->boundaries() as $boundary) {
    imagerectangle(
        $handle,
        $boundary["left"],
        $boundary["top"],
        $boundary["right"],
        $boundary["bottom"],
        $color
    );
}

imagepng($handle, __DIR__ . "/images/connected.png");
