<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework;

/**
 * Class FlagTest
 *
 * @package Magento\Framework
 */
class FlagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Flag
     */
    protected $flag;

    public function setUp()
    {
        $data = ['flag_code' => 'synchronize'];
        $this->createInstance($data);
    }

    protected function createInstance(array $data = [])
    {
        $eventManager = $this->getMock('Magento\Framework\Event\Manager', ['dispatch'], [], '', false, false);
        $context = $this->getMock('Magento\Framework\Model\Context', [], [], '', false, false);
        $context->expects($this->once())
            ->method('getEventDispatcher')
            ->will($this->returnValue($eventManager));
        $registry = $this->getMock('Magento\Framework\Registry', [], [], '', false, false);

        $adapter = $this->getMock('Magento\Framework\DB\Adapter\Adapter', ['beginTransaction'], [], '', false, false);
        $adapter->expects($this->any())
            ->method('beginTransaction')
            ->will($this->returnSelf());
        $appResource = $this->getMock(
            'Magento\Framework\App\Resource',
            [],
            [],
            '',
            false,
            false
        );
        $appResource->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($adapter));

        $resource = $this->getMockBuilder('Magento\Framework\Flag\Resource')
            ->setMethods(['__wakeup', 'load', 'save', 'addCommitCallback', 'commit', 'rollBack'])
            ->setConstructorArgs(['resource' => $appResource])
            ->getMockForAbstractClass();
        $resource->expects($this->any())
            ->method('addCommitCallback')
            ->will($this->returnSelf());

        $resourceCollection = $this->getMock('Magento\Framework\Data\Collection\Db', [], [], '', false, false);

        $this->flag = new \Magento\Framework\Flag(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function tearDown()
    {
        unset($this->flag);
    }

    public function testConstruct()
    {
        $flagCode = 'synchronize';
        $this->createInstance();
        $this->flag->setFlagCode('synchronize');
        $this->assertEquals($flagCode, $this->flag->getFlagCode());
    }

    public function testGetFlagData()
    {
        $result = $this->flag->getFlagData();
        $this->assertNull($result);
        $flagData = serialize('data');
        $this->flag->setData('flag_data', $flagData);
        $result = $this->flag->getFlagData();
        $this->assertEquals(unserialize($flagData), $result);
    }

    public function testSetFlagData()
    {
        $flagData = 'data';
        $this->flag->setFlagData($flagData);
        $result = unserialize($this->flag->getData('flag_data'));
        $this->assertEquals($flagData, $result);
    }

    public function testLoadSelf()
    {
        $this->assertInstanceOf('Magento\Framework\Flag', $this->flag->loadSelf());
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Please define flag code.
     */
    public function testLoadSelfException()
    {
        $this->createInstance();
        $this->flag->loadSelf();
    }

    public function testBeforeSave()
    {
        $this->flag->setData('block', 'blockNmae');
        $result = $this->flag->save();
        $this->assertSame($this->flag, $result);
        $this->assertEquals('synchronize', $this->flag->getFlagCode());
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Please define flag code.
     */
    public function testBeforeSaveException()
    {
        $this->createInstance();
        $this->flag->setData('block', 'blockNmae');
        $this->flag->beforeSave();
    }
}
