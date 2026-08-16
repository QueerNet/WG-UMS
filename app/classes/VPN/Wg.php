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
        $stmt = $this->db->prepare("SELECT * FROM WG WHERE userid = ?");
        $stmt->execute([$UUID]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function wg_check_ip(string $allowedip): array
    {
        $IP = false;   // true if this IP is already in use
        
        // Begin PDO transaction
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }

        // Check for duplicate IPs or devices
        $stmt = $this->db->prepare("SELECT AllowedIPs FROM WG WHERE (AllowedIPs = ? AND active=TRUE)");
        $stmt->execute([$allowedip]);

        // For each result in net, resolve to two booleans (IP duped, NAME, duped)
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $device) {
            if ($device['AllowedIPs'] === $allowedip) {
                $IP = $IP || true;
            }
        }
        // Return boolean array from dupe check
        $result = ['success' => true, 'data' => $IP];
        return $result;
    }


    public function wg_check_name(int $userid, string $devname): array
    {
        $NAME = false;   // true if this IP is already in use
        
        // Begin PDO transaction
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }

        // Check for duplicate devnames
        $stmt = $this->db->prepare("SELECT devname FROM WG WHERE (userid = ? AND devname = ?)");
        $stmt->execute([$userid, $devname]);

        // For each result in net, resolve to two booleans (IP duped, NAME, duped)
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $device) {
            if ($device['devname'] === $devname) {
                $NAME = $NAME || true;
            }
        }
        // Return boolean array from dupe check
        $result = ['success' => true, 'data' => $NAME];
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

            $dupe = $this->wg_check_name($userid, $devname);

            if ($dupe['data']) {
                return ['success', false, 'error', 11];
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

        // Get corresponding name and userid
        $stmt = $this->db->prepare("SELECT devname, userid FROM WG WHERE id = ?");
        $stmt->execute([$devid]);
        $dev_details = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        

        // Look for existing/repeated user (wg_check, above)
        $dupe_check_ip = $this->wg_check_ip($allowedIp, $devid);
        //$dupe_check_name = $this->wg_check_name($dev_details[0]['userid'], $dev_details[0]['devname']);
        $ip_dupe = $dupe_check_ip['data'];

        if ($ip_dupe) {
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

    public function wg_rename(int $devid, string $newname): array
    {
        $newname = preg_replace('/[^a-zA-Z0-9 ]/', '', $newname);
        try {
            // Begin PDO transaction
            $startedTransaction = false;
            if (!$this->db->inTransaction()) {
                $this->db->beginTransaction();
                $startedTransaction = true;
            }

            // Update name in DB
            $insert = $this->db->prepare("UPDATE WG SET devname = ? WHERE id= ? ;");
            $insert->execute([$newname, $devid]);
            $this->db->commit();
            $result = ['success', TRUE, 'data', $newname];
        } catch (\PDOException $e) {
            $result = ['success', FALSE, 'error', $stderr];
        }
        return $result;
    }
}