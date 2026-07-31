<?php

namespace VPN;

/**
 * Trait ShadowSocks
 *
 * This trait provides a set of functions for managing a ShadowSocks server.
 * ShadowSocks is a secure Socks5 proxy designed to provide internet privacy.
 * @see https://en.wikipedia.org/wiki/Shadowsocks
 */
trait ShadowSocks
{
    /**
     * Starts routing traffic through ShadowSocks.
     *
     * @param string $WireGuard Name of the WireGuard interface.
     * @param int $WireGuardPort WireGuard port to redirect traffic for.
     */
    public static function ss_start_routing_traffic(string $WireGuard, int $WireGuardPort = 17968): void
    {
        // Create a new iptables chain named SHADOWSOCKS
        shell_exec('iptables -t nat -N SHADOWSOCKS');

        // Array of addresses that should not be proxied
        $returnAddresses = [
            "0.0.0.0/8",
            "10.0.0.0/8",
            "127.0.0.0/8",
            "169.254.0.0/16",
            "172.16.0.0/12",
            "192.168.0.0/16",
            "224.0.0.0/4",
            "240.0.0.0/4"
        ];

        // Add a RETURN rule for each address in the array
        foreach ($returnAddresses as $address) {
            shell_exec("iptables -t nat -A SHADOWSOCKS -d $address -j RETURN");
        }

        // Add rules to exclude certain ports from proxying
        shell_exec("iptables -t nat -A SHADOWSOCKS -p tcp --dport 22:1023 -j RETURN");
        shell_exec("iptables -t nat -A SHADOWSOCKS -p tcp --dport 1081:65535 -j RETURN");

        // Add a rule to redirect traffic through ShadowSocks
        shell_exec("iptables -t nat -A SHADOWSOCKS -p tcp --dport 80,443,53,$WireGuardPort -j REDIRECT --to-ports 1080");

        // Apply the SHADOWSOCKS chain to the WireGuard interface
        shell_exec("iptables -t nat -A PREROUTING -i $WireGuard -p tcp -j SHADOWSOCKS");
    }

    /**
     * Stops routing traffic through ShadowSocks.
     *
     * @param string $WireGuard Name of the WireGuard interface.
     */
    public static function ss_stop_routing_traffic(string $WireGuard): void
    {
        // Remove the rule applying the SHADOWSOCKS chain to the WireGuard interface
        shell_exec("iptables -t nat -D PREROUTING -i $WireGuard -p tcp -j SHADOWSOCKS");

        // Flush and delete the SHADOWSOCKS chain
        shell_exec('iptables -t nat -F SHADOWSOCKS');
        shell_exec('iptables -t nat -X SHADOWSOCKS');
    }

    /**
     * Saves the ShadowSocks configuration.
     *
     * @param string $server IP address of the ShadowSocks server.
     * @param int $server_port Port of the ShadowSocks server.
     * @param int $local_port Local port to listen on.
     * @param string $password Password for authenticating with the ShadowSocks server.
     * @param int $timeout Timeout in seconds.
     * @param string $method Encryption method.
     */
    public static function ss_save_config(
        string $server = '0.0.0.0',
        int    $server_port = 8388,
        int    $local_port = 8388,
        string $password = 'your_password',
        int    $timeout = 60,
        string $method = 'aes-256-cfb',
    ): void
    {
        // Build the configuration array
        $config = [
            'server' => $server,
            'server_port' => $server_port,
            'local_port' => $local_port,
            'password' => $password,
            'timeout' => $timeout,
            'method' => $method,
        ];

        // Save the configuration to a file
        self::ss_put_config(json_encode($config, JSON_PRETTY_PRINT));
    }

    /**
     * Returns the current ShadowSocks configuration.
     *
     * @return string Current ShadowSocks configuration in JSON format.
     */
    public static function ss_get_config(): string
    {
        // Read and return the contents of the configuration file
        return file_get_contents("/etc/shadowsocks-libev/config.json");
    }

    /**
     * Writes data to the ShadowSocks configuration file.
     *
     * @param mixed $data Data to write.
     * @return false|int Number of bytes written to the file, or false on failure.
     */
    public static function ss_put_config(mixed $data): false|int
    {
        // Write the data to the file and return the result
        return file_put_contents("/etc/shadowsocks-libev/config.json", $data, 0600);
    }

    /**
     * Starts the ShadowSocks service.
     */
    public static function ss_start(): void
    {
        // Enable and start the ShadowSocks service
        shell_exec('systemctl enable shadowsocks-libev-local@config.service');
        shell_exec('systemctl start shadowsocks-libev-local@config.service');
    }

    /**
     * Restarts the ShadowSocks service.
     */
    public static function ss_restart(): void
    {
        // Restart the ShadowSocks service
        shell_exec('systemctl restart shadowsocks-libev-local@config.service');
    }

    /**
     * Stops the ShadowSocks service.
     */
    public static function ss_stop(): void
    {
        // Stop and disable the ShadowSocks service
        shell_exec('systemctl stop shadowsocks-libev-local@config.service');
        shell_exec('systemctl disable shadowsocks-libev-local@config.service');
    }
}
