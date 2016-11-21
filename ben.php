<?php

#
#Shortcuts & Stuff PHP should have... 

$string = 'zero|one|two|three';
echo ben::explode_get('|',$string,2);

class ben{


    # explode and get array element oneliner
    public static function explode_get($delimiter,$string,$offset)
    {

        $array = explode($delimiter, $string);
        return $array[$offset];
    }
}






