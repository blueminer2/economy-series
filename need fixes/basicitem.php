<?php

/*
__PocketMine Plugin__
name=basicitem
description=this plugin gives you items everytime you join
version=0.0.1
author=miner of mcpekorea
class=bi
apiversion=7
*/

/* 
Small Changelog
===============

0.0.1: Initial release

*/



class bi implements Plugin{

	private $api, $path;

	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->api->addHandler("player.join", array($this, "eventHandler"));
                $this->path = $this->api->plugin->createConfig($this, array(
                	STONE_SWORD => "STONE_SWORD",
			STONE_SHOVEL => "STONE_SHOVEL",
			STONE_PICKAXE => "STONE_PICKAXE",
			STONE_AXE => "STONE_AXE",
                ));
	}

	public function eventHandler($data, $event) {
	$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
	switch($cmd){
		case "player.join":
			$player = $this->get($target);
			if($player->gamemode === SURVIVAL){
					if($this->api->getProperty("item-enforcement") === true){
						$data["player"]->addItem($cfg, 0, 1));
					}else{
						$this->api->entity->drop(new Position($data["player"]->entity->x, $data["player"]->entity->y, $data["player"]->entity->z, $data["player"]->level), BlockAPI::getItem($cfg));
					}
			$this->api->chat->broadcast("[basicitem]gave $player the basic items.");
	}
	}

	public function __destruct(){

	}

	
}
