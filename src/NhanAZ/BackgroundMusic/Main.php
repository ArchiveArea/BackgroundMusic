<?php

declare(strict_types=1);

namespace NhanAZ\BackgroundMusic;

use NhanAZ\BackgroundMusic\task\DownloadTask;
use NhanAZ\BackgroundMusic\task\GetInfoTask;
use NhanAZ\libBedrock\ResourcePackManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;

class Main extends PluginBase implements Listener {

	use SingletonTrait;

    const PACK_URL = "https://api.github.com/repos/jacob3105/BackgroundMusic/contents/BackgroundMusic%20Pack";

	protected function onLoad(): void {
		self::setInstance($this);
		define("RESOURCE_PACK_PATH", Path::join($this->getFile(), "resources", "BackgroundMusic Pack"));
		if (!is_dir(RESOURCE_PACK_PATH)) {
			@mkdir(RESOURCE_PACK_PATH);
			$this->getLogger()->info("Downloading BackgroundMusic Pack...");
			$this->getServer()->getAsyncPool()->submitTask(new GetInfoTask(self::PACK_URL));
		} else {
			try {
				ResourcePackManager::registerResourcePack($this);
			} catch (\Exception) {
				@rmdir(RESOURCE_PACK_PATH);
			}
		}
	}

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	protected function onDisable(): void {
		try {
			ResourcePackManager::unRegisterResourcePack($this);
		} catch (\Exception) {
		}
	}

	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$playerPos = $player->getPosition();
		$packet = PlaySoundPacket::create(
			soundName: "C418Sweden",
			x: $playerPos->getX(),
			y: $playerPos->getY(),
			z: $playerPos->getZ(),
			volume: 1.0,
			pitch: 1.0
		);
		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($player, $packet): void {
			if ($player->isOnline()) {
				$player->getNetworkSession()->sendDataPacket($packet);
			}
		}), 4400);
		/** 3m40s = 220s * 20 tick =  4400 tick */
	}
}
