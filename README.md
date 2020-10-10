# VastleX-PM
A tool for the Not-so-great but powerful Minecraft: Bedrock Edition proxy!

# Configuration
```yaml
# This is your proxy secret
# This prevents other proxies players from connecting to your server
# Any player that connects with an unmatched secret will be kicked
proxy:
  # You can disable secret verification by leaving it empty,
  # WARNING: That put your server at-risk, since auth is disabled, any player can join as any name they like
  secret: "xZeroMCPE"

messages:
  disconnect-proxy: "Ouch man! something is wrong with your connection"
```

Be sure to update the `secret` to reflect your set VastleX proxy secret, it prevents other
proxy players from connecting, ultimately, you can disable it by leaving it empty.

Make sure you set `xbox-auth=off` inside the `server.properties` as VastleX handles authentication part (unless disabled),
leaving it on will kick players connecting from the VastleX proxy.

# API
Ensure you have installed this plugin on your server,
you may use the following methods to transfer a player
to another server within VastleX proxy
```php
         /*
         * Assuming $player is instance of the Players object, 127.0.0.1 & 19133 being the
         * the IP/Port you wish to transfer the player to
         */
        $vastlex = \xZeroMCPE\VastleX\VastleX::getInstance()->transfer($player, "127.0.0.1", 19133);
```
Please note that by default, it closes the connection as soon as the transfer takes place,
### Basic use
You can use the `/transfer <player name> <address> <port>` to transfer to any server within the proxy
The above command requires operator command.

### Custom Implementation ?
if you wish to use it in your custom implementation, without the use of the API,
you can just use the custom `VastleXTransferPacket`

An example would be:

```php
        $pk = new VastleXTransferPacket();
        $pk->host = "127.0.0.1";
        $pk->port = 19133;
        $pk->message = "Hey man, you're being transferred!";
        $pk->hideMessage = false; //shows the message
```

### Other stuff...
VastleX also overrides the `xuid` & the `ip` property to the player's correct xuid/ip, so everything 
that uses the xuid/ip should work as expected, unless there's a bug, which you're freely 
to report.