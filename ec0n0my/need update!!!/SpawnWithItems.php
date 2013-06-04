<?php

/*
 __PocketMine Plugin__
name=SpawnWithItems
description=When you connect to a server, give you selected items automatically.
version=1.2
author=MinecrafterJPN
class=SpawnWithItems
apiversion=4,5,6
*/

class SpawnWithItems implements Plugin{
	private $api;
	private $path;

	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}

	public function init(){
		$this->api->addHandler("player.join", array($this, 'eventHandler'), 10);
		$this->api->console->register("swi", "Give items to all joiners automatically when they connect", array($this, "commandHandler"));
		$this->path = $this->api->plugin->createConfig($this, array());
	}

	public function eventHandler($data, $event){
		switch($event){
			case "player.join":
				$spawnAt = $data->data->get('position');
				$vector = new Vector3($spawnAt['x'], $spawnAt['y'], $spawnAt['z']);
				$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
				foreach($cfg as $info){
					if($info['ID'] === 'money'){
						$d = array(
								'username' => $data->username,
								'method' => 'grant',
								'amount' => $info['count']
						);
						$this->api->handle("money.handle", $d);
					}else{
						$item = BlockAPI::getItem($info['ID'], $info['meta'], $info['count']);
						$this->api->block->drop($vector, $item);
					}
				}
				break;
		}
	}

	public function commandHandler($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "swi":
				if($issuer !== "console"){
					$output .= "Must be run on the console.\n";
					break;
				}
				if(!isset($params[0])){
					console("Usage: /swi <itemID> [count] [meta] or /swi money <amount>");
					break;
				}
				if($params[0] === "money"){
					if(!is_numeric($params[1]) or $params[1] < 0){
						console("[SpawnWithItems]<amount> : numeric and over 0");
						break;
					}
					$dat = array(
							array(
									'ID' => 'money',
									'count' => $params[1],
									'meta' => 0
							)
					);
					$this->overwriteConfig($dat);
					console("[SpawnWithItems]Money : $params[1]");
				}else{
					if(!is_numeric($params[0])){
						console("[SpawnWithItems]<itemID> : numeric");
						break;
					}
					$itemID = $params[0];
					if(isset($params[1])){
						if(is_numeric($params[1]) === false){
							console("[SpawnWithItems][count] : numeric");
							break;
						}
						$count = trim($params[1]);
					}else{
						$count = 10;
					}
					if(isset($params[2])){
						if(is_numeric($params[2]) === false){
							console("[SpawnWithItems][meta] : numeric");
							break;
						}
						$meta = trim($params[2]);
					}else{
						$meta = 0;
					}
					$dat = array(
							array(
									'ID' => $itemID,
									'count' => $count,
									'meta' => $meta
							)
					);
					$this->overwriteConfig($dat);
					console("[SpawnWithItems]$itemID : $count : $meta");
				}
				break;
		}
		return $output;
	}

	private function overwriteConfig($dat){
		$cfg = array();
		$cfg = $this->api->plugin->readYAML($this->path . 'config.yml');
		$result = array_merge($cfg, $dat);
		$this->api->plugin->writeYAML($this->path.'config.yml', $result);
	}

	public function __destruct(){
	}
}
