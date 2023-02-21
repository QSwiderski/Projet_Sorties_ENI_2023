<?php

namespace App;

class ToolKitBQP
{
    /*
     *transform les entités (objet ou string) au format UTF 8
     */
    function turnToUTF8($d) {
        if (is_array($d))
            foreach ($d as $k => $v)
                $d[$k] = $this->turnToUTF8($v);

        else if(is_object($d))
            foreach ($d as $k => $v)
                $d->$k = $this->turnToUTF8($v);

        else
            return utf8_encode($d);

        return $d;
    }
}