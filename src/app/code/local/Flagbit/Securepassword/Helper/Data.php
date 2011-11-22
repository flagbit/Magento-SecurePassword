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
    const XML_CONFIG_TIMEOUT= 'securepassword/general/timeout';

    /**
     * get serialized Password Hash Array with Timeout
     * 
     * @param int $timeout
     * @param int $length
     * @return string $secureHash
     */
    public function getSerializedHashWithTimeout($timeout = null, $length = 15) 
    {
    	if($timeout === null) {
    		$timeout = Mage::getStoreConfig(self::XML_CONFIG_TIMEOUT);
    	}
    	$secureHash = array(
    						'hash'		=> Mage::helper('core')->getRandomString($length),
    						'expire'	=> empty($timeout) ? 0 : time()+$timeout,
    	 					);
    	
    	return serialize($secureHash);    	
    }

    public function validateSerializedHashWithTimeout($serializedHash, $hash)
    {
        $valid = false;
        $hashdata = unserialize($serializedHash);
        if(!empty($hashdata['expire'])){
            $valid = $hashdata['expire'] > time();
        }else{
            $valid = true;
        }
        return $valid;
    }
    
}