#!/bin/bash
# Equivalente Linux do jasperstarter.bat usado no Windows local. Fica ao lado
# do jasperstarter.bat dentro de jasperstarter7/bin (pasta nao versionada,
# copiada manualmente para o VPS - ver reports-service/README de deploy).
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
exec java -cp "$SCRIPT_DIR/../patch:$SCRIPT_DIR/../lib/*" de.cenote.jasperstarter.App "$@"
