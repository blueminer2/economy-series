<?php

/*
__PocketMine Plugin__
name=Life
version=0.0.1
author=miner
class=life
apiversion=9
*/

define("DEFAULT_GENDER", DEFAULT_GENDER);
define("DEFAULT_AGE", DEFAULT_AGE);
define("DEFAULT_MARRIED", DEFAULT_MARRIED);
define("DEFAULT_MARRIED1", DEFAULT_MARRIED1);
define("DEFAULT_NO", DEFAULT_NO);

class life implements Plugin{
	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}

	public function init(){
		$this->path = $this->api->plugin->createConfig($this, array());

                $this->api->addHandler("player.join", array($this, "eventHandler"));

                $this->api->console->register("lifehelp", "Command to guide the course of your life", array($this, "handleCommand"));
                $this->api->console->register("lifehelp1", "Command to guide the course of your life", array($this, "handleCommand"));
                $this->api->console->register("lifehelp2", "Command to guide the course of your life", array($this, "handleCommand"));
                $this->api->console->register("life", "Command to handle your life", array($this, "handleCommand"));
                $this->api->console->alias("growup", "life");
                $this->api->console->alias("choosemale", "life");
                $this->api->console->alias("choosefemale", "life");
		$this->api->console->register("marriage", "command that covers most of the marriage", array($this, "handleCommand"));
		$this->api->console->alias("propose", "marriage");
                $this->api->console->alias("decline", "marriage");
                $this->api->console->alias("devorce", "marriage");
		$this->api->console->alias("baby", "marriage");
	}

	public function __destruct(){

	}

        	public function eventHandler($event, $data){
		$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
                switch($event){
			case "player.join":
			$target = $data->username;
                        if(array_key_exists($target, $cfg))
                        {
                        $this->api->plugin->createConfig($this,array(
						$target => array(
								'age' => DEFAULT_AGE
								'gender' => DEFAULT_GENDER
								'married' => DEFAULT_MARRIED
								'married1' => DEFAULT_MARRIED1
								'babyavailable' => DEFAULT_NO
						)
				));
				$this->api->chat->broadcast("[Life]$target is born... please select gender.");
				$this->api->chat->broadcast("[Life]To select gender, type /choosemale or /choosefemale.");
			}
                        break;
                }
         }
	public function handleCommand($cmd, $arg, $args, $issuer, $alias)
        {
          $output = "";
		switch($cmd){
			case "lifehelp":
			$this->api->chat->sendTo("[Life]type /choosemale to become a man");
			$this->api->chat->sendTo("[Life]type /choosefemale to become a woman";
                        $this->api->chat->sendTo("[Life]type /growup <number> to grow up";
                        $this->api->chat->sendTo("[Life]type /life for more infos";
                        break;

			case "lifehelp1":
			$this->api->chat->sendTo("[Life]type /choosemale to become a man");
			$this->api->chat->sendTo("[Life]type /choosefemale to become a woman";
                        $this->api->chat->sendTo("[Life]type /growup <number> to grow up";
                        $this->api->chat->sendTo("[Life]type /life for more infos";
			break;

			case "lifehelp2":
			$this->api->chat->sendTo("[Life]type /propose to marry someone";
			$this->api->chat->sendTo("[Life]type /decline to say no (if you don't that will mean yes)";
			$this->api->chat->sendTo("[Life]type /devorce to devorce";
			$this->api->chat->sendTo("[Life]type /baby to have a baby (only for women = if you are male you can't use this)";
			$this->api->chat->sendTo("[Life]type /marriage for more info";
			break;

			case "life":
				$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
				switch($subcommands){
                                        case "growup":
                                        $amount = $args[2];
                                        if(!is_numeric($amount))
                                        {
                                        $output .= "[Life]Please insert number";
                                        }else{
                                        $grow = array(
                                        	$target => array(
                                        		'age' => $amount
                                        		)
                                       		);
                                       	$this->overwriteConfig($grow)
                                        break;
					case "choosemale":
					$chosen1 = $args[1];
					if(!is_numeric($chosen1))
					$male = array(
						$target => array(
							'gender' => $chosen1
							)
						);
					$this->overwriteConfig($male);
					$this->api->chat->broadcast("[Life]$issuer is now male.";
					}
                                        break;

					case "choosefemale":
					$chosen2 = $args[1];
					if(!is_numeric($chosen2))
					$female = array(
						$target => array(
							'gender' => $chosen2
							)
						);
					$this->overwriteConfig($female);
					$this->api->chat->broadcast("[Life]$issuer is now female.";
					}
                                        break;
				}
			case "marriage":
			$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
				switch($subcommands)
					case "propose":
                                        $target = $args[1];
                                        $player = $issuer->username;
                                        if($target === $player)
                                        {
						$output .= "[Life]You can't propose to your self.";
					}

					if(!$this->api->player->get($target))
					{
						$output .= "[Life]$target is not in the server right now.";
					}

                                        if($playerage <= 19 === false)
                                        {
                                               $marriage = array(
						$target => array(
							'married' => $issuer
							'married1' => $target
							)
						);
					$this->overwriteConfig($marriage);
					$output .= "[Life]If $target refuses (or decline) to marry you, you cannout marry $target.";
                                        }
                                        break;

                                        case "decline":
                                        if($issuer instaceof player)
                                        {
                                        	$marriage1 = array(
                                        		$target => array(
                                        			'married' => SOLO
                                        			'married1' => SOLO
                                        			)
                                       			);
						$this->overwriteConfig($marriage1);
					$output .= "[Life]You declined to the propose.";
                                        }
                                        break;
                                        
                                        case "devorce":
                                        if($issuer instaceof player)
                                        {
                                        	$marriage2 = array(
                                        		$target => array(
                                        			'married' => SOLO
                                        			'married1' => SOLO
                                        			)
                                       			);
						$this->overwriteConfig($marriage2);
					$output .= "[Life]You devorced with your partner.";
                                        }
                                        break;

                                        case "baby":
                                        if($issuer = $chosen2)
                                        {
		$npcplayer = new Player("0", "127.0.0.1", 0, 0);
		$npcplayer->spawned = true;
		$playerClassReflection = new ReflectionClass(get_class($npcplayer));
		$usernameField = $playerClassReflection->getProperty("username");
		$usernameField->setAccessible(true);
		$usernameField->setValue($npcplayer, $npcname);
		$iusernameField = $playerClassReflection->getProperty("iusername");
		$iusernameField->setAccessible(true);
		$iusernameField->setValue($npcplayer, strtolower($npcname));
		$timeoutField = $playerClassReflection->getProperty("timeout");
		$timeoutField->setAccessible(true);
		$timeoutField->setValue($npcplayer, PHP_INT_MAX - 0xff);
		$entityit = $this->api->entity->add($this->api->level->getDefault(), ENTITY_PLAYER, 0, array(
			"x" => $location->x,
			"y" => $location->y,
			"z" => $location->z,
			"Health" => 20,
			"player" => $npcplayer,
		));
		$entityit->setName($npcname);
		$this->api->entity->spawnToAll($this->api->level->getDefault(), $entityit->eid);
		$npcplayer->entity = $entityit;
		array_push($this->npclist, $npcplayer);
		$this->config->get("npcs")[$npcname] = array(
			"Pos" => array(
				0 => $location->x,
				1 => $location->y,
				2 => $location->z,
			),
		);
		                                      }
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