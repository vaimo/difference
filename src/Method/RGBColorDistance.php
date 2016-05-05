<?php

namespace Undemanding\Difference\Method;

class RGBColorDistance
{
    /**
     * Calculates the RGB distance between pixels of two states.
     *
     * @param array $firstStatePixel
     * @param array $secondStatePixel
     *
     * @return float
     */
    public function __invoke(array $firstStatePixel, array $secondStatePixel)
    {
        $red = $firstStatePixel["red"] - $secondStatePixel["red"];
        $red *= $red;

        $green = $firstStatePixel["green"] - $secondStatePixel["green"];
        $green *= $green;

        $blue = $firstStatePixel["blue"] - $secondStatePixel["blue"];
        $blue *= $blue;

        $delta = $red + $green + $blue;

        return sqrt($delta);
    }
}
