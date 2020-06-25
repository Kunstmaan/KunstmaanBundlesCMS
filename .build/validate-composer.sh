#!/usr/bin/env bash

hasFailure=0
packages=$(find "$(pwd)/src/Kunstmaan" -maxdepth 2 -mindepth 2 -type f -name composer.json -exec dirname '{}' \; | sort -n )
for package in $packages; do
    echo "- Validating $(basename ${package})"
    composer validate --ansi --strict --no-check-lock ${package}/composer.json

    exitCode=$?
    if [ $exitCode -ne 0 ]; then
        hasFailure=$exitCode
    fi
done

echo "- Validating main composer.json"
composer validate --ansi --strict --no-check-lock composer.json

exitCode=$?
if [ $hasFailure -ne 0 ] || [ $exitCode -ne 0 ] ; then
  exit 1;
fi
