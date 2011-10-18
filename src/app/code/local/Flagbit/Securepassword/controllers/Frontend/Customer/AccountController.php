<?php
/**
 * 
 * Extends the Mage_Customer_AccountController and overwrite functions
 * to change the user password without sending an email
 * 
 * @category   Flagbit
 * @package    Flagbit_Securepassword
 * @author	   Flagbit GmbH & Co. KG <magento@flagbit.de>
 * 
 */
include_once("Mage/Customer/controllers/AccountController.php");

class Flagbit_Securepassword_Frontend_Customer_AccountController extends Mage_Customer_AccountController
{
	/**
	 * 
	 * prepare all information for changing the password
	 * 
	 */
	public function forgotPasswordPostAction()
    {
        $email = $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $this->_getSession()->addError($this->__('Invalid email address.'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
                return;
            }
            
            
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                	
                    $newSecureHash = urlencode(
                    							base64_encode(
                    									$this->generateSecurityPasswordHash(20)
                    							));
                    
                    $customer->setSecurepasswordkey($newSecureHash);
                    $customer->save();

                    $customer->sendPasswordReminderEmail();

                    $this->_getSession()->addSuccess($this->__('A new email for changing password has been sent.'));
                    $this->getResponse()->setRedirect(Mage::getUrl('*/*'));
                   
                    return;
                }
                catch (Exception $e){
                    $this->_getSession()->addError($e->getMessage());
                }
            } else {
                $this->_getSession()->addError($this->__('This email address was not found in our records.'));
                $this->_getSession()->setForgottenEmail($email);
            }
        } else {
            $this->_getSession()->addError($this->__('Please enter your email.'));
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
            return;
        }

        $this->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
    }
    
    /**
     * 
     * Set a random security hash
     * @param integer $length
     * @return array $secureHash
     */
    public function generateSecurityPasswordHash($length = 15, $loopCount = 5) {
        
    	//@todo add expiration 
    	$tempHash = '';
    	  	
    	for ($i=0; $i<=$loopCount; $i++) {
    		$tempHash .= Mage::helper('core')->getRandomString($length);
    	}
    	
    	$secureHash = array(
    							'hash'	=> sha1($tempHash),
    							'expire' 		=> getTimestamp()+900,
    	 					);
    	
    	
    	return $secureHash;
    }
}