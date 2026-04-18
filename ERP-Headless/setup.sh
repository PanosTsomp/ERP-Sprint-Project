#!/bin/bash
set -e

echo ""
echo "================================="
echo " ERP Sprint — Architect Setup   "
echo "================================="
echo ""

# Check Docker is installed
if ! command -v docker &> /dev/null; then
    echo "ERROR: Docker is not installed."
    echo "Run: sudo pacman -S docker docker-compose"
    echo "Then: sudo systemctl enable --now docker"
    exit 1
fi

# Check Docker is running
if ! docker info &> /dev/null; then
    echo "ERROR: Docker daemon is not running."
    echo "Run: sudo systemctl start docker"
    exit 1
fi

echo "[1/4] Cloning frappe_docker..."
if [ -d "frappe_docker" ]; then
    echo "frappe_docker already exists — skipping clone."
else
    git clone https://github.com/frappe/frappe_docker frappe_docker
fi

echo ""
echo "[2/4] Copying environment template..."
if [ -f "frappe_docker/.env" ]; then
    echo ".env already exists — skipping."
else
    cp .env.example frappe_docker/.env
    echo "Created frappe_docker/.env from template."
fi

echo ""
echo "[3/4] Checking iptables rules for Tailscale + Docker..."
BRIDGE=$(ip link show | grep 'br-' | awk '{print $2}' | tr -d ':' | head -1)

if [ -z "$BRIDGE" ]; then
    echo "No Docker bridge found yet — start the stack first, then re-run this script."
else
    echo "Found Docker bridge: $BRIDGE"
    sudo iptables -C FORWARD -i tailscale0 -o "$BRIDGE" -j ACCEPT 2>/dev/null || \
        sudo iptables -I FORWARD -i tailscale0 -o "$BRIDGE" -j ACCEPT
    sudo iptables -C FORWARD -i "$BRIDGE" -o tailscale0 -j ACCEPT 2>/dev/null || \
        sudo iptables -I FORWARD -i "$BRIDGE" -o tailscale0 -j ACCEPT
    echo "iptables rules applied."
fi

echo ""
echo "[4/4] Done. Next steps:"
echo ""
echo "  1. Edit frappe_docker/.env and set your DB_PASSWORD"
echo "  2. cd frappe_docker"
echo "  3. docker compose -f pwd.yml up -d"
echo "  4. Watch setup: docker compose -f pwd.yml logs -f create-site"
echo "  5. Open http://localhost:8080 — login: Administrator / admin"
echo "  6. Create an Item, generate API keys, share Tailscale with Integrator"
echo ""
echo "ERPNext will be available at http://<your-tailscale-ip>:8080"
echo ""