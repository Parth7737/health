#!/bin/bash
systemctl stop apache2
cp -R /opt/SHAUK/* /var/www/html/
