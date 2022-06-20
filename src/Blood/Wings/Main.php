<?php

namespace Blood\Wings;

use Blood\Wings\saveSkin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\plugin\PluginBase;
use Blood\Wings\Libs\jojoe77777\FormAPI\SimpleForm;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {
	
	/** @var self $instance */
    public static $instance;
    
    /** @var int*/
    public $json;
	
	public function onEnable() : void {
		self::$instance = $this;
    	$this->getServer()->getPluginManager()->registerEvents($this, $this);
    	$this->checkSkin();
    	$this->checkRequirement();
    	$this->getLogger()->info($this->json . " Geometry Skin Confirmed");
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		if($sender instanceof Player){
			switch(strtolower($cmd->getName())){
				case "cw":
                    $this->Form($sender, TextFormat::YELLOW . "Select Your Wings:");
                    return true;
                break;
				case "customwing":
				    $this->Form($sender, TextFormat::YELLOW . "Select Your Wings:");
				    return true;
				break;
                    }
		} else {
			$sender->sendMessage(TextFormat::RED . "You dont Have Permission to use this Command");
			return false;
		}
	}
	
	public function dataReceiveEv(DataPacketReceiveEvent $ev)
    {
        if ($ev->getPacket() instanceof LoginPacket) {
            $data = JwtUtils::parse($ev->getPacket()->clientDataJwt);
            $name = $data[1]["ThirdPartyName"];
            if ($data[1]["PersonaSkin"]) {             
                if (!file_exists($this->getDataFolder() . "saveskin")) {
                    mkdir($this->getDataFolder() . "saveskin", 0777);
                }
                copy($this->getDataFolder()."steve.png",$this->getDataFolder() . "saveskin/{$name}.png");
                return;
            }
            if ($data[1]["SkinImageHeight"] == 32) {           
            }
            $saveSkin = new SaveSkin();
            $saveSkin->saveSkin(base64_decode($data[1]["SkinData"], true), $name);
        }
    }
    
    public function onQuit(PlayerQuitEvent $ev)
    {
        $name = $ev->getPlayer()->getName();

        $willDelete = $this->getConfig()->getNested('DeleteSkinAfterQuitting');
        if ($willDelete) {
            if (file_exists($this->getDataFolder() . "saveskin/{$name}.png")) {
                unlink($this->getDataFolder() . "saveskin/{$name}.png");
            }
        }
    }
    
    public function Form($sender, string $txt){
    	$form = new SimpleForm(function (Player $sender, $data = null){
    		if($data === null){
    			return false;
    		}
    		switch($data){
    			case 0:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "kagune");
    			break;
    			case 1:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "kakuja");
    			break;
    			case 2:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "mercy");
    			break;
    			case 3:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "balrog");
    			break;
    			case 4:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "blazingelectro");
    			break;
    			case 5:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "darkaurora");
    			break;
    			case 6:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "davinci");
    			break;
    			case 7:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "devil");
    			break;
    			case 8:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "diamond");
    			break;
    			case 9:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "legendarydragonknight");
    			break;
    			case 10:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "monarchbutterfly");
    			break;
    			case 11:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "razor");
    			break;
    			case 12:
    			    $setskin = new setSkin();
    			    $setskin->setSkin($sender, "robotic");
    			break;
    			case 13:
    			  $this->resetSkin($sender);
    			break;
    			case 14:
    			break;
    		}
    	});
    	$form->setTitle(TextFormat::RED . "Custom" . TextFormat::WHITE . "Wing");
    	$form->setContent($txt);
    	$form->addButton("§cKagune §4Kaneki");
    	$form->addButton("§0Kakuja §4Kaneki");
    	$form->addButton("§6Mercy §awing");
    	$form->addButton("§cBalrog §awing");
    	$form->addButton("§eBlazing §fElectro §awing");
    	$form->addButton("§5Dark §dPurple §awing");
    	$form->addButton("§6Da§fVinci §awing");
    	$form->addButton("§4Devil §awing");
    	$form->addButton("§bDiamond §awing");
    	$form->addButton("§cLegendary §4Dragon §fKnight");
    	$form->addButton("§bMonarch §cButter§dfly §awing");
    	$form->addButton("§eRazor §awing");
    	$form->addButton("§bRobo§3tic §awing");
    	$form->addButton("Reset Skin");
    	$form->addButton("Exit");
    	$form->sendToPlayer($sender);
    	return $form;
    }
    
    public function resetSkin(Player $player){
      $player->sendMessage("Reset To Original Skin Successfully");
      $reset = new resetSkin();
      $reset->setSkin($player);
    }
    
    public function checkSkin(){
      $Available = [];
      if(!file_exists($this->getDataFolder() . "skin")){
        mkdir($this->getDataFolder() . "skin");
      }
      $path = $this->getDataFolder() . "skin/";
      $allskin = scandir($path);
      foreach($allskin as $file){
          array_push($Available, preg_replace("/.json/", "", $file));
      }
      foreach($Available as $value){
        if(!in_array($value . ".png", $allskin)){
          unset($Available[array_search($value, $Available)]);
        }
      }
      $this->json = count($Available);
      $Available = [];
    }
    
    public function checkRequirement(){
      if(!extension_loaded("gd")){
        $this->getServer()->getLogger()->info("§6Clothes: Uncomment gd2.dll (remove symbol ';' in ';extension=php_gd2.dll') in bin/php/php.ini to make the plugin working");
        $this->getServer()->getPluginManager()->disablePlugin($this);
      }
      if(!class_exists(SimpleForm::class)){
        $this->getServer()->getLogger()->info("§6Clothes: FormAPI class missing,pls use .phar from poggit!");
        $this->getServer()->getPluginManager()->disablePlugin($this);
        return;
      }
      if (!file_exists($this->getDataFolder() . "steve.png") || !file_exists($this->getDataFolder() . "steve.json") || !file_exists($this->getDataFolder() . "config.yml")) {
            if (file_exists(str_replace("config.yml", "", $this->getResources()["config.yml"]))) {
                $this->recurse_copy(str_replace("config.yml", "", $this->getResources()["config.yml"]), $this->getDataFolder());
            } else {
                $this->getServer()->getLogger()->info("§6Clothes: Something wrong with the resources");
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return;
            }
      }
    }
    
    public function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
