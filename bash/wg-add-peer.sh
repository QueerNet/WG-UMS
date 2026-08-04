#!/usr/bin/env bash
# /usr/local/bin/wg-add-peer.sh
set -euo pipefail

IFACE="$1"
PUBKEY="$2"
ALLOWED_IP="$3"

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
wg set "$IFACE" peer "$PUBKEY" allowed-ips "$ALLOWED_IP"

# Persist so it survives a reboot (writes current running state back to the conf file)
wg-quick save "$IFACE"
