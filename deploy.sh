#!/bin/bash
BOLD='\033[1m'
GREEN='\033[1;32m'
RESET='\033[0m'
NC='\033[39m' # Default Color

echo -e "${BOLD}>>> Resetting ${GREEN}git${NC} repository to HEAD\n${RESET}"
git reset HEAD --hard

echo -e "\n${NC}${BOLD}>>> Install ${GREEN}Composer${NC} packages...${RESET}"
composer install
echo -e "\n${NC}${BOLD}>>> Install ${GREEN}node${NC} packages...${RESET}"
npm install
echo -e "\n${NC}${BOLD}>>> Install ${GREEN}bower${NC} packages...${RESET}"
bower install
echo -e "\n${NC}${BOLD}>>> Running ${GREEN}gulp:build${NC}...${RESET}"
./node_modules/gulp/bin/gulp.js build

echo -e "\n${NC}${BOLD}>>> Adding ownership to ${GREEN}www-data${NC}...${RESET}"
rm -r application/cache/twig

echo -e "\n${NC}${BOLD}>>> Adding ownership to ${GREEN}www-data${NC}...${RESET}"
chown -R :www-data ./*

echo -e "\n\n${NC}${BOLD}>>> Successfully Deployed!!!\n\n${RESET}"
