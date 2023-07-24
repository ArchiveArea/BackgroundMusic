<?php

declare(strict_types=1);

namespace NhanAZ\BackgroundMusic\task;

use NhanAZ\BackgroundMusic\Main;
use NhanAZ\libBedrock\ResourcePackManager;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use pocketmine\utils\InternetRequestResult;

class DownloadTask extends AsyncTask {

    public function __construct(
        private string $url,
        private string $path,
        private bool $finish
    ) {}


    /**
     * @return void
     */
    public function onRun(): void {
        $this->setResult(Internet::getURL($this->url));
    }

    public function onCompletion(): void {
        if (!$this->getResult() instanceof InternetRequestResult) {
            return;
        }
        $content = $this->getResult()->getBody();
        file_put_contents($this->path, $content);
        if ($this->finish) {
            Server::getInstance()->getLogger()->debug("Downloaded BackgroundMusic Pack!");
            ResourcePackManager::registerResourcePack(Main::getInstance());
        }
    }
}