#!/bin/bash
set -e

# Fix MPM conflict at runtime - ensure only prefork is active
rm -f /etc/apache2/mods-enabled/mpm_event.load
rm -f /etc/apache2/mods-enabled/mpm_event.conf
rm -f /etc/apache2/mods-enabled/mpm_worker.load
rm -f /etc/apache2/mods-enabled/mpm_worker.conf
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load 2>/dev/null || true
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf 2>/dev/null || true

# Set Apache to listen on Railway's PORT (default 80)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf

# Debug: show loaded MPMs
echo "=== Loaded MPM modules ==="
ls -la /etc/apache2/mods-enabled/mpm_* 2>/dev/null || echo "No MPM found"
apache2ctl -M 2>/dev/null | grep mpm || true
echo "========================="

exec apache2-foreground
