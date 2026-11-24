#!/bin/bash
set -e
PATCHES_URL=https://raw.githubusercontent.com/LokiCheckout/magento-2.4.9-patches/d670eb0a2361a9e9173262344f03398091840ec5/core/patches.json
PATCHES_SHA256=d3b3efd36c9e87ec4b98334370fb0da72ae2cca8536f2db00e00c0920e6307b4
cp composer.json composer-old.json
curl -sSf "$PATCHES_URL" -o loki-checkout-patches.json
echo "$PATCHES_SHA256  loki-checkout-patches.json" | sha256sum -c -
jq -s add composer-old.json loki-checkout-patches.json > composer.json
