<?php
/**
 * 
 * Customer model
 * @category   Flagbit
 * @package    Flagbit_Securepassword
 * @author 	   Flagbit GmbH & Co. KG <magento@flagbit.de>
 *
 */
class Flagbit_Securepassword_Model_Customer extends Mage_Customer_Model_Customer
{
	/**
	 * 
	 * Prepare the fata to load
	 * @param string $customerSecurepasswordkey
	 */
	public function loadBySecurePasswordKey($customerSecurePasswordKey) 
	{
	    $this->_getResource()->loadBySecurePasswordKey($this, $customerSecurePasswordKey);
        return $this;
    }
}