<?php

namespace VPN;

trait Wg
{

    public function wg_get_pubkey($iface): string
    {
        // Build call to script
        $cmd = ['sudo', '/usr/local/bin/wg-get-pubkey.sh', $iface];

        // Run process
        $process = proc_open($cmd, [
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ], $pipes);

        // Get result and clean up
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
        return trim($stdout);
    }
    public function wg_list_devices(string $UUID): array
    {
        // Begin PDO transaction
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }
        // TODO: check for orphaned device listings in SQL and prune
        $stmt = $this->db->prepare("SELECT id, devname, AllowedIPs FROM WG WHERE userid = ?");
        $stmt->execute([$UUID]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function wg_check(string $allowedip, int $devid): array
    {
        
        $IP = false;   // true if this IP is already in use
        $NAME = false; // true if this devid is already in use

        // Begin PDO transaction
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }

        // Check for duplicate IPs or devices
        $stmt = $this->db->prepare("SELECT AllowedIPs, id FROM WG WHERE (AllowedIPs = ? OR devname = ?) AND active=TRUE");
        $stmt->execute([$allowedip, $devid]);

        // For each result in net, resolve to two booleans (IP duped, NAME, duped)
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $device) {
            if ($device['AllowedIPs'] === $allowedip) {
                $IP = true;
            }
            if ($device['id'] === $devid) {
                $NAME = true;
            }
        }
        // Return boolean array from dupe check
        $result = ['success' => true, 'data' => [$IP, $NAME]];
        return $result;
    }

    public function wg_get_next_ip(string $userid, string $devname): array
    {
        // Default to error, then try
        $result = ['success' => false, 'error' => 'Unknown error'];

        try {
            // Begin PDO transaction
            $startedTransaction = false;
            if (!$this->db->inTransaction()) {
                $this->db->beginTransaction();
                $startedTransaction = true;
            }

            // Build query string
            $sql = "SELECT CONCAT('10.200.200.', 
                        MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(AllowedIPs, '/', 1), '.', -1) AS UNSIGNED)) + 1,
                        '/32'
                    ) AS next_ip
                    FROM WG
                    FOR UPDATE";

            // Load query
            $stmt = $this->db->query($sql);
            $nextIp = $stmt->fetchColumn() ?? '10.200.200.2/32';

            // Check out an inactive entry to reserve IP
            $insert = $this->db->prepare("INSERT INTO WG (userid, devname, AllowedIPs, active) VALUES (?, ?, ?, FALSE)");
            $insert->execute([$userid, $devname, $nextIp]);

            $this->db->commit();

            // Return reserved IP
            $result = ['success' => true, 'ip' => $nextIp];
        } catch (\PDOException $e) {
            // If an exception occured, rollback the changes
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // Return error
            $result = ['success' => false, 'error' => $e->getMessage()];
        }
        // Return result
        return $result;
    }



    function wg_add_peer(string $iface, string $pubkey, string $psk, string $allowedIp): array
    {
        // Begin PDO transaction
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }

        // Get device id
        $stmt = $this->db->prepare("SELECT id FROM WG WHERE AllowedIPs = ?");
        $stmt->execute([$allowedIp]);
        $devid = $stmt->fetchColumn();

        // Look for existing/repeated user (wg_check, above)
        $dupe_check = $this->wg_check($allowedIp, $devid);
        $ip_dupe = $dupe_check['data'][0];
        $name_dupe = $dupe_check['data'][1];
        $dupe = $ip_dupe && $name_dupe;
        if ($dupe) {
            $result = ['success' => FALSE, 'error' => "Entry is duplicate."];
            return $result;
        } else {
            // Build call to script
            $cmd = ['sudo', '/usr/local/bin/wg-add-peer.sh', $iface, $pubkey, $psk, $allowedIp];

            // Run process
            $process = proc_open($cmd, [
                1 => ['pipe', 'w'], // stdout
                2 => ['pipe', 'w'], // stderr
            ], $pipes);

            // Get result and clean up
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);

        }

        if (!$exitCode) {
            // If dev was added successfully, activate device in DB
            $insert = $this->db->prepare("UPDATE WG SET active = TRUE WHERE id= ? ;");
            $insert->execute([$devid]);
            $this->db->commit();

            // Return result
            $result = ['success', TRUE, 'data', 'Successfully added device'];
        } else {
            $result = ['success', FALSE, 'error', $stderr];
        }
        return $result;
    }

    public function wg_rm_peer(string $iface, int $devid): array
    {
        // Begin PDO transaction
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }

        // Get allowedIPs from devid
        $stmt = $this->db->prepare("SELECT AllowedIPs, active FROM WG WHERE id = ?");
        $stmt->execute([$devid]);

        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $allowedIp = $results[0]['AllowedIPs'];
        $active = $results[0]['active'];

        if (!$active) {
            // If dev was removed successfully, remove from DB
            $insert = $this->db->prepare("DELETE FROM WG WHERE id= ? ;");
            $insert->execute([$devid]);
            $this->db->commit();
            $result = ['success', TRUE, 'data', 'Successfully removed phantom device'];
            return $result;
        } else {

            // Build call to script
            $cmd = ['sudo', '/usr/local/bin/wg-rm-peer.sh', $iface, $allowedIp];

            // Run process
            $process = proc_open($cmd, [
                1 => ['pipe', 'w'], // stdout
                2 => ['pipe', 'w'], // stderr
            ], $pipes);

            // Get result and clean up
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);
        }

        if (!$exitCode) {
            // If dev was removed successfully, remove from DB
            $insert = $this->db->prepare("DELETE FROM WG WHERE id= ? ;");
            $insert->execute([$devid]);
            $this->db->commit();

            if (trim($stderr)=="IP not found") {
                $result = ['success', TRUE, 'data', 'Successfully removed phantom device'];
            } else {
                $result = ['success', TRUE, 'data', 'Successfully removed device'];
            }
        } else {
            $result = ['success', FALSE, 'error', $stderr];
        }
        return $result;
    }
}