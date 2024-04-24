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
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_HelperBrokerTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var Zend_Controller_Front
     */
    protected $front;

    public function setUp(): void
    {
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->setParam('noViewRenderer', true)
                    ->setParam('noErrorHandler', true)
                    ->throwExceptions(true);
        Zend_Controller_Action_HelperBroker::resetHelpers();

        //$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        //$viewRenderer->setActionController();
    }

    public function testLoadingAndReturningHelper()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-get-redirector/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', $response->getBody());
    }

    public function testLoadingAndReturningHelperStatically()
    {
        $helper = new Zend_Controller_Action_HelperBroker_TestHelper();
        Zend_Controller_Action_HelperBroker::addHelper($helper);
        $received = Zend_Controller_Action_HelperBroker::getExistingHelper('testHelper');
        $this->assertSame($received, $helper);
    }

    public function testGetExistingHelperThrowsExceptionWithUnregisteredHelper()
    {
        $this->expectException(Zend_Controller_Action_Exception::class);
        $this->expectExceptionMessage('Action helper "TestHelper" has not been registered with the helper broker');
        $received = Zend_Controller_Action_HelperBroker::getExistingHelper('testHelper');
    }

    public function testLoadingHelperOnlyInitializesOnce()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http();
        $request->setModuleName('default')
                ->setControllerName('zend_controller_action_helper-broker')
                ->setActionName('index');
        $response = new Zend_Controller_Response_Cli();
        $this->front->setResponse($response);

        $helper = new Zend_Controller_Action_HelperBroker_TestHelper();
        Zend_Controller_Action_HelperBroker::addHelper($helper);

        $controller = new Zend_Controller_Action_HelperBrokerController($request, $response, array());
        $controller->test();
        $received = $controller->getHelper('testHelper');
        $this->assertSame($helper, $received);
        $this->assertEquals(1, $helper->count);
    }

    public function testLoadingAndCheckingHelpersStatically()
    {
        $helper = new Zend_Controller_Action_Helper_Redirector();
        Zend_Controller_Action_HelperBroker::addHelper($helper);

        $this->assertTrue(Zend_Controller_Action_HelperBroker::hasHelper('redirector'));
    }

    public function testLoadingAndRemovingHelpersStatically()
    {
        $helper = new Zend_Controller_Action_Helper_Redirector();
        Zend_Controller_Action_HelperBroker::addHelper($helper);

        $this->assertTrue(Zend_Controller_Action_HelperBroker::hasHelper('redirector'));
        Zend_Controller_Action_HelperBroker::removeHelper('redirector');
        $this->assertFalse(Zend_Controller_Action_HelperBroker::hasHelper('redirector'));
    }
    public function testReturningHelper()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-get-redirector/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', $response->getBody());
    }

    public function testReturningHelperViaMagicGet()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-helper-via-magic-get/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', $response->getBody());
    }

    public function testReturningHelperViaMagicCall()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-helper-via-magic-call/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());

        $this->front->returnResponse(true);

        require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files/Helpers/TestHelper.php';
        Zend_Controller_Action_HelperBroker::addHelper(new MyApp_TestHelper());

        $response = $this->front->dispatch($request);
        $this->assertEquals('running direct call', $response->getBody());
    }

    public function testNonExistentHelper()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-bad-helper/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());

        $this->front->returnResponse(true);
        $response = $this->front->dispatch($request);
        $this->assertStringContainsString('not found', $response->getBody());
    }

    public function testCustomHelperRegistered()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-custom-helper/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());

        $this->front->returnResponse(true);

        require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files/Helpers/TestHelper.php';
        Zend_Controller_Action_HelperBroker::addHelper(new MyApp_TestHelper());

        $response = $this->front->dispatch($request);
        $this->assertEquals('MyApp_TestHelper', $response->getBody());
    }

    public function testCustomHelperFromPath()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-custom-helper/');
        $this->front->setResponse(new Zend_Controller_Response_Cli());

        $this->front->returnResponse(true);

        Zend_Controller_Action_HelperBroker::addPath(
            dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Helpers',
            'MyApp'
        );

        $response = $this->front->dispatch($request);
        $this->assertEquals('MyApp_TestHelper', $response->getBody());
    }

    public function testGetExistingHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(new Zend_Controller_Action_Helper_Redirector());
        // already included in setup, techinically we shouldnt be able to do this, but until 2.0 - its allowed
        Zend_Controller_Action_HelperBroker::addHelper(new Zend_Controller_Action_Helper_ViewRenderer()); // @todo in future this should throw an exception

        $helpers = Zend_Controller_Action_HelperBroker::getExistingHelpers();
        $this->assertIsArray($helpers);
        $this->assertCount(2, $helpers);
        $this->assertContains('ViewRenderer', array_keys($helpers));
        $this->assertContains('Redirector', array_keys($helpers));
    }

    public function testGetHelperStatically()
    {
        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $this->assertInstanceOf(Zend_Controller_Action_Helper_ViewRenderer::class, $helper);

        $helpers = Zend_Controller_Action_HelperBroker::getExistingHelpers();
        $this->assertIsArray($helpers);
        $this->assertCount(1, $helpers);
    }

    public function testHelperPullsResponseFromRegisteredActionController()
    {
        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

        $aRequest = new Zend_Controller_Request_Http();
        $aRequest->setModuleName('default')
                 ->setControllerName('zend_controller_action_helper-broker')
                 ->setActionName('index');
        $aResponse  = new Zend_Controller_Response_Cli();
        $controller = new Zend_Controller_Action_HelperBrokerController($aRequest, $aResponse, array());

        $fRequest = new Zend_Controller_Request_Http();
        $fRequest->setModuleName('foo')
                 ->setControllerName('foo-bar')
                 ->setActionName('baz');
        $fResponse = new Zend_Controller_Response_Cli();
        $this->front->setRequest($fRequest)
                    ->setResponse($fResponse);

        $helper->setActionController($controller);

        $hRequest = $helper->getRequest();
        $this->assertSame($hRequest, $aRequest);
        $this->assertNotSame($hRequest, $fRequest);
        $hResponse = $helper->getResponse();
        $this->assertSame($hResponse, $aResponse);
        $this->assertNotSame($hResponse, $fResponse);
    }

    public function testHelperPullsResponseFromFrontControllerWithNoRegisteredActionController()
    {
        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $this->assertNull($helper->getActionController());

        $aRequest = new Zend_Controller_Request_Http();
        $aRequest->setModuleName('default')
                 ->setControllerName('zend_controller_action_helper-broker')
                 ->setActionName('index');
        $aResponse = new Zend_Controller_Response_Cli();

        $fRequest = new Zend_Controller_Request_Http();
        $fRequest->setModuleName('foo')
                 ->setControllerName('foo-bar')
                 ->setActionName('baz');
        $fResponse = new Zend_Controller_Response_Cli();
        $this->front->setRequest($fRequest)
                    ->setResponse($fResponse);

        $hRequest = $helper->getRequest();
        $this->assertNotSame($hRequest, $aRequest);
        $this->assertSame($hRequest, $fRequest);
        $hResponse = $helper->getResponse();
        $this->assertNotSame($hResponse, $aResponse);
        $this->assertSame($hResponse, $fResponse);
    }

    public function testHelperPathStackIsLifo()
    {
        Zend_Controller_Action_HelperBroker::addPath(
            dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Helpers',
            'MyApp'
        );

        $urlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('url');
        $this->assertInstanceOf(MyApp_Url::class, $urlHelper);
    }

    /**
     * @group ZF-4704
     */
    public function testPluginLoaderShouldHaveDefaultPrefixPath()
    {
        $loader = Zend_Controller_Action_HelperBroker::getPluginLoader();
        $paths  = $loader->getPaths('Zend_Controller_Action_Helper');
        $this->assertNotEmpty($paths);
    }

    public function testCanLoadNamespacedHelper()
    {
        $this->front->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files')
            ->setResponse(new Zend_Controller_Response_Cli())
            ->returnResponse(true);

        $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files/Helpers';
        Zend_Controller_Action_HelperBroker::addPath($path, 'MyApp\Controller\Action\Helper\\');

        $request  = new Zend_Controller_Request_Http('http://framework.zend.com/helper-broker/test-can-load-namespaced-helper/');
        $response = $this->front->dispatch($request);
        $this->assertEquals('MyApp\Controller\Action\Helper\NamespacedHelper', $response->getBody());
    }

    /**
     * @group ZF-4704
     */
    public function testBrokerShouldAcceptCustomPluginLoaderInstance()
    {
        $loader = Zend_Controller_Action_HelperBroker::getPluginLoader();
        $custom = new Zend_Loader_PluginLoader();
        Zend_Controller_Action_HelperBroker::setPluginLoader($custom);
        $test = Zend_Controller_Action_HelperBroker::getPluginLoader();
        $this->assertNotSame($loader, $test);
        $this->assertSame($custom, $test);

        // Reset back to normal
        Zend_Controller_Action_HelperBroker::setPluginLoader($loader);
    }
}

class Zend_Controller_Action_HelperBroker_TestHelper extends Zend_Controller_Action_Helper_Abstract
{
    public $count = 0;

    public function init()
    {
        ++$this->count;
    }
}

class Zend_Controller_Action_HelperBrokerController extends Zend_Controller_Action
{
    public $helper;

    public function init()
    {
        $this->helper = $this->_helper->getHelper('testHelper');
    }

    public function test()
    {
        $this->_helper->getHelper('testHelper');
    }
}
