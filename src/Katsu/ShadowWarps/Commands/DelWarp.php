<?php

declare(strict_types=1);

namespace Katsu\ShadowWarps\Commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;

use Katsu\ShadowWarps\API\WarpAPI;
use Katsu\ShadowWarps\WarpDelay;

class DelWarp extends Command implements PluginOwned
{
    private $plugin;
    private $warpAPI;

    public function __construct(WarpDelay $plugin, WarpAPI $warpAPI)
    {
        $command = explode(":", WarpDelay::getConfigValue("delwarp_cmd"));
        parent::__construct($command[0]);
        if (isset($command[1])) $this->setDescription($command[1]);
        $this->setAliases(WarpDelay::getConfigValue("delwarp_aliases"));
        $this->setPermission("shadowwarps.cmd.delwarp");
        $this->plugin = $plugin;
        $this->warpAPI = $warpAPI;
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $command = explode(":", WarpDelay::getConfigValue("delwarp_cmd"));
            if ((isset($command[2])) and (WarpDelay::hasPermissionPlayer($sender, $command[2]))) return;
            if (isset($args[0])) {
                if ($this->warpAPI->existWarp($args[0])) {
                    $this->warpAPI->delWarp($args[0]);
                    $sender->sendMessage(WarpDelay::getConfigReplace("delwarp_good"));
                } else $sender->sendMessage(WarpDelay::getConfigReplace("delwarp_msg_no_exist"));
            } else $sender->sendMessage(WarpDelay::getConfigReplace("delwarp_on_warp"));
        }
    }
}
