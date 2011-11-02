<?php
/**
 * 
 * Customer entity model
 * @category   Flagbit
 * @package    Flagbit_Securepassword
 * @author 	   Flagbit GmbH & Co. KG <magento@flagbit.de>
 *
 */
class Flagbit_Securepassword_Model_Entity_Customer extends Mage_Customer_Model_Entity_Customer
{
	/**
	 * 
	 * Load the customer data with the help of the securehash
	 * @param Mage_Customer_Model_Customer $customer
	 * @param string $key
	 * @param unknown_type $testOnly
	 */
	public function loadBySecurePasswordKey(Mage_Customer_Model_Customer $customer, $key, $testOnly = false)
    {
        $attribute_id = Mage::getModel('eav/entity_attribute')->loadByCode('customer', 'securepasswordkey')->getId();
        	
    	$select = $this->_getReadAdapter()->select()
            ->from(array('c' => $this->getEntityTable()), array($this->getEntityIdField()))
            ->joinLeft(array('ct' => $this->getEntityTable().'_text'),'c.entity_id = ct.entity_id')
            ->where('ct.attribute_id=?', $attribute_id)
            ->where('ct.value=?',$key);
           
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            if (!$customer->hasData('website_id')) {
                Mage::throwException(Mage::helper('customer')->__('Customer website ID must be specified when using the website scope.'));
            }
            $select->where('website_id=?', (int)$customer->getWebsiteId());
        }

        if ($id = $this->_getReadAdapter()->fetchOne($select, array('customer_securepasswordkey' => $key))) {
            $this->load($customer, $id);
        }
        else {
            $customer->setData(array());
        }
        return $this;
    }
}