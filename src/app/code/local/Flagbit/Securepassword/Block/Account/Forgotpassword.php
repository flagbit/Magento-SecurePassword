<?php
/**
 * 
 * Checks the user data
 * @category   Flagbit
 * @package    Flagbit_Securepassword
 * @author 	   Flagbit GmbH & Co. KG <magento@flagbit.de>
 *
 */
class Flagbit_Securepassword_Block_Account_Forgotpassword extends Mage_Customer_Block_Account_Forgotpassword
{
	/**
	 * 
	 * to write something into the session
	 */
	protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	
	/**
     * 
     * checks if the user came over the 'Forgot Your Password?' link or directly
     */
	public function checkUserData()
	{
		$params = Mage::app()->getRequest()->getParams();
		if(isset($params['secureHash'])){
			$sessionSecurePasswordHash = $params['secureHash'];
			$customer = Mage::getModel('customer/customer')
	                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
	                ->loadBySecurePasswordKey($sessionSecurePasswordHash);
	         $securePasswordHash = $customer->getSecurepasswordkey();
			
			$timestamp = time();
			// time in 15 minutes
			$date = $timestamp + 900;
			$deactive = $this->_getSession()->getSessionDeactivatedAt();
			if ($deactive == '') {
				$this->_getSession()->setSessionDeactivatedAt($date);
			} else {
				//hash prüfen
				$activeHash = $this->_getSession()->getActiveHash();
				if ($activeHash == '') {
					$hash = '';
					if (isset($params['secureHash'])) {
						
						$hash = $params['secureHash'];
					}	
					$activeHash = $hash;
					$this->_getSession()->setActiveHash($activeHash);
				}				
				if ($securePasswordHash != $activeHash) {
					$this->_getSession()->setSessionDeactivatedAt($date);
				}
			}
			
			$sessionSecurePasswordHash = $params['secureHash'];

			$now = Mage::getModel('core/date')->timestamp(time());
			$hashExpiration = unserialize(Mage::helper('securepassword/data')->decode(
			$sessionSecurePasswordHash)
			);
			
			$email = $customer->getEmail();
			Zend_Debug::dump($hashExpiration['expire']-$now);
            if($email != '' && $sessionSecurePasswordHash == $securePasswordHash && (($hashExpiration['expire']-$now)>0)) {
            	
                //@todo remove german comments!
            	// 1 -> Daten passen
            	return true;
            }
            else {
            	// return error message
            	$this->_getSession()->addError($this->__('Email or security hash does not match or security hash has been expired.'));
            	return;
            	//return $this->__('Email or security hash does not match.');
            	
            }
		}
	}
}