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
		if(is_dir("./plugins/Marriage/".$this->api->getProperty("level-name")) === false){
			mkdir("./plugins/Marriage/".$this->api->getProperty("level-name"), 0777, true);
		}
		$this->createConfig($this, array(
		//여기에부부목록이랑아기목록써놓도록할수있게... 
                ));

		$this->api->console->register("marriage", "command that covers most of the marriage", array($this, "handleCommand"));
                $this->api->console->alias("accept", "marriage");
                $this->api->console->alias("decline", "marriage");
                $this->api->console->alias("devorce", "marriage");
		$this->api->console->alias("baby", "marriage");
	}
	
	public function __destruct(){
	
	}
	
	public function handleCommand($cmd, $arg){
		switch($cmd){
			case "marriage":
				break;
				/*


                                돈핸들러사용법
                                $this->api->handle("money.handle", $d);


				*/
		}
	}

}