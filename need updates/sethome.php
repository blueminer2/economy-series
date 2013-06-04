<?php

/*
__PocketMine Plugin__
name=sethome
version=0.0.2
author=miner of mcpekorea
class=sh
apiversion=7
*/

class sh implements Plugin{
	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
                $spawn = $this->api->level->getSpawn();
                $this->path = $this->api->plugin->createConfig($this, array(
                        "homeX" => $spawn["x"],
			"homeY" => $spawn["y"],
			"homeZ" => $spawn["z"],
			"change-home" => true,
		));
		$this->config = $this->api->plugin->readYAML($this->path."config.yml");
		if($this->config["change-home"] === false){
			$this->config["homeX"] = $spawn["x"];
			$this->config["homeY"] = $spawn["y"];
			$this->config["homeZ"] = $spawn["z"];
			$this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	}
			$this->api->console->register("home", "Teleports home", array($this, "command"));
			$this->api->console->register("sethome", "sethome inits the home position", array($this, "command"));
	}

	public function __destruct(){

	}

	public function handleCommand($cmd, $arg){
		switch($cmd){
			case "":
					switch(strtolower(array_shift($args))){
						case "sethome":
							$z = array_pop($args);
							$y = array_pop($args);
							$x = array_pop($args);
							if($x === null or $y === null or $z === null){
								console("[sethome] Usage: ///sethome <x> <y> <z>");
							}else{
								$this->config["change-home"] = true;
								$this->config["homeX"] = (float) $x;
								$this->config["homeY"] = (float) $y;
								$this->config["homeZ"] = (float) $z;
								console("[sethome] Home set to X ".$this->config["spawnX"]." Y ".$this->config["spawnY"]." Z ".$this->config["spawnZ"]);
								$this->api->plugin->writeYAML($this->path."config.yml", $this->config);
							}
							break;
						default:
							console("[sethome] Set the home point: /sethome <x> <y> <z>");
							break;
                                                        case "home":
                                                        if($this->api->player->tppos(implode(" ", $args), $this->config["homeX"], $this->config["homeY"], $this->config["homeZ"]) !== false){
                                                          console("[sethome] Teleported to home");
				}else{
					console("[sethome] Usage: /home <player>");
				}
				break;
				                                                    }
				   }
				 }  

	public function handle(&$data, $event){
		switch($event){
			case "api.player.offline.get":
				if($this->config["force-spawn"] === true){
					$data["home"]["x"] = $this->config["homeX"];
					$data["home"]["y"] = $this->config["homeY"];
					$data["home"]["z"] = $this->config["homeZ"];
				}
				break;
		}
	}

}