<?php


namespace xZeroMCPE\VastleX;


use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class VastleXListener implements Listener
{

    public function onPacket(DataPacketReceiveEvent $event): void
    {

        $player =  $event->getPlayer();
        $packet = $event->getPacket();

        if($packet instanceof LoginPacket) {
            if(VastleX::getInstance()->getSecret() === '') {
                $this->handle($player, $packet);
            } else {
                if($packet->clientData['ThirdPartyName'] !== VastleX::getInstance()->getSecret()) {
                    VastleX::getInstance()->getLogger()->error(TextFormat::LIGHT_PURPLE . $player->getAddress() . ":" . $player->getPort() . " tried to login with an incorrect secret: " . TextFormat::LIGHT_PURPLE . $packet->clientData['ThirdPartyName']);
                    $player->kick(VastleX::getInstance()->getConfig()->getNested("messages.disconnect-proxy"), false);
                } else {
                    $this->handle($player, $packet);
                }
            }
        }
    }

    public function handle(Player $player, LoginPacket $packet): void
    {

        /*
         * Update their XUID with the one given by VastleX
         */
        $reflection = new \ReflectionClass($player);
        $reflec = $reflection->getProperty("xuid");
        $reflec->setAccessible(true);
        $reflec->setValue($player, $packet->clientData['PlatformOnlineId']);

        /*
         * Now continue on updating their IP ADDRESS to their real one provided by VastleX
         */


            $prop = $reflection->getProperty("ip");
            $prop->setAccessible(true);
            $prop->setValue($player, explode(":", $packet->clientData["ServerAddress"])[0]);

        VastleX::getInstance()->getLogger()->info(TextFormat::LIGHT_PURPLE . $player->getAddress() . ":" . $player->getPort() . TextFormat::GREEN . " passed secret verification");
    }
}