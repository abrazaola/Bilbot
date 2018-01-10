#!/usr/bin/env bash

ssh aitor@vps473909.ovh.net -t 'cd /home/aitor/bilbot && docker-compose up -d'
echo 'Started'
