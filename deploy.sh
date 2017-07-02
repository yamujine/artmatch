#!/bin/bash
echo -e "\e[1m>>> Resetting \e[32mgit\e[39m repository to HEAD\n\e[0m"
git reset HEAD --hard
echo -e "\n\e[0m\e[1m>>> Install \e[32mnode\e[39m packages...\e[0m"
npm install
echo -e "\n\e[0m\e[1m>>> Install \e[32mbower\e[39m packages...\e[0m"
bower install
echo -e "\n\e[0m\e[1m>>> Running \e[32mgulp:build\e[39m...\e[0m"
./node_modules/gulp/bin/gulp.js build

echo -e "\n\e[0m\e[1m>>> Adding ownership to \e[32mwww-data\e[39m...\e[0m"
chown -R :www-data ./*

echo -e "\n\n\e[0m\e[1m>>> Successfully Deployed!!!\n\n\e[0m"
