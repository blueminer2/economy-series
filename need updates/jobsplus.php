<?php

/*
 __PocketMine Plugin__
name=Jobs+
description=this is a job plugin
version=0.0.1
author=miner of mcpekorea
class=jobsplus
apiversion=4,5,6
*/

class jobsplus implements Plugin{
	private $api, $path, $jobList = array();
	
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}

	public function init(){
		$this->path = $this->api->plugin->createConfig($this, array());
		if(filesize($this->path . "config.yml") <= 4){
			$this->defaultConfig();
		}
		$this->jobList = $this->api->plugin->readYAML($this->path . "config.yml");
		touch($this->path . "playerlist.yml");
		if(filesize($this->path . "playerlist.yml") === 0){
			$this->api->plugin->writeYAML($this->path . "playerlist.yml", array());
		}
		$this->api->console->register("jobs", "Super command of PocketJobs", array($this, "commandHandler"));
		$this->api->addHandler("player.block.break", array($this, "eventHandler"));
		$this->api->addHandler("player.block.place", array($this, "eventHandler"));
	}

	public function __destruct(){
	}

	public function eventHandler($data, $event){
		switch($event){
			case "player.block.break":
				$this->workCheck("break", $data['player']->username, $data['target']->getID(), $data['target']->getMetadata());
				break;
			case "player.block.place":
				$this->workCheck("place", $data['player']->username, $data['item']->getID(), $data['item']->getMetadata());
				break;
		}
	}

	public function commandHandler($cmd, $args, $issuer, $alias){
		$output = "";
		$cmd = strtolower($cmd);
		$subCmd = strtolower($args[0]);
		switch($cmd){
			case "jobs":
				switch($subCmd){
					case "":
						$output .= "[jobsplus]/jobs :: show the commands available to you\n";
						$output .= "[jobsplus]/jobs browse :: search the jobs available to you\n";
						$output .= "[jobsplus]/jobs join <jobname> :: join the selected job\n";
						$output .= "[jobsplus]/jobs leave <jobname> :: leave the selected job\n";
						$output .= "[jobsplus]/jobs info <jobname> :: show the detail of selected job\n";
						$output .= "[jobsplus]/jobs reset :: reset the job list to default\n";
						break;
					case "browse":
						$output .= "[jobsplus]";
						foreach($this->jobList as $job){
							$output .= $job['jobname'] . " ";
						}
						$output .= "\n";
						break;
					case "join":
						if($issuer === "console"){
							console("[jobsplus]Must be run in the world.");
							break;
						}
						if(!isset($args[1])){
							$output .= "Usage: /jobs join <jobname>\n";
							break;
						}
						$jobname = strtolower($args[1]);
						$jobExist = false;
						foreach($this->jobList as $job){
							if(strtolower($job['jobname']) === $jobname){
								$jobExist = true;
							}
						}
						if(!$jobExist){
							$output .= "[jobsplus]$args[1] not found.";
							break;
						}
						$output .= $this->joinJob($issuer->username, $jobname);					
						break;
					case "leave":
						if($issuer === "console"){
							console("[jobsplus]Must be run on the world.");
							break;
						}
						if(!isset($args[1])){
							$output .= "Usage: /jobs leave <jobname>\n";
							break;
						}
						$jobname = strtolower($args[1]);
						$jobExist = false;
						foreach($this->jobList as $job){
							if(strtolower($job['jobname']) === $jobname){
								$jobExist = true;
							}
						}
						if(!$jobExist){
							$output .= "[jobsplus]$args[1] not found.";
							break;							
						}
						$output .= $this->leaveJob($issuer->username, $jobname);
						break;
					case "info":
						if(isset($args[1]) === false){
							$output .= "Usage: /jobs info <jobname>\n";
							break;
						}
						$jobname = strtolower($args[1]);
						$jobExist = false;
						foreach($this->jobList as $job){
							if(strtolower($job['jobname']) === $jobname){
								$jobExist = true;
							}
						}
						if(!$jobExist){
							$output .= "[jobsplus]$args[1] not found.";
							break;
						}
						$output .= $this->infoJob($jobname);
						break;
					case "stat":
						if($issuer === "console"){
							console("[jobsplus]Must be run in the world.");
							break;
						}
						$output .= "[jobsplus]Unimplemented wait for a moment :)";
						break;
					case "reset":
						if($issuer !== "console"){
							$output .= "[jobsplus]Must be run on the console.\n";
							break;
						}
						console("[jobsplus]Reseting the config. . .");
						$this->defaultConfig();
						console("[jobsplus]succeeded process");
						break;
				}
		}
		return $output;
	}

	private function joinJob($username, $job){
		$config = $this->api->plugin->readYAML($this->path . "playerlist.yml");
		if(isset($config[$username]['slot1'])){
			if(isset($config[$username]['slot2'])){
				return "[jobsplus]Your job slot is full.\n";
			}
			$result = array(
					$username => array(
							'slot2' => $job
					)
			);
			$this->overwriteConfig2($result, $this->path . "playerlist.yml");
			return "[jobsplus]Set $job to your job slot2.\n";
		}
		$result = array(
				$username => array(
						'slot1' => $job,
						'slot2' => null
				)
		);
		$this->overwriteConfig2($result, $this->path .  "playerlist.yml");
		return "[jobsplus]Set $job to your job slot1.\n";
	}

	private function leaveJob($username, $job){
		$config = $this->api->plugin->readYAML($this->path . "playerlist.yml");
		if(!array_key_exists($username, $config)) return "[jobsplus]You have no jobs.\n";
		if($config[$username]['slot1'] === $job){
			$result = array(
					$username => array(
							'slot1' => null
					)
			);
			$this->overwriteConfig2($result, $this->path .  "playerlist.yml");
			return "[jobsplus]Remove $job from your job slot1.\n";
		}elseif($config[$username]['slot2'] === $job){
			$result = array(
					$username => array(
							'slot2' => null
					)
			);
			$this->overwriteConfig2($result, $this->path .  "playerlist.yml");
			return "[jobsplus]Remove $job from your job slot2.\n";
		}else{
			return "[jobsplus]You are not part of $job\n";
		}
	}

	private function infoJob($jobname){
		$output = "";
		foreach($this->jobList as $job){
			if(strtolower($job['jobname']) === $jobname){
				$output .= "[jobsplus]$jobname\n";
				foreach($job['salary'] as $type => $detail){
					foreach($detail as $value){
						$id = $value['ID'];
						$meta = $value['meta'];
						$amount = $value['amount'];
						$output .= "[jobsplus]$type $id:$meta $amount\n";
					}
				}
			}
		}
		return $output;
	}

	private function workCheck($type, $username, $id, $meta){
		$jobConfig = $this->api->plugin->readYAML($this->path . "config.yml");
		$playerConfig = $this->api->plugin->readYAML($this->path . "playerlist.yml");
		$flag = false;
		if(array_key_exists($username, $playerConfig) === false){
			return;
		}
		foreach($this->jobList as $job){
			foreach($job['salary'] as $jobType => $detail){
				if($jobType === $type){
					foreach($detail as $value){
						if($value['ID'] === $id and $value['meta'] === $meta){
							$targetJob = strtolower($job['jobname']);
							$amount = $value['amount'];
							$flag = true;
						}
					}
				}
			}
		}
		if(!$flag) return;
		if($playerConfig[$username]['slot1'] === $targetJob or $playerConfig[$username]['slot2'] === $targetJob){
			$money = array(
					'username' => $username,
					'method' => 'grant',
					'amount' => $amount
			);
			$this->api->handle("money.handle", $money);
		}
	}

	private function defaultConfig(){
		$config = array(
				array(
						'jobname' => 'Woodcutter',
						'salary' => array(
								'break' => array(
										array(
												'ID' => 17,
												'meta' => 0,
												'amount' => 25
										),
										array(
												'ID' => 17,
												'meta' => 1,
												'amount' => 25
										),
										array(
												'ID' => 17,
												'meta' => 2,
												'amount' => 25
										),
										array(
												'ID' => 17,
												'meta' => 3,
												'amount' => 25
										),
								),
								'place' => array(
										array(
												'ID' => 6,
												'meta' => 0,
												'amount' => 1
										),
										array(
												'ID' => 6,
												'meta' => 1,
												'amount' => 1
										),
										array(
												'ID' => 6,
												'meta' => 2,
												'amount' => 1
										),
										array(
												'ID' => 6,
												'meta' => 3,
												'amount' => 1
										)
								)
						)
				),
				array(
						'jobname' => 'Miner',
						'salary' => array(
								'break' => array(
										array(
												'ID' => 1,
												'meta' => 0,
												'amount' => 3
										),
										array(
												'ID' => 14,
												'meta' => 0,
												'amount' => 25
										),
										array(
												'ID' => 15,
												'meta' => 0,
												'amount' => 20
										),
										array(
												'ID' => 21,
												'meta' => 0,
												'amount' => 17
										),
										array(
												'ID' => 49,
												'meta' => 0,
												'amount' => 9
										),
										array(
												'ID' => 56,
												'meta' => 0,
												'amount' => 80
										),
										array(
												'ID' => 73,
												'meta' => 0,
												'amount' => 10
										)
								)
						)
				),
				array(
						'jobname' => 'builder',
						'salary' => array(
								'place' => array(
								                array(
								                		'ID' => 5,
								                		'meta' => 0,
								                		'amount' => 1,
								                ),
								                array(
								                		'ID' => 4,
								                		'meta' => 0,
								                		'amount' => 1,
								                ),
								                array(
								                		'ID' => 24,
								                		'meta' => 0,
								                		'amount' => 2,
								                ),
								                array(
								                		'ID' => 24,
								                		'meta' => 1,
								                		'amount' => 2,
								                ),
								                array(
								                		'ID' => 24,
								                		'meta' => 2,
								                		'amount' => 2,
								                ),
								                array(
								                		'ID' => 24,
								                		'meta' => 3,
								                		'amount' => 2,
								                ),
								                array(
								                		'ID' => 42,
								                		'meta' => 0,
								                		'amount' => 10,
								                ),
								                array(
								                		'ID' => 41,
								                		'meta' => 0,
								                		'amount' => 10,
								                ),
								                array(
								                		'ID' => 43,
								                		'meta' => 0,
								                		'amount' => 5,
								                ),
								                array(
								                		'ID' => 44,
								                		'meta' => 0,
								                		'amount' => 3,
								                ),
								                array(
								                		'ID' => 45,
								                		'meta' => 0,
								                		'amount' => 8,
								                ),
								                array(
								                		'ID' => 42,
								                		'meta' => 0,
								                		'amount' => 10,
								                ),
								                array(
								                		'ID' => 48,
								                		'meta' => 0,
								                		'amount' => 10,
								                ),
								                array(
								                		'ID' => 49,
								                		'meta' => 0,
								                		'amount' => 10,
								                ),
								                array(
								                		'ID' => 53,
								                		'meta' => 0,
								                		'amount' => 3,
								                ),
								                array(
								                		'ID' => 64,
								                		'meta' => 0,
								                		'amount' => 4,
								                ),
								                array(
								                		'ID' => 67,
								                		'meta' => 0,
								                		'amount' => 4,
								                ),
								                array(
								                		'ID' => 65,
								                		'meta' => 0,
								                		'amount' => 2,
								                ),
								                array(
								                		'ID' => 98,
								                		'meta' => 0,
								                		'amount' => 5,
								                ),
								                array(
								                		'ID' => 98,
								                		'meta' => 1,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 98,
								                		'meta' => 2,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 89,
								                		'meta' => 0,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 102,
								                		'meta' => 0,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 103,
								                		'meta' => 0,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 155,
								                		'meta' => 0,
								                		'amount' => 8,
								                ),
								                array(
								                		'ID' => 155,
								                		'meta' => 1,
								                		'amount' => 8,
								                ),
								                array(
								                		'ID' => 155,
								                		'meta' => 2,
								                		'amount' => 8,
								                ),
								                array(
								                		'ID' => 156,
								                		'meta' => 0,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 112,
								                		'meta' => 0,
								                		'amount' => 9,
								                ),
                                                                                ),
                                                                                'break' => array(
								                array(
								                		'ID' => 5,
								                		'meta' => 0,
								                		'amount' => 1,
								                ),
								                array(
								                		'ID' => 4,
								                		'meta' => 0,
								                		'amount' => 1,
								                ),
								                array(
								                		'ID' => 24,
								                		'meta' => 0,
								                		'amount' => 2,
								                ),
								                array(
								                		'ID' => 24,
								                		'meta' => 1,
								                		'amount' => 2,
								                ),
								                array(
								                		'ID' => 24,
								                		'meta' => 2,
								                		'amount' => 2,
								                ),
								                array(
								                		'ID' => 24,
								                		'meta' => 3,
								                		'amount' => 2,
								                ),
								                array(
								                		'ID' => 42,
								                		'meta' => 0,
								                		'amount' => 10,
								                ),
								                array(
								                		'ID' => 41,
								                		'meta' => 0,
								                		'amount' => 10,
								                ),
								                array(
								                		'ID' => 43,
								                		'meta' => 0,
								                		'amount' => 5,
								                ),
								                array(
								                		'ID' => 44,
								                		'meta' => 0,
								                		'amount' => 3,
								                ),
								                array(
								                		'ID' => 45,
								                		'meta' => 0,
								                		'amount' => 8,
								                ),
								                array(
								                		'ID' => 42,
								                		'meta' => 0,
								                		'amount' => 10,
								                ),
								                array(
								                		'ID' => 48,
								                		'meta' => 0,
								                		'amount' => 10,
								                ),
								                array(
								                		'ID' => 49,
								                		'meta' => 0,
								                		'amount' => 10,
								                ),
								                array(
								                		'ID' => 53,
								                		'meta' => 0,
								                		'amount' => 3,
								                ),
								                array(
								                		'ID' => 64,
								                		'meta' => 0,
								                		'amount' => 4,
								                ),
								                array(
								                		'ID' => 67,
								                		'meta' => 0,
								                		'amount' => 4,
								                ),
								                array(
								                		'ID' => 65,
								                		'meta' => 0,
								                		'amount' => 2,
								                ),
								                array(
								                		'ID' => 98,
								                		'meta' => 0,
								                		'amount' => 5,
								                ),
								                array(
								                		'ID' => 98,
								                		'meta' => 1,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 98,
								                		'meta' => 2,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 89,
								                		'meta' => 0,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 102,
								                		'meta' => 0,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 103,
								                		'meta' => 0,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 155,
								                		'meta' => 0,
								                		'amount' => 8,
								                ),
								                array(
								                		'ID' => 155,
								                		'meta' => 1,
								                		'amount' => 8,
								                ),
								                array(
								                		'ID' => 155,
								                		'meta' => 2,
								                		'amount' => 8,
								                ),
								                array(
								                		'ID' => 156,
								                		'meta' => 0,
								                		'amount' => 6,
								                ),
								                array(
								                		'ID' => 112,
								                		'meta' => 0,
								                		'amount' => 9,
										)
								)
						)
				)
				);
		$this->api->plugin->writeYAML($this->path . "config.yml", $config);
	}

	private function overwriteConfig($dat){
		$cfg = array();
		$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
		$result = array_merge($cfg, $dat);
		$this->api->plugin->writeYAML($this->path."config.yml", $result);
	}

	private function overwriteConfig2($dat, $path){
		$cfg = array();
		$cfg = $this->api->plugin->readYAML($path);
		$result = array_merge($cfg, $dat);
		$this->api->plugin->writeYAML($path, $result);
	}
}