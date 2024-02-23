<?php

declare(strict_types=1);

namespace Katsu\ShadowWarps\Task;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\player\Player;

use Katsu\ShadowWarps\WarpDelay;
use Katsu\ShadowWarps\API\WarpAPI;

class TeleportationTask extends Task
{
    private $startPosition;
    private $player;
    private $warp;
    private $timer;

    public function __construct(Player $player, string $warp)
    {
        $this->warp = $warp;
        $this->player = $player;
        $this->startPosition = $player->getPosition();
        $this->timer = WarpDelay::getConfigValue("delay");
        WarpDelay::getInstance()->getScheduler()->scheduleDelayedRepeatingTask($this, 20, 20);
    }

    public function onRun(int $currentTick): void
    {
        $player = $this->player;
        if (!$player->isOnline()) {
            $this->getHandler()->cancel();
            return;
        }

        if ($player->getPosition()->getFloorX() === $this->startPosition->getFloorX() &&
            $player->getPosition()->getFloorY() === $this->startPosition->getFloorY() &&
            $player->getPosition()->getFloorZ() === $this->startPosition->getFloorZ()) {
            $player->sendTip(WarpDelay::getConfigReplace("warp_msg_cooldown", ["{time}"], [$this->timer]));
            $this->timer--;
        } else {
            $player->sendMessage(WarpDelay::getConfigReplace("warp_msg_cancel"));
            $player->getEffects()->remove(VanillaEffects::BLINDNESS());
            $this->getHandler()->cancel();
            return;
        }

        if ($this->timer === 0) {
            $player->getEffects()->remove(VanillaEffects::BLINDNESS());
            $player->teleport(WarpAPI::getWarp($this->warp));
            $player->sendTip(WarpDelay::getConfigReplace("warp_msg_teleport"));
            $this->getHandler()->cancel();
            return;
        }
    }
}
