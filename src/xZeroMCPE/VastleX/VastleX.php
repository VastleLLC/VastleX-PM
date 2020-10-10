<?php


namespace xZeroMCPE\VastleX;


use pocketmine\network\mcpe\protocol\ScriptCustomEventPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use xZeroMCPE\VastleX\Packet\VastleXTransferPacket;

class VastleX extends PluginBase
{

    public $config;
    public $secret = "";

    public static $instance;

    public function onEnable()
    {
        self::$instance = $this;

        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        if ((strlen(((string)$this->getConfig()->getNested("proxy.secret"))) == 0)) {
            $this->getLogger()->alert("You've disabled secret verification, which is pretty much unsafe.... unless you know what you're doing?");
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
    public function transfer(Player $player, string $address, int $port, string $message = "Transferring you to another server...")
    {
        $pk = new VastleXTransferPacket();
        $pk->host = $address;
        $pk->port = $port;
        $pk->message = $message;
        $pk->hideMessage = strlen($message) !== false;
        $player->sendDataPacket($pk);

        //Close their connection after sending them the transfer packet
        $player->close("Transferring", $message, strlen($message) !== 0);
    }
}