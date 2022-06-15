<?php

declare(strict_types=1);

namespace NhanAZ\BackgroundMusic;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use NhanAZ\libRegRsp\libRegRsp;

class Main extends PluginBase implements Listener {

	private libRegRsp $libRegRsp;

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->libRegRsp = new libRegRsp($this);
		$this->libRegRsp->regRsp();
	}

	protected function onDisable(): void {
		$this->libRegRsp->unregRsp();
	}

	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$playerPos = $player->getPosition();
		$packet = PlaySoundPacket::create(
			/** string $soundName */
			"C418Sweden",
			/** float $x */
			$playerPos->getX(),
			/** float $y */
			$playerPos->getY(),
			/** float $z */
			$playerPos->getZ(),
			/** float $volume */
			1.0,
			/** float $pitch */
			1.0
		);
		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($player, $packet): void {
			if ($player->isOnline()) {
				$player->getNetworkSession()->sendDataPacket($packet);
			}
		}), 4400);
		/** 3m40s = 220s * 20 tick =  4400 tick */
	}
}
