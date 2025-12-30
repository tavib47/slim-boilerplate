#!/bin/bash

SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$SCRIPT_DIR/.."

source scripts/utils.sh

if [[ "$DDEV" == "true"  ]]; then
  ddev start
  ddev composer install
  ddev cghooks update
else
  echo "Please use DDEV 😄"
fi
