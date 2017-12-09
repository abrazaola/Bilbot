#!/usr/bin/env bash

ssh aitor@vps473909.ovh.net -t 'cd /home/aitor/bilbot && docker-compose down'
ssh aitor@vps473909.ovh.net -t 'rm -rf /home/aitor/bilbot; mkdir /home/aitor/bilbot'
tar --exclude='.git' -zcvf release.tar.gz .
scp release.tar.gz aitor@vps473909.ovh.net:/home/aitor/bilbot/release.tar.gz
ssh aitor@vps473909.ovh.net -t 'tar -zxvf /home/aitor/bilbot/release.tar.gz -C /home/aitor/bilbot'
ssh aitor@vps473909.ovh.net -t 'cd /home/aitor/bilbot && docker-compose up -d'
rm release.tar.gz
echo '🍻 Bilbot deployed successfully'
