#!/usr/bin/env bash

set -e
sudo mysql -u root -ppassword -e "CREATE user webdiplomacy IDENTIFIED BY 'magic';"
sudo mysql -u root -ppassword -e "CREATE DATABASE webdiplomacy_test;"
sudo mysql -u root -ppassword -e "GRANT ALL PRIVILEGES ON *.* TO 'webdiplomacy' WITH GRANT OPTION"

# we allow this to fail because it's not a working script with mariadb. This will be fixed
# when we move to Eloquent schema migrations
set +e
sudo mysql -u root -ppassword webdiplomacy_test < ~/repo/install/FullInstall/fullInstall.sql
set -e

exit 0