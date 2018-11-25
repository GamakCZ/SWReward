<?php

declare(strict_types=1);

namespace skywars;

use onebone\economyapi\EconomyAPI;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\utils\Config;
use skywars\event\PlayerArenaWinEvent;

/**
 * Class SWReward
 * @package skywars
 */
class SWReward extends PluginBase implements Listener {

    /** @var Config $config */
    public $config;

    /** @var EconomyAPI $economyAPI */
    public $economyAPI;

    public function onEnable() {
        if(!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        if(!is_file($this->getDataFolder() . "/config.yml")) {
            $this->saveResource("/config.yml");
        }

        $this->config = (new Config($this->getDataFolder() . "/config.yml", Config::YAML))->getAll(false);

        if(!class_exists(EconomyAPI::class)) {
            throw new PluginException("Could not load EconomyAPI provider");
        }
        if(!class_exists(SkyWars::class)) {
            throw new PluginException("SkyWars plugin was not found!");
        }

        $this->economyAPI = EconomyAPI::getInstance();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param PlayerArenaWinEvent $event
     */
    public function onWin(PlayerArenaWinEvent $event) {
        $player = $event->getPlayer();

        $player->sendMessage((string) $this->config["message"]);
        EconomyAPI::getInstance()->addMoney($player, (int)$this->config["reward"]);
    }
}