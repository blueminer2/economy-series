<?php

/*
__PocketMine Plugin__
name=deathnote
version=0.0.2
author=miner of mcpekorea
class=dn
*/

class dn implements Plugin{
	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->api->console->register("dn", "kills a player with deathnote", array($this, "handleCommand"));
	}
	
	public function __destruct(){
	
	}
	
	public function handleCommand($cmd, $args, $issuer){
			switch($cmd){
                          case "dn write":
                          $player = $this->api->player->get($name);
                          $this->api->player->kill
                          $this->server->api->chat->broadcast("[deathnote] $player has been written to the deathnote");
                          break;
                          }
 }
}                                                    