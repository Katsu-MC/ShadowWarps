<?php

declare(strict_types=1);

namespace Katsu\ShadowWarps\Commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;

use Katsu\ShadowWarps\WarpDelay;
use Katsu\ShadowWarps\Forms\WarpForms;
use Katsu\ShadowWarps\Task\TeleportationTask;
use Katsu\ShadowWarps\API\WarpAPI;

class Warp extends Command implements PluginOwned
{
    private $plugin;
    private $warpAPI;

    public function __construct(WarpDelay $plugin, WarpAPI $warpAPI)
    {
        $command = explode(":", WarpDelay::getConfigValue("warp_cmd"));
        parent::__construct($command[0]);
        if (isset($command[1])) $this->setDescription($command[1]);
        $this->setAliases(WarpDelay::getConfigValue("warp_aliases"));
        $this->setPermission("shadowwarps.cmd.warp");
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
            $command = explode(":", WarpDelay::getConfigValue("warp_cmd"));
            if ((isset($command[2])) and (WarpDelay::hasPermissionPlayer($sender, $command[2]))) return;
            if ((isset($args[0])) and ($this->warpAPI->existWarp($args[0]))) {
                if (($sender->hasPermission("shadowwarps.cmd.warps"))) {
                    $sender->teleport($this->warpAPI->getWarp($args[0]));
                    $sender->sendMessage(WarpDelay::getConfigReplace("warp_msg_teleport"));
                } else {
                    $sender->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * (WarpDelay::getConfigValue("delay") + 2), 10));
                    new TeleportationTask($sender, $args[0]);
                }
            } else {
                if (WarpDelay::getConfigValue("form")) {
                    $warpForms = new WarpForms($this->warpAPI);
                    $sender->sendForm($warpForms->warpForm());
                    return;
                }

                $warps = implode(", ", $this->warpAPI->getAllWarps());
                $sender->sendMessage(WarpDelay::getConfigReplace("warp_msg_list", ["{warp}"], [$warps]));
            }
        }
    }
}
