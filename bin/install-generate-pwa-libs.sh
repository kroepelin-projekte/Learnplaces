#!/usr/bin/env sh

set -e

installDependency() {
    (mkdir -p "`dirname "$0"`/../generate-pwa-libs/$1" && cd "`dirname "$0"`/../generate-pwa-libs/$1" && wget -O - "$2" | tar -xz --strip-components=1)
}

installDependency flux-json-api https://github.com/fluxfw/flux-json-api/archive/refs/tags/v2022-11-01-1.tar.gz

installDependency flux-localization-api https://github.com/fluxfw/flux-localization-api/archive/refs/tags/v2022-11-07-1.tar.gz

installDependency flux-pwa-generator-api https://github.com/fluxfw/flux-pwa-generator-api/archive/refs/tags/v2022-11-07-2.tar.gz
