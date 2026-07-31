<?php

namespace VPN;

use Exception;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Trait Generate
 *
 * This trait provides a set of functions for generating keys and IP addresses.
 */
trait Generate
{
    /**
     * Generates a private key, public key, and preshared key for WireGuard.
     *
     * @return array Array with the private key, public key, and preshared key.
     */
    #[ArrayShape([
        'PrivateKey' => 'string',
        'PublicKey' => 'string',
        'PresharedKey' => 'string',
    ])]
    public static function generate_keys(): array
    {
        // Generate a private key using the wg genkey command
        $PrivateKey = str_replace("\n", "", shell_exec('wg genkey'));
        // Generate a public key from the private key using the wg pubkey command
        $PublicKey = str_replace("\n", "", shell_exec('echo ' . $PrivateKey . ' | wg pubkey'));
        // Generate a preshared key using the wg genpsk command
        $PresharedKey = str_replace("\n", "", shell_exec('wg genpsk'));

        return [
            'PrivateKey' => $PrivateKey,
            'PublicKey' => $PublicKey,
            'PresharedKey' => $PresharedKey,
        ];
    }

    /**
     * Generates an IP address for a new client based on a given template and a list of already-used IP addresses.
     *
     * @param array $IPs Array of already-used IP addresses.
     * @param string $Template Template for generating the IP address. Defaults to '10.66.66.x'.
     * @return string The generated IP address.
     * @throws Exception If the maximum number of clients has been reached.
     */
    public static function generate_ip(array $IPs, string $Template = '10.66.66.x'): string
    {
        // Initialize the variable to hold the generated address
        $Address = null;

        // Check each possible IP address in the range from 2 to 254
        for ($i = 2; $i < 255; $i++) {
            // Filter the list of IP addresses, excluding addresses that are already used or don't match the template
            $Client = array_filter($IPs, function ($IP) use ($Template, $i) {
                return $IP === str_replace('x', $i, $Template) || !str_starts_with($IP, str_replace('x', '', $Template));
            });

            // If this address is not yet in the list, use it
            if (!$Client) {
                $Address = str_replace("x", $i, $Template);
                break;
            }
        }

        // If all addresses are already taken, throw an exception
        if (!$Address) {
            throw new Exception("Maximum number of clients reached.");
        }

        return $Address;
    }
}
