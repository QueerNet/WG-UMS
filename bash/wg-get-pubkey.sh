#!/usr/bin/env bash
# /usr/local/bin/wg-rm-peer.sh
set -euo pipefail

IFACE="$1"

# Check interface name is whitelisted
case "$IFACE" in
    QLS|wg0|wg1) ;;  # add any other valid interface names here
    *) echo "bad interface"; exit 1 ;;
esac

WG_PUBKEY=$(wg show | sed -n -e 's/^.*public key: //p')

echo "$WG_PUBKEY";
exit;
