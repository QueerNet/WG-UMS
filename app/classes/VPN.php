<?php

/**
 * https://github.com/localzet-dev/Wireguard-Manager-PHP
 */


use VPN\{Configurator, Wg};

class VPN
{
    use Configurator, Wg;

    private $db;
    private $fm;

    public function __construct()
    {
        $this->db = new Database();
        $this->fm = new Format(); // note: app\helpers\Format, not VPN\Format — see note below
    }
}