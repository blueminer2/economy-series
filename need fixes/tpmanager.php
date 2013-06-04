<?php

/*
__PocketMine Plugin__
name=tpmanager
version=0.0.1
author=miner of mcpekorea
class=tpmgr
*/

class tpmgr implements Plugin{
	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}

	public function init(){
		$this->api->console->register("tp", "teleport", array($this, "handleCommand"));
		$this->api->console->register("tpos", "teleport to a position", array($this, "handleCommand"));
		$this->api->console->register("tpall", "teleport everyone to you", array($this, "handleCommand"));
	}

	public function __destruct(){

	}

	public function handleCommand($cmd, $arg){
		switch($cmd){
			case "tp":
                        $target = $this->api->player->get($target);
                        if(($target instanceof Player) and ($target->entity instanceof Entity)){
                          return $this->tppos($name, $target->entity->x, $target->entity->y, $target->entity->z);
                          }
                          return false;
                          break;
                        case "tpos":
                        $player = $this->api->player->get($name);
                        if(($player instanceof Player) and ($player->entity instanceof Entity)){
                          $player->teleport(new Vector3($x, $y, $z));
                          return true;
                          }
                          return false;
                          break;
                        case "tpall":
                        $target = $this->api->player->getall;
                        if(($target instanceof Player) and ($target->entity instanceof Entity)){
                          return $this->tppos($name, $target->entity->x, $target->entity->y, $target->entity->z);
                          }
                          return false;
                          break;
  }
 }
}
