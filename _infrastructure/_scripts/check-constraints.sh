#!/bin/bash
cd /var/dev/place-of-power/server
echo "=== categories indexes ==="
docker compose -f _infrastructure/docker-compose.yml exec postgres psql -U app -d app -t -c "SELECT indexname FROM pg_indexes WHERE tablename='categories' ORDER BY indexname;"
echo "=== articles indexes ==="
docker compose -f _infrastructure/docker-compose.yml exec postgres psql -U app -d app -t -c "SELECT indexname FROM pg_indexes WHERE tablename='articles' ORDER BY indexname;"
echo "=== fk constraint ==="
docker compose -f _infrastructure/docker-compose.yml exec postgres psql -U app -d app -t -c "SELECT conname FROM pg_constraint WHERE conrelid='articles'::regclass AND contype='f';"