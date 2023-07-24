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


    /**
     * @return void
     */
    public function onRun(): void {
        $this->setResult(Internet::getURL(Main::PACK_URL));
    }

    public function onCompletion(): void {
        if (!$this->getResult() instanceof InternetRequestResult) {
            return;
        }
        $content = $this->getResult()->getBody();
        file_put_contents(RESOURCE_PACK_PATH . ".zip", $content);
        Server::getInstance()->getLogger()->debug("Downloaded BackgroundMusic Pack!");
        $zip = new \ZipArchive();
        $zip->open(RESOURCE_PACK_PATH . ".zip");
        $zip->extractTo(RESOURCE_PACK_PATH);
        $zip->close();
        @unlink(RESOURCE_PACK_PATH . ".zip");
        Server::getInstance()->getLogger()->debug("Extracted BackgroundMusic Pack!");
        ResourcePackManager::registerResourcePack(Main::getInstance());
    }
}