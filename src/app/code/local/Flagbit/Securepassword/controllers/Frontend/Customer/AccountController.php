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
            
            $customer = Mage::getModel('securepassword/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $hash = $customer->getLostPasswordHash();
                    $customer->setMailHash($hash);
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
    
    public function changepasswordPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/changepassword');
        }

        if ($this->getRequest()->isPost()) {
            /* @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::registry('customer');

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            $errors = array();
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $errors = array();

                $newPass    = $this->getRequest()->getPost('password');
                $confPass   = $this->getRequest()->getPost('confirmation');

                $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                if (Mage::helper('core/string')->strpos($oldPass, ':')) {
                    list($_salt, $salt) = explode(':', $oldPass);
                } else {
                    $salt = false;
                }

                if (strlen($newPass)) {
                    // Set entered password and its confirmation - they will be validated later to match each other and be of right length
                    $customer->setPassword($newPass);
                    $customer->setConfirmation($confPass);
                    $customer->setSecurepasswordkey('');
                } else {
                    $errors[] = $this->__('New password field cannot be empty.');
                }

                // Validate account and compose list of errors if any
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/changepassword');
                return $this;
            }

            try {
                $customer->setConfirmation(null);
                $customer->save();
                $this->_getSession()->addSuccess($this->__('The account information has been saved.'));
                $this->_getSession()->unsLostPasswordHash();
                $this->getResponse()->setRedirect(Mage::getUrl('*/*'));

                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the password.'));
            }
        }

        $this->_redirect('*/*/changepassword');        
    }
    
    
    
	/**
	 * 
	 * the template which should be loaded
	 */
    public function changepasswordAction ()
    {
        $customer = Mage::registry('customer');
    	if ($customer instanceof Mage_Customer_Model_Customer
    	    && $customer->getId()) {

            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');
            $this->renderLayout();
    	}
	}
	
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        
        $action = $this->getRequest()->getActionName();
        if (!preg_match('/^(changepassword|changepasswordPost)/i', $action)) {
           parent::preDispatch();
        }else{
            if(Mage::app()->getRequest()->getParam('hash')){
                $this->_getSession()->setLostPasswordHash(Mage::app()->getRequest()->getParam('hash'));
            }
            
            $hash = $this->_getSession()->getLostPasswordHash();
        	if ($hash) {
                $customer = Mage::getModel('securepassword/customer')
                                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())                
                                ->loadByLostPasswordHash($hash);
                                
                if ($customer->getId()) {
                    Mage::register('customer', $customer);
    			} else {		
    			    $this->setFlag('', 'no-dispatch', true);
    				$this->_getSession()->addError($this->__('The session timed out.'));
    				$this->getResponse()->setRedirect(Mage::getUrl('*/*'));
    				return;
    			}
        	}
        	else {
        	    $this->setFlag('', 'no-dispatch', true);
        		$this->_getSession()->addError($this->__('You have called the page directly. This is not allowed.'));
        		$this->getResponse()->setRedirect(Mage::getUrl('*/*'));
        		return;
        	}            
                        
            Mage_Core_Controller_Front_Action::preDispatch();
        }
    }	
	

    
}