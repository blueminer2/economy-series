<?php

/*
__PocketMine Plugin__
name=marriage
version=0.0.1
author=miner&onebone
class=m
apiversion=7
*/

class m implements Plugin{
	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->api->console->register("marriage", "command that covers most of the marriage", array($this, "handleCommand"));
		$this->api->console->alias("baby", "");
	}
	
	public function __destruct(){
	
	}
	
	public function handleCommand($cmd, $arg){
		switch($cmd){
			case "example":
				console("EXAMPLE!!!");
				break;
		}
	}

}