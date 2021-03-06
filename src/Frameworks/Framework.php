<?php

namespace BlueRaster\PowerBIAuthProxy\Frameworks;

use BlueRaster\PowerBIAuthProxy\Utils;
use BlueRaster\PowerBIAuthProxy\Utils\Csrf;
use BlueRaster\PowerBIAuthProxy\Exceptions\MissingUserProviderException;
use BlueRaster\PowerBIAuthProxy\UserProviders\UserProvider;
use BlueRaster\PowerBIAuthProxy\UserProviders\BaseUser;

abstract class Framework{

// 	protected $user;

	protected $user_providers;

	protected $user_provider;

	protected $config;

	public function __construct(Array $config = []){
		$this->config = $config;
	}

	public function getConfig(){
    	return $this->config;
	}

	public function installerPath(){
		$classname = class_basename($this);
		return Utils::root_path("installers/$classname/installer.php");
	}

	public static function test(){
		return false;
	}

	final public function getUserProvider() : UserProvider{
		if($this->user_provider) return $this->user_provider;
		$this->user_provider = collect($this->user_providers)->map(function($classname){
			$p = "BlueRaster\\PowerBIAuthProxy\\UserProviders\\$classname";

			if($p::test($this)){
				return new $p;
			}
		})->filter()->first();

		if(empty($this->user_provider)){
			throw new MissingUserProviderException;
		}


		return $this->user_provider;
	}

	// Provides the currently used "user" object that the framework is utilizing.
	//
	final public function getUser() : BaseUser{
		return $this->getUserProvider()->getUser();
	}


	abstract public function getCsrf() : Csrf;
}
