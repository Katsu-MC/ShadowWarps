<?php

declare(strict_types=1);

namespace Katsu\ShadowWarps\Forms;

use pocketmine\player\Player;
use pocketmine\Server;

use jojoe77777\FormAPI\SimpleForm;
use Katsu\ShadowWarps\WarpDelay;
use Katsu\ShadowWarps\API\WarpAPI;

class WarpForms
{
    private $warpAPI;

    public function __construct(WarpAPI $warpAPI)
    {
        $this->warpAPI = $warpAPI;
    }

    public function warpForm(): SimpleForm
    {
        $form = new SimpleForm(function (Player $player, string $data = null) {
            if ($data === null) return;

            $name = explode(":", WarpDelay::getConfigValue("warp_cmd"))[0];
            Server::getInstance()->getCommandMap()->dispatch($player, "$name $data");
        });
        $form->setTitle(WarpDelay::getConfigValue("title"));
        $form->setContent(WarpDelay::getConfigValue("content"));
        foreach ($this->warpAPI->getAllWarps() as $warp) {
            $form->addButton(WarpDelay::getConfigReplace("button", "{warp}", $warp), -1, "", $warp);
        }
        return $form;
    }
}
