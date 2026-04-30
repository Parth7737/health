#!/bin/bash
set -e
set -x

# Check if /opt/SHAUK exists, then clean it
if [ -d /opt/SHAUK ]; then
    rm -rf /opt/SHAUK/*
    rm -rf /opt/SHAUK/.* 2>/dev/null || true  # Suppress errors for . and ..
else
    mkdir -p /opt/SHAUK
fi

# Recreate the target HTML directory
mkdir -p /var/www/html/SHAUK

# Final flush
sync
