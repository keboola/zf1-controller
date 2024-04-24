<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test class for Zend_Controller_Request_Simple.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Request
 */
class Zend_Controller_Request_SimpleTest extends PHPUnit\Framework\TestCase
{
    public function testSimpleRequestIsOfAbstractRequestType()
    {
        $request = new Zend_Controller_Request_Simple();
        $this->assertInstanceOf(Zend_Controller_Request_Abstract::class, $request);
    }

    public function testSimpleReqestRetainsValuesPassedFromConstructor()
    {
        $request = new Zend_Controller_Request_Simple('test1', 'test2', 'test3', array('test4' => 'test5'));
        $this->assertEquals($request->getActionName(), 'test1');
        $this->assertEquals($request->getControllerName(), 'test2');
        $this->assertEquals($request->getModuleName(), 'test3');
        $this->assertEquals($request->getParam('test4'), 'test5');
    }

    /**
     * @group ZF-3472
     */
    public function testSettingParamToNullInSetparamsCorrectlyUnsetsValue()
    {
        $request = new Zend_Controller_Request_Simple;
        $request->setParam('key', 'value');
        $request->setParams(array(
            'key' => null
        ));
        $this->assertNull($request->getParam('key'));
    }
}
