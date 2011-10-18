<?php
/**
* Zend Framework
*
* @category Flagbit
* @package Flagbit_Filter
* @author David Fuhr <fuhr@flagbit.de>
* @copyright Copyright (c) 2010 Flagbit GmbH & Co. KG (http://www.flagbit.de)
* @version $Id$
*/

/**
* @category Flagbit
* @package Flagbit_Filter
* @author David Fuhr <fuhr@flagbit.de>
* @copyright Copyright (c) 2010 Flagbit GmbH & Co. KG (http://www.flagbit.de)
*/

class Flagbit_Securepassword_Helper_Data extends Mage_Core_Helper_Abstract
{

/**
* Encode the given data very similar to base64
*
* Different from the original base64 algorithm this method only returns
* url safe characters.
*
* @param string $data The data to encode.
* @return string The encoded data, as a string.
*/
    public function encode($data)
    {
        return strtr(base64_encode($data), array(
            '/' => '_',
            '+' => '-',
            '=' => '',
        ));
    }
    
/**
* Decodes an url safe base64 string
*
* The method should also decode regular base64 strings.
*
* @param string $data The encoded data.
* @param bool $strict Returns FALSE if input contains character from outside the base64 alphabet.
* @return Returns the original data or FALSE on failure. The returned data may be binary.
*/
    public function decode($data, $strict = false)
    {
        $data = strtr($data, array(
            '_' => '/',
            '-' => '+',
        ));
        if (strlen($data) % 2 != 0) {
            $data .= '=';
        }
        return base64_decode($data, $strict);
    }
}