<?php
namespace SoampliApps\Authentication\Providers;

class APIProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $_userFactory;
    protected $_userGateway;
    protected $_provider;
    
    public function setUp()
    {
        $this->_userFactory = $this->getMock('\SoampliApps\Authentication\APIUserFactoryInterface');
        $this->_userGateway = $this->getMock('\SoampliApps\Authentication\UserGateway');
        
        $request = array();
        $server = array( 'PHP_AUTH_USER' => 53, 'PHP_AUTH_PW' => 'some-api-key');
        $request['server'] = $server;
        $this->request = $request;
        
        $this->_provider = new APIProvider($request, $this->_userFactory, $this->_userGateway);
    }
    
    public function testHasTriedToLoginWithAPICredentials()
    {
        $this->assertTrue($this->_provider->hasAttemptedToLoginWithProvider());
        $this->_provider = new APIProvider(array(), $this->_userFactory, $this->_userGateway);
        $this->assertFalse($this->_provider->hasAttemptedToLoginWithProvider());
        $user_factory = $this->getMock('\SoampliApps\Authentication\UserFactoryInterface');
        $this->_provider = new APIProvider($this->request, $user_factory, $this->_userGateway);
        $this->assertFalse($this->_provider->hasAttemptedToLoginWithProvider());
        
    }
    
    public function testLoggingInWithAPICredentials()
    {
        $user = new \stdClass();
        $user->userId = $this->request['server']['PHP_AUTH_USER'];
        $user->username = 'test';
        $user_factory = $this->getMock('\SoampliApps\Authentication\APIUserFactoryInterface');
        $user_factory->expects($this->once())
                     ->method('getFromUserIdAndAPIKey')
                     ->with($this->equalTo($this->request['server']['PHP_AUTH_USER']), $this->request['server']['PHP_AUTH_PW'])
                     ->will($this->returnValue($user));
        $this->_provider = new APIProvider($this->request, $user_factory, $this->_userGateway);
        $this->assertEquals($user, $this->_provider->processLoginAttempt());
        
        $user_factory = $this->getMock('\SoampliApps\Authentication\APIUserFactoryInterface');
        $user_factory->expects($this->once())
                     ->method('getFromUserIdAndAPIKey')
                     ->with($this->equalTo($this->request['server']['PHP_AUTH_USER']), $this->request['server']['PHP_AUTH_PW'])
                     ->will($this->throwException(new \Exception()));
        $this->_provider = new APIProvider($this->request, $user_factory, $this->_userGateway);
        $this->assertNull($this->_provider->processLoginAttempt());
    }

    public function testLogout()
    {
        $this->assertTrue($this->_provider->logout());
    }
    
    public function testUserWantsToBeRemembered()
    {
        $this->assertFalse($this->_provider->userWantsToBeRemembered());
    }
    
    /**
     * @covers SoampliApps\Authentication\Providers\APIProvider::shouldPersist
     */
    public function testShouldPersist()
    {
        $this->assertTrue($this->_provider->shouldPersist());
        $user = new \stdClass();
        $user->userId = $this->request['server']['PHP_AUTH_USER'];
        $user->username = 'test';
        $user_factory = $this->getMock('\SoampliApps\Authentication\APIUserFactoryInterface');
        $user_factory->expects($this->once())
                     ->method('getFromUserIdAndAPIKey')
                     ->with($this->equalTo($this->request['server']['PHP_AUTH_USER']), $this->request['server']['PHP_AUTH_PW'])
                     ->will($this->returnValue($user));
        $this->_provider = new APIProvider($this->request, $user_factory, $this->_userGateway);
        $this->assertEquals($user, $this->_provider->processLoginAttempt());
        $this->assertFalse($this->_provider->shouldPersist());
    }
}
