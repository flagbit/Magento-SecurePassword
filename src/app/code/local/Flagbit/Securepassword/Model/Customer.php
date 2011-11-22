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
    
    function _construct()
    {
        $this->_init('securepassword/customer');
    }    
    
	/**
	 * 
	 * Prepare the fata to load
	 * @param string $hash
	 */
	public function loadByLostPasswordHash($hash) 
	{
	    $this->_getResource()->loadBySecurePasswordKey($this, $hash);

	    if(!$this->getSecurepasswordkey() 
	        || !Mage::helper('securepassword/data')->validateSerializedHashWithTimeout($this->getSecurepasswordkey() , $hash)){
	        $this->unsetData();
	    }
	    
        return $this;
    }
    
    public function getLostPasswordHash($timeout = null)
    {
        $hash = Mage::helper('securepassword/data')->getSerializedHashWithTimeout($timeout);
        $this->setSecurepasswordkey($hash);
        $this->getResource()->saveAttribute($this, 'securepasswordkey');
        return md5($hash);
    }
}