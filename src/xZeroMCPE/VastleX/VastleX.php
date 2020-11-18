<?php


namespace xZeroMCPE\VastleX;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ScriptCustomEventPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use xZeroMCPE\VastleX\Packet\VastleXTransferPacket;

class VastleX extends PluginBase
{

    public Config $config;
    public string $secret = "";

    public static VastleX $instance;

    public function onEnable()
    {
        self::$instance = $this;

        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        $errors = [];

        if ((((string)$this->getConfig()->getNested("proxy.secret")) === '')) {
            $this->getLogger()->alert("You've disabled secret verification, which is pretty much unsafe.... unless you know what you're doing?");
        }
        if($this->getServer()->getOnlineMode()) {
            $this->getLogger()->alert("Xbox Live verification is turned on, please disable it (through server.properties) or players will be kicked upon join. Be advised that VastleX handles authentication, so no reason to have it on.");
        }
        $this->secret = (string)$this->getConfig()->getNested("proxy.secret");
        $this->getServer()->getPluginManager()->registerEvents(new VastleXListener(), $this);
    }

    /**
     * @return VastleX
     */
    public static function getInstance(): VastleX
    {
        return self::$instance;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    /*
     * Transfer the player to the given server address/port
     */
    public function transfer(Player $player, string $address, int $port, string $message = "Transferring you to another server..."): void
    {
        $pk = new VastleXTransferPacket();
        $pk->host = $address;
        $pk->port = $port;
        $player->sendDataPacket($pk);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if(!$sender->isOp()) {
            $sender->sendMessage(TextFormat::RED . "You don't have permission to do that!");
        } else if(count($args) >= 3) {
            if(($player = Server::getInstance()->getPlayer($args[0])) !== null) {
                $sender->sendMessage("Attempting to transfer " . $player->getName() . " to {$args[1]}:{$args[2]}");
                self::getInstance()->transfer($player, $args[1], $args[2]);
            } else {
                $sender->sendMessage(TextFormat::RED . "We can't find anyone that goes by " . $args[0]);
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "You either didn't supply the required arguments\n /transfer player-name address port");
        }
        return true;
    }
}