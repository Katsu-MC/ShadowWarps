<?php

declare(strict_types=1);

namespace Katsu\ShadowWarps;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;

use Katsu\ShadowWarps\API\WarpAPI;
use Katsu\ShadowWarps\Commands\Warp;
use Katsu\ShadowWarps\Commands\DelWarp;
use Katsu\ShadowWarps\Commands\SetWarp;

class WarpDelay extends PluginBase
{
    private static $main;

    public function onEnable(): void
    {
        self::$main = $this;
        $this->saveDefaultConfig();

        $warpAPI = new WarpAPI();

        $this->getServer()->getCommandMap()->registerAll("ShadowWarps", [
            new DelWarp($this, $warpAPI),
            new SetWarp($this, $warpAPI),
            new Warp($this, $warpAPI)
        ]);
    }

    public static function getInstance(): self
    {
        return self::$main;
    }

    public function onDisable(): void
    {
        WarpAPI::getData()->save();
    }

    public static function getConfigReplace(string $path, array|string $replace = [], array|string $replacer = []): string
    {
        $config = self::$main->getConfig();
        $return = str_replace("{prefix}", $config->get("prefix"), $config->get($path));
        return str_replace($replace, $replacer, $return);
    }

    public static function hasPermissionPlayer(Player $player, string $perm): bool
    {
        if (self::$main->getServer()->isOp($player->getName())) return false;
        if ($player->hasPermission($perm)) return false;
        else {
            $player->sendMessage(self::getConfigReplace("no_perm"));
            return true;
        }
    }

    public static function getConfigValue(string $path): mixed
    {
        return self::$main->getConfig()->get($path);
    }
}
