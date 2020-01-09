#!/bin/bash -e

# Source bashrc to make nvm() available
if [ -f ~/.bashrc ]; then
    source ~/.bashrc
fi

# Delete old node_modules
rm -rf node_modules

# Set correct node version
nvm use

npm install
npm run build
