#!/bin/bash
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

PostDown = iptables -D FORWARD -i $IFACE -o $IFACE -p tcp --dport 80  -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
PostDown = iptables -D FORWARD -i $IFACE -o $IFACE -p tcp --dport 443 -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
PostDown = iptables -D FORWARD -i $IFACE -o $IFACE -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
PostDown = iptables -D FORWARD -i $IFACE -o $IFACE -j REJECT --reject-with icmp-port-unreachable
PostDown = iptables -D FORWARD -i $IFACE -j ACCEPT
PostDown = iptables -D FORWARD -o $IFACE -j ACCEPT
EOF

sudo chmod 600 "$WG_DIR/$IFACE.conf"

sudo apt install -y iptables-persistent

sudo systemctl enable --now wg-quick@$IFACE
