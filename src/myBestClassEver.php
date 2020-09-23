<?php

namespace src\Controller;

class myBestClassEver
{
    protected function myStringContainsToto(string $stringToTest)
    {
        $containsToto = false;

        if (strpos($stringToTest, 'toto') == false) {
            $containsToto = true;
        }

        return $containsToto;
    }
}
