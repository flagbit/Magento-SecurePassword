<?php
/**
 * 
 * add the attribute securepasswordkey
 * @category   Flagbit
 * @package    Flagbit_Securepassword
 * @author 	   Flagbit GmbH & Co. KG <magento@flagbit.de>
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'securepasswordkey', array(
    'type'     => 'text',
    'label'    => 'securepasswordkey',
    'visible'  => false,
    'required' => false,
    'input'    => 'text',
));

$installer->endSetup();