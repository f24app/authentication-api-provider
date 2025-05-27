<?php
namespace SoampliApps\Authentication\Providers;

class APIProvider implements ProviderInterface
{
    protected $request;
    protected $userFactory;
    protected $userGateway;
    protected $server;
    protected $persist = true;

    public function __construct(array $request, \SoampliApps\Authentication\UserFactoryInterface $user_factory, \SoampliApps\Authentication\UserGateway $user_gateway)
    {
        $this->request = $request;
        $this->userFactory = $user_factory;
        $this->userGateway = $user_gateway;
        if(isset($this->request['server'])) {
            $this->server = $this->request['server'];
        }
    }

    public function hasAttemptedToLoginWithProvider()
    {
        if(! $this->userFactory instanceof \SoampliApps\Authentication\APIUserFactoryInterface ) {
            return false; // or should we throw an exception
        }
        if( isset($this->server['PHP_AUTH_USER']) && ! is_null($this->server['PHP_AUTH_USER']) && '' != $this->server['PHP_AUTH_USER'] && isset($this->server['PHP_AUTH_PW']) && ! is_null($this->server['PHP_AUTH_PW']) && '' != $this->server['PHP_AUTH_PW'] ) {
            return true;
        }
        return false;
    }

    public function processLoginAttempt()
    {
        try {
            $user = $this->userFactory->getFromUserIdAndAPIKey($this->server['PHP_AUTH_USER'], $this->server['PHP_AUTH_PW']);
            $this->persist = false;
            return $user;
        } catch( \Exception $e) {
            return null;
        }

    }

    public function logout()
    {
        return true;
    }

    public function userWantsToBeRemembered()
    {
        return false;
    }

    public function shouldPersist()
    {
        return $this->persist;
    }

}
