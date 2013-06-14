<?php

/*
__PocketMine Plugin__
name=Life
version=0.0.1
author=miner&onebone
class=life
apiversion=8
*/

define('DEFAULT_AGE', 1);
define('GENDER', notselected);

class life implements Plugin{
	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}

	public function init(){
		$this->path = $this->api->plugin->createConfig($this, array());

                $this->api->addHandler("player.join", array($this, "eventHandler"));
                $this->api->addHandler("player.gender", array($this, "eventHandler"));
                $this->api->addHandler("player.age", array($this, "eventHandler"));

                $this->api->console->register("Life", "Command to handle your life", array($this, "handleCommand"));
                $this->api->console->alias("choosemale", "Life");
                $this->api->console->alias("choosefemale", "Life");
		$this->api->console->register("marriage", "command that covers most of the marriage", array($this, "handleCommand"));
                $this->api->console->alias("accept", "marriage");
                $this->api->console->alias("decline", "marriage");
                $this->api->console->alias("devorce", "marriage");
		$this->api->console->alias("baby", "marriage");
		$this->api->console->alias("babyaccept", "marriage");
		$this->api->console->alias("babydecline", "marriage");
	}

	public function __destruct(){

	}

	public function eventHandler($event, $data){
		$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
                switch($event){
			case "player.join":
			$target = $data->username;
                        if(array_key_exists($target, $cfg)) break;
                        $this->api->plugin->createConfig($this,array(
						$target => array(
								'age' => DEFAULT_AGE
								'gender' => GENDER
						)
				));
				$this->api->chat->broadcast("[Life]$target is born... please select gender.");
				$this->api->chat->broadcast("[Life]To select gender, type /choosemale or /choosefemale.");
				break;
                                 //for this part i used part of the older version of pocketmoney...
	public function handleCommand($cmd, $arg){
		switch($cmd){
			case "marriage":
			switch(//subcommands
				break;
				/*


                                how to use money.handle
                                $this->api->handle("money.handle", $d);
                                
                                for ex)
                                switch($cmd){
                                  case "buy":
                                  if ($issuer == $cmd){
                                    $this->api->chat->broadcast("please run in game");
                                  }else{
                                    $d = $this->api->plugin->readYAML($this->path . "config.yml");
                                    $this->api->handle("money.handle", $d);

				*/
		}
	}

}