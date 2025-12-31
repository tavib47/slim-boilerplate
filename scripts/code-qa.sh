#!/bin/bash

SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$SCRIPT_DIR/.."

source scripts/utils.sh

preCommand=$([ "$DDEV" == "true" ] && [ "$IS_DDEV_PROJECT" != "true" ] && echo "ddev exec" || echo "")
status=0

# PHPCS (uses phpcs.xml.dist for configuration)
echo "Running PHPCS..."
bash -c "$preCommand ./vendor/bin/phpcs"
if [ $? -ne 0 ]; then
  echo -e "${red}PHPCS found issues.${no_color}"
  status=1
else
  echo -e "${green}PHPCS passed.${no_color}"
fi

# PHP Code Beautifier and Fixer (uses phpcs.xml.dist for configuration)
echo ""
echo "Running PHPCBF..."
bash -c "$preCommand ./vendor/bin/phpcbf"
phpcbf_exit=$?
if [ $phpcbf_exit -eq 0 ]; then
  echo -e "${green}PHPCBF completed (no fixes needed).${no_color}"
elif [ $phpcbf_exit -eq 1 ]; then
  echo -e "${yellow}PHPCBF fixed some issues.${no_color}"
else
  echo -e "${red}PHPCBF could not fix all issues.${no_color}"
  status=1
fi

# PHPStan (uses phpstan.neon for configuration)
echo ""
echo "Running PHPStan..."
bash -c "$preCommand ./vendor/bin/phpstan analyse --memory-limit=512M"
if [ $? -ne 0 ]; then
  echo -e "${red}PHPStan found issues.${no_color}"
  status=1
else
  echo -e "${green}PHPStan passed.${no_color}"
fi

echo ""
if [ $status -ne 0 ]; then
  echo -e "${red}Code checks failed.${no_color}"
  exit 1
else
  echo -e "${green}All code checks passed.${no_color}"
fi
