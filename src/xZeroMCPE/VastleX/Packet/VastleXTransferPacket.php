<?php


namespace xZeroMCPE\VastleX\Packet;


use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\DataPacket;

class VastleXTransferPacket extends DataPacket
{

    public const NETWORK_ID = Packet::TRANSFER;

    public $host;
    public $port;
    public $message;
    public $hideMessage;

    public function encodePayload() : void
    {
        $this->putString($this->host);
        $this->putVarInt($this->port);
        $this->putString($this->message);
        $this->putBool($this->hideMessage);
        $this->isEncoded = true;
    }

    public function handle(NetworkSession $session): bool
    {
       return true;
    }
}