<?php
/**
 * 
 * saves the new password or redirect to the correct view
 * @category   Flagbit
 * @package    Flagbit_Securepassword
 * @author	   Flagbit GmbH & Co. KG <magento@flagbit.de>
 *
 */
class Flagbit_Securepassword_RequestController extends Mage_Core_Controller_Front_Action
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
	 * the template which should be loaded
	 */
    public function changepasswordAction ()
    {
    	$params = Mage::app()->getRequest()->getParams();
    	if (isset($params['secureHash'])) {
    		$deactive = $this->_getSession()->getSessionDeactivatedAt();
    		$timestamp = time();
    		if (strlen($deactive == 0)) {
				$date = $timestamp + 900;
    			$this->_getSession()->setSessionDeactivatedAt($date);
    			$deactive = $this->_getSession()->getSessionDeactivatedAt();
			}
			if ($timestamp <= $deactive) {
				$this->loadLayout();
				$this->renderLayout();	
			}
			else {
				$this->_getSession()->addError($this->__('The session timed out.'));
				$this->_forward('login','account','customer');
			}
    			
    	}
    	else {
    		$this->_getSession()->addError($this->__('You have called the page directly. This is not allowed.'));
    		$this->_forward('login','account','customer');	
    	}
	}
    
    /**
     * 
     * saves the new password and deletes session parts if all data a correct
     */
	public function editpostAction()
	{
		$deactive = $this->_getSession()->getSessionDeactivatedAt();
		$timestamp = time();
		
		$params = Mage::app()->getRequest()->getParams();
		if (isset($params['secureHash'])) {
			$paramsecurehash = $params['secureHash'];	
		}
		else {
			$paramsecurehash = '';
		}
		
		//@todo Wrong Type of URL generation use Mage::getUrl($path, $arguments) insteed
		$newUrl = Mage::getBaseUrl();
        $newUrl .= 'securepassword/request/changepassword/secureHash/';
        $newUrl .= $paramsecurehash;
		
		if ($timestamp <= $deactive) {
			$length = 6;
			$customer = Mage::getModel('customer/customer')
	                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
	                ->loadBySecurepasswordkey($paramsecurehash);
	            $parammail = $customer->email;
	
	       if ($paramsecurehash != '') {
	        	// wird password and confirmation übertragen
				if (isset($params["password"]) && isset($params["confirmation"])) {
					$newPassword = trim($params["password"]," ");
					$newPasswordConfirmation = trim ($params["confirmation"]," ");
					
					// Both have more than x letters
					if (strlen($newPassword) >= $length || strlen($newPasswordConfirmation) >= $length) {
						// Are $newPassword and $newPasswordConfirmation equal?
						if ($newPassword == $newPasswordConfirmation) {
							// get userdata with the help of the Email
							            
						    $email = $customer->email;
	            			$securepasswordhash = $customer->securepasswordkey;
	            			
	            			if($paramsecurehash == $securepasswordhash) {
	            				// set new pasysword and passwordHash
	            				$customer->setPassword($newPassword);
	        					$customer->setPasswordHash($customer->hashPassword($customer->$newPassword));
	            				
	        					// set securepasswordkey empty in the database
	            				$customer->setSecurepasswordkey('');
	            				$customer->save();
	            				$this->_getSession()->setSessionDeactivatedAt('');
	            				
	            				$this->_getSession()->addSuccess($this->__('The password has been changed successful.'));
	            				
	            				$newUrl = Mage::getBaseUrl();
			                    $newUrl .= 'customer/account/login/';
			                }
	            			else {
	            				// return error message
	            				$this->_getSession()->addError($this->__('Email or security hash does not match.'));
	            				
	            			}
						}
						else {
							// return error message
							$this->_getSession()->addError($this->__('Please make sure your passwords match.'));
						}
					}
				}
				else {
					//Fehlerbehandlung da direkter Aufruf
					$this->_getSession()->addError($this->__('There was no password transmitted.'));
				}
			}
			else {
				//Fehlerbehandlung da direkter Aufruf
				$this->_getSession()->addError($this->__('You have called the page directly. This is not allowed.'));
			}
		}
		
		
		$this->getResponse()->setRedirect($newUrl);
	}
}