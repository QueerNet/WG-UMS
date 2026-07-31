<?php

namespace VPN;

trait Wg
{
    public function wg_list_devices(string $UUID) {
        $query = "SELECT devid FROM WG WHERE userid = $UUID;";
        $result = $this->db->select($query);
        return $result;
    }

    public function wg_check(string $TBL_NAME, $allowedip, $devid) {
        // Check database for this device PrivateIP
        $IP = FALSE; // If IP is duplicated
        $NAME = FALSE; // If device name is duplicated
        $query = "SELECT * FROM WG WHERE AllowedIPs = '$allowedip' OR devid = '$devid';";
        $result = $this->db->select($query);
        foreach ($result as $device) {
            $ipcheck = $device['AllowedIPs'] == $allowedip;
            $devcheck = $device['devid'] == $devid;
            if ($ipcheck || $devcheck) {
                $IP = $IP || $ipcheck; // If IP was TRUE in previous iter OR now
                $NAME = $NAME || $devcheck; // If IP was TRUE in previous iter OR now
            }
        }
        return [$IP, $NAME];
    }

    public function wg_add_peer(string $iface, string $privkey, string $pubkey, string $psk, string $allowedIp): bool
    {
        // Before calling this function, generate keys in browser and pull them here with superglobal _POST
        // Look for existing/repeated user (wg_check, above)
        // Add user to wireguard
        // wg set wg0 listen-port 51820 private-key /path/to/private-key peer ABCDEF... allowed-ips 192.168.88.0/24 endpoint 209.202.254.14:8172
        // If user was added successfully, add them to the database in the WG table
        // If database addition successful, add keys to secrets store
        // If any errors happen, catch the exception and clean up partially added device.
        // If success, return TRUE. If exceptions, return FALSE.
    }

    public function wg_rm_peer(string $iface, string $allowedIp): bool
    {
        // Before calling this function, generate keys in browser and pull them here with superglobal _POST
        // Look for existing/repeated user (wg_check, above)
        // Add user to wireguard
        // wg set wg0 listen-port 51820 private-key /path/to/private-key peer ABCDEF... allowed-ips 192.168.88.0/24 endpoint 209.202.254.14:8172
        // If user was added successfully, add them to the database in the WG table
        // If database addition successful, add keys to secrets store
        // If any errors happen, catch the exception and clean up partially added device.
        // If success, return TRUE. If exceptions, return FALSE.
    }
}
