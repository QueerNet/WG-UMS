### About
This API initiates a wg-quick setup but adds and removes peers using "wg set" commands for live updates without interrupting existing connections. Use the config.php file under app/classes/config/config.php to enter your instance's specific parameters.

PHPMailer is used and configuration parameters are exposed as described above.

#### Privacy statement
All wireguard keys are generated locally in the user's browser. These keys are never collected or stored by the server. Do bear in mind, though, that this project is still young and overall security is imperfect. Some security concerns that have already been accounted for:
- No plaintext passwords
- Strict access controls for VPN api
- Access controls for admin panel

# VPN API

Call originates from Javascript which sends POST request to api.php which parses and validates inputs and forwards the work to the corresponding function within Wg.php. Response is parsed into array and sent back to javascript for handling.

Javascript uses async await and database uses PDO for rollback support. Devices added to the database that fail to create new peers in WG, will be marked as inactive and may be pruned automatically (tbd).

# Bash

### Init
```bash
#!/bin/bash
# /usr/local/bin/wg-init.sh

set -e

WG_DIR="/etc/wireguard"
IFACE="QLS"
LISTEN_PORT=42069

umask 077
wg genkey | tee privkey | wg pubkey > pubkey
PRIVATE_KEY=$(cat privkey)
PUBLIC_KEY=$(cat pubkey)
echo "$PRIVATE_KEY" > "$WG_DIR/$IFACE.key"
echo "$PUBLIC_KEY" > "$WG_DIR/$IFACE.pub"
rm privkey pubkey

# Write the initial config — wg-quick will create/manage the interface from this
sudo tee "$WG_DIR/$IFACE.conf" > /dev/null <<EOF
[Interface]
PrivateKey = $PRIVATE_KEY
Address = 10.200.200.1/24
ListenPort = $LISTEN_PORT
SaveConfig = true

# Allow only HTTP/HTTPS between VPN peers
PostUp = iptables -A FORWARD -i $IFACE -o $IFACE -p tcp --dport 80  -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
PostUp = iptables -A FORWARD -i $IFACE -o $IFACE -p tcp --dport 443 -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
PostUp = iptables -A FORWARD -i $IFACE -o $IFACE -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
# Everything else peer-to-peer: explicit deny, not silent drop, so it's auditable
PostUp = iptables -A FORWARD -i $IFACE -o $IFACE -j REJECT --reject-with icmp-port-unreachable
# Peer <-> internet
PostUp = iptables -A FORWARD -i $IFACE -j ACCEPT
PostUp = iptables -A FORWARD -o $IFACE -j ACCEPT
# Masquerade
PostUp = iptables -t nat -I POSTROUTING 1 -s 10.200.200.0/24 -o ens3 -j MASQUERADE

PostDown = iptables -D FORWARD -i $IFACE -o $IFACE -p tcp --dport 80  -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
PostDown = iptables -D FORWARD -i $IFACE -o $IFACE -p tcp --dport 443 -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
PostDown = iptables -D FORWARD -i $IFACE -o $IFACE -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
PostDown = iptables -D FORWARD -i $IFACE -o $IFACE -j REJECT --reject-with icmp-port-unreachable
PostDown = iptables -D FORWARD -i $IFACE -j ACCEPT
PostDown = iptables -D FORWARD -o $IFACE -j ACCEPT
PostDown = iptables -t nat -D POSTROUTING -s 10.200.200.0/24 -o ens3 -j MASQUERADE
EOF

# Lock down conf
chmod 600 "$WG_DIR/$IFACE.conf"

# Create folder for PSK storage
mkdir -p /dev/shm/wireguard

# Enable daemon for managing WG
systemctl enable --now wg-quick@$IFACE
```

### Get pubkey
```bash
#!/usr/bin/env bash
# /usr/local/bin/wg-get-pubkey.sh
set -euo pipefail

IFACE="$1"
ALLOWED_IP="$2"

# Check interface name is whitelisted
case "$IFACE" in
    QLS|wg0|wg1) ;;  # add any other valid interface names here
    *) echo "bad interface"; exit 1 ;;
esac

# Basic IPv4/32 CIDR check — tighten to your actual subnet
[[ "$ALLOWED_IP" =~ ^10\.200\.200\.[0-9]{1,3}/32$ ]] || { echo "bad ip"; exit 1; }

PUBKEY=$(sudo wg show QLS dump | awk -v ip=$ALLOWED_IP '$4 == ip {print $1}')

# if PUBKEY is blank, this user wasn't real
if [[ -z "$PUBKEY" ]]; then
echo "IP not found" >&2; # log error
exit;

fi

sudo wg set "$IFACE" peer "$PUBKEY" remove

# Persist so it survives a reboot (writes current running state back to the conf file)
wg-quick save "$IFACE"

```

### Add peer
```bash
#!/usr/bin/env bash
# /usr/local/bin/wg-add-peer.sh
set -euo pipefail

IFACE="$1"
PUBKEY="$2"
PSK="$3"
ALLOWED_IP="$4"

PSK_PATH=$(mktemp /dev/shm/wireguard/psk.XXXXXX)
chmod 600 "$PSK_PATH"

echo "$PSK" > "$PSK_PATH"

WG_PUBKEY=$(wg show | sed -n -e 's/^.*public key: //p')

if [[ "$2" == "pubkey" ]]; then
echo "$WG_PUBKEY";
exit;
fi

# Check interface name is whitelisted
case "$IFACE" in
    QLS|wg0|wg1) ;;  # add any other valid interface names here
    *) echo "bad interface"; exit 1 ;;
esac

# WireGuard pubkeys: 44 base64 chars, ends in '='
[[ "$PUBKEY" =~ ^[A-Za-z0-9+/]{43}=$ ]] || { echo "bad pubkey"; exit 1; }

# Basic IPv4/32 CIDR check — tighten to your actual subnet
[[ "$ALLOWED_IP" =~ ^10\.200\.200\.[0-9]{1,3}/32$ ]] || { echo "bad ip"; exit 1; }

# Live-add the peer — no reload, no restart, no interruption to other peers
wg set "$IFACE" peer "$PUBKEY" allowed-ips "$ALLOWED_IP" preshared-key "$PSK_PATH"

rm -f "$PSK_PATH"

# Persist so it survives a reboot (writes current running state back to the conf file)
wg-quick save "$IFACE"
```

### Remove peer
```bash
#!/usr/bin/env bash
# /usr/local/bin/wg-rm-peer.sh
set -euo pipefail

IFACE="$1"

# Check interface name is whitelisted
case "$IFACE" in
    QLS|wg0|wg1) ;;  # put any valid interface names here
    *) echo "bad interface"; exit 1 ;;
esac

# Get server pubkey
WG_PUBKEY=$(wg show | sed -n -e 's/^.*public key: //p')

# Return to stdout
echo "$WG_PUBKEY";
exit;
```

# Sudoers rules

```
# /etc/sudoers.d/wg-add-peer
www-data ALL=(root) NOPASSWD: /usr/local/bin/wg-get-pubkey.sh
www-data ALL=(root) NOPASSWD: /usr/local/bin/wg-add-peer.sh
www-data ALL=(root) NOPASSWD: /usr/local/bin/wg-rm-peer.sh
```

#### Permissions for scripts:
```bash
sudo chown root:root /usr/local/bin/wg-*
sudo chmod 744 /usr/local/bin/wg-*
```


