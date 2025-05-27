<?php
namespace SoampliApps\Authentication;

interface APIUserFactoryInterface extends UserFactoryInterface
{
    public function getFromUserIdAndAPIKey($user_id, $api_key);
}
