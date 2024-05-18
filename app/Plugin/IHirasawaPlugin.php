<?php

namespace App\Plugin;

interface IHirasawaPlugin
{
    public function onEnable();
    public function onDisable();
    public function onInstall();
    public function onUpgrade(string $lastVersion);
    public function onUninstall();
}