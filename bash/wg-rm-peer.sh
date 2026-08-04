#!/usr/bin/env bash
# /usr/local/bin/wg-rm-peer.sh
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
