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
 * Test class for Zend_Controller_Action_Helper_Url.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_Helper_UrlTest extends PHPUnit\Framework\TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->setRequest(new Zend_Controller_Request_Http());
        $this->helper = new Zend_Controller_Action_Helper_Url();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->helper);
    }

    public function testSimpleWithAllParamsProducesAppropriateUrl()
    {
        $url = $this->helper->simple('baz', 'bar', 'foo', array('bat' => 'foo', 'ho' => 'hum'));
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertStringContainsString('/bat/foo', $url);
        $this->assertStringContainsString('/ho/hum', $url);
    }

    public function testSimpleWithMissingControllerAndModuleProducesAppropriateUrl()
    {
        $request = $this->front->getRequest();
        $request->setModuleName('foo')
                ->setControllerName('bar');
        $url = $this->helper->simple('baz', null, null, array('bat' => 'foo', 'ho' => 'hum'));
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertStringContainsString('/bat/foo', $url);
        $this->assertStringContainsString('/ho/hum', $url);
    }

    public function testSimpleWithDefaultModuleProducesUrlWithoutModuleSegment()
    {
        $url = $this->helper->simple('baz', 'bar', 'default', array('bat' => 'foo', 'ho' => 'hum'));
        $this->assertEquals('/bar/baz', substr($url, 0, 8));
    }

    public function testUrlMethodCreatesUrlBasedOnNamedRouteAndPassedParameters()
    {
        $router = $this->front->getRouter();
        $route  = new Zend_Controller_Router_Route(
            'foo/:action/:page',
            array(
                'module'     => 'default',
                'controller' => 'foobar',
                'action'     => 'bazbat',
                'page'       => 1
            )
        );
        $router->addRoute('foo', $route);
        $url = $this->helper->url(array('action' => 'bar', 'page' => 3), 'foo');
        $this->assertEquals('/foo/bar/3', $url);
    }

    public function testUrlMethodCreatesUrlBasedOnNamedRouteAndDefaultParameters()
    {
        $router = $this->front->getRouter();
        $route  = new Zend_Controller_Router_Route(
            'foo/:action/:page',
            array(
                'module'     => 'default',
                'controller' => 'foobar',
                'action'     => 'bazbat',
                'page'       => 1
            )
        );
        $router->addRoute('foo', $route);
        $url = $this->helper->url(array(), 'foo');
        $this->assertEquals('/foo', $url);
    }

    public function testUrlMethodCreatesUrlBasedOnPassedParametersUsingDefaultRouteWhenNoNamedRoutePassed()
    {
        $this->front->getRouter()->addDefaultRoutes();
        $this->front->addModuleDirectory(dirname(__FILE__) . '/../../_files/modules');
        $url = $this->helper->url(array(
            'module'     => 'foo',
            'controller' => 'bar',
            'action'     => 'baz',
            'bat'        => 'foo',
            'ho'         => 'hum'
        ));
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertStringContainsString('/bat/foo', $url);
        $this->assertStringContainsString('/ho/hum', $url);
    }

    public function testDirectProxiesToSimple()
    {
        $url = $this->helper->direct('baz', 'bar', 'foo', array('bat' => 'foo', 'ho' => 'hum'));
        $this->assertEquals('/foo/bar/baz', substr($url, 0, 12));
        $this->assertStringContainsString('/bat/foo', $url);
        $this->assertStringContainsString('/ho/hum', $url);
    }

    /**
     * @group ZF-2822
     */
    public function testBaseUrlIsAssembledIntoUrl()
    {
        $this->front->setBaseUrl('baseurl');

        $request = $this->front->getRequest();
        $request->setModuleName('module')
                ->setControllerName('controller');

        $url = $this->helper->simple('action', null, null, array('foo' => 'bar'));
        $this->assertEquals('/baseurl/module/controller/action/foo/bar', $url);
    }
}
