#!/bin/bash

# Colors for output
red='\033[0;31m'
green='\033[0;32m'
yellow='\033[0;33m'
no_color='\033[0m'

# Function to get the arguments from command line
function args()
{
    options=$(getopt --long ddev -- "$@")
    [ $? -eq 0 ] || {
        echo "Incorrect option provided"
        exit 1
    }
    eval set -- "$options"
    while true; do
        case "$1" in
        --ddev)
            DDEV=true
            ;;
        --)
            shift
            break
            ;;
        esac
        shift
    done
}

# Set DDEV to true by default
DDEV=true

# Load .env file
if test -f "$SCRIPT_DIR/../.env"; then
  source .env
fi

# Get the arguments
args $0 "$@"
