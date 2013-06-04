<?php

/*
 __PocketMine Plugin__
name=ec0n0my
description=ec0n0my pluigin is a economy plugin
version=0.0.2
author=miner of MCPEKOREA
class=economy
apiversion=4,5,6
*/

define(DEFAULT_MONEY, 30);

class economy implements Plugin{
	private $api, $path;

	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}

	public function init(){
		$this->api->addHandler("player.join", array($this, "eventHandler"));
		$this->api->addHandler("money.handle", array($this, "eventHandler"));
		$this->api->addHandler("money.player.get", array($this, "eventHandler"));
		$this->api->console->register("money", "command for handling Money", array($this, "commandHandler"));
		$this->path = $this->api->plugin->createConfig($this, array());
	}

	public function eventHandler($data, $event){
		$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
		switch($event){
			case "player.join":
				$target = $data->username;
				if(array_key_exists($target, $cfg)) break;
				$this->api->plugin->createConfig($this,array(
						$target => array(
								'money' => DEFAULT_MONEY
						)
				));
				$this->api->chat->broadcast("[ec0n0my]$target has joined to ec0n0my.");
				break;
			case "money.handle":
				if(isset($data['username']) or !isset($data['method']) or !isset($data['amount'])) return false;
				$target = $data['username'];
				$method = $data['method'];
				$amount = (int)$data['amount'];
				if(!$this->api->player->get($target) or !array_key_exists($target, $cfg) or !is_numeric($amount)){
					return false;
				}
				switch($method){
					case "set":
						if($amount < 0){
							return false;
						}
						$result = array(
								$target => array(
										'money' => $amount
								)
						);
						$this->overwriteConfig($result);
						break;
					case "grant":
						$targetMoney = $cfg[$target]['money'] + $amount;
						if($targetMoney < 0) return false;
						$result = array(
								$target => array(
										'money' => $targetMoney
								)
						);
						$this->overwriteConfig($result);
						break;
					default:
						return false;
				}
			case "money.player.get":
				if(array_key_exists($data['username'], $cfg))
				{
					return $cfg[$data['username']]['money'];
				}
				return false;
				break;
		}
	}

	public function commandHandler($cmd, $args, $issuer, $alias){
		$cmd = strtolower($cmd);
		if($issuer === "console"){
			switch($cmd){
				case "money":
					$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
					$subCommand = $args[0];
					switch($subCommand){
						case "set":
							$target = $args[1];
							if(!$this->api->player->get($target)){
								console("[ec0n0my]$target is offline.");
								break;
							}
							if(!array_key_exists($target, $cfg)){
								console("[ec0n0my]$target is not part of a ec0n0my.");
								break;
							}								
							$amount = $args[2];
							if($amount < 0 or !is_numeric($amount)){
								console("[ec0n0my]$amount is an invalid Smoney.");
								break;
							}								
							$result = array(
									$target => array(
											'money' => $amount
									)
							);
							$this->overwriteConfig($result);
							console("[ec0n0my]have set your money.");
							$this->api->chat->sendTo(false, "[ec0n0my][set]Your money has been changed.\n$target:$amount Smoney", $target);
							break;
						case "grant":
							$target = $args[1];
							if(!$this->api->player->get($target)){
								console("[ec0n0my]$target is offline.");
								break;
							}
							if(!array_key_exists($target, $cfg)){
								console("[ec0n0my]$target is not part of a ec0n0my.");
								break;
							}								
							$amount = $args[2];
							$targetMoney = $cfg[$target]['money'] + $amount;
							if(!is_numeric($amount) or $targetMoney < 0){
								console("[PocketMoney]$amount is an Invalid Smoney.");
								break;
							}
							$result = array(
									$target => array(
											'money' => $targetMoney
									)
							);
							$this->overwriteConfig($result);
							console("[ec0n0my]granted you $amount.");
							$this->api->chat->sendTo(false, "[INFO][grant]You Smoney is changed.\n$target:$targetMoney Smoney", $target);
							break;
						case "top":
							$amount = $args[1];
							$temp = array();
							foreach($cfg as $name => $elements){
								$temp[$name] = $elements['money'];
							}
							arsort($temp);
							$i = 1;
							console("[ec0n0my]Lists the rich");
							foreach($temp as $name => $money){
								if($i > $amount){
									break;
								}
								console("#$i : $name $money Smoney");
								$i++;
							}
							break;
						case "stat":
							$total = 0;
							$num = 0;
							foreach($cfg as $name => $elements){
								$total += $elements['money'];
								$num++;
							}
							$avr = floor($total / $num);
							console("[ec0n0my]Circulation:$total Average:$avr Accounts:$num");
							break;
						default:
							console("[ec0n0my]that command dose not exist.");
							break;
					}
					break;
			}
		}else{
			$output = "";
			switch($cmd){
				case "money":
					$subCommand = $args[0];
					$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
					switch($subCommand){
						case "":
							if(!array_key_exists($issuer->username, $cfg)){
								$output .= "[ec0n0my]You is not part of ec0n0my.";
								break;
							}
							$money = $cfg[$issuer->username]['money'];
							$output .= "[ec0n0my]$money Smoney";
							break;
						case "pay":
							$target = $args[1];
							$payer = $issuer->username;
							if(!$this->api->player->get($target)){
								$output .= "[ec0n0my]$target is offline.";
								break;
							}
							if(!array_key_exists($target, $cfg) or !array_key_exists($payer, $cfg)){
								$output .= "[ec0n0my]Either You or $target is not part of ec0n0my.";
								break;
							}								
							$targetMoney = $cfg[$target]['money'];
							$payerMoney = $cfg[$payer]['money'];
							$amount = $args[2];
							if(!is_numeric($amount) or $amount < 0 or $amount > $payerMoney){
								$output .= "[ec0n0my]$amount is an Invalid amount.";
								break;
							}
							$targetMoney += $amount;
							$payerMoney -= $amount;
							$result = array(
									$payer => array(
											'money' => $payerMoney
									),
									$target => array(
											'money' => $targetMoney
									)
							);
							$this->overwriteConfig($result);
							$output .= "[ec0n0my]paid you Smoney.";
							$this->api->chat->sendTo(false, "[PocketMoney]$amount Smoney is paid from $payer", $target);
							break;
						case "top":
							$amount = $args[1];
							$temp = array();
							foreach($cfg as $name => $elements){
								$temp[$name] = $elements['money'];
							}
							arsort($temp);
							$i = 1;
							$output .= "[ec0n0my]Lists the rich\n";
							foreach($temp as $name => $money){
								if($i > $amount){
									break;
								}
								$output .= "#$i : $name $money Smoney\n";
								$i++;
							}
							break;
						case "stat":
							$total = 0;
							$num = 0;
							foreach($cfg as $name => $elements){
								$total += $elements['money'];
								$num++;
							}
							$avr = floor($total / $num);
							$output .= "[ec0n0my]Circ:$total Avr:$avr Accounts:$num";
							break;
						default:
							$output .= "[ec0n0my]that sub command dose not exist.";
							break;
					}
					break;
			}
			return $output;
		}
	}

	private function overwriteConfig($dat){
		$cfg = array();
		$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
		$result = array_merge($cfg, $dat);
		$this->api->plugin->writeYAML($this->path."config.yml", $result);
	}
	
	public function __destruct(){
	}
}