<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\Config;

/**
 * Magento configuration DOM factory
 */
class DomFactory
{
    const CLASS_NAME = 'Magento\Framework\Config\Dom';

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManger
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManger)
    {
        $this->_objectManager = $objectManger;
    }

    /**
     * Create DOM object
     *
     * @param array $arguments
     * @return \Magento\Framework\Config\Dom
     */
    public function createDom(array $arguments = [])
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments);
    }
}
