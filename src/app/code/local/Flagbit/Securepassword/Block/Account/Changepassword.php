<?php
/**
 * 
 * Checks the user data
 * @category   Flagbit
 * @package    Flagbit_Securepassword
 * @author 	   Flagbit GmbH & Co. KG <magento@flagbit.de>
 *
 */
class Flagbit_Securepassword_Block_Account_Changepassword extends Mage_Core_Block_Template
{
	/**
	 * 
	 * to write something into the session
	 */
	protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	
}