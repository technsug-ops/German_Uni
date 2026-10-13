#!/usr/bin/env bash
# Local snapshot temizleyici (bkz. docs/LOCAL_DB_BASELINE.md).
# Yalnız 127.0.0.1/localhost üzerindeki, adı 'almanyauni_baseline' ile başlayan ve uygulama şemasını
# (migrations + faqs + posts) içeren bir veritabanında çalışır. Mevcut 'almanyauni', forum ve test
# veritabanları reddedilir.
#   bash scripts/local-db/sanitize.sh almanyauni_baseline
set -euo pipefail
DB="${1:?Hedef local veritabanı adı gerekli (ör. almanyauni_baseline)}"
HOST="${DB_HOST_OVERRIDE:-127.0.0.1}"
USER="${DB_USER_OVERRIDE:-root}"

case "$HOST" in 127.0.0.1|localhost) ;; *) echo "DUR: host '$HOST' local değil." >&2; exit 2 ;; esac
case "$DB" in almanyauni_baseline*) ;; *) echo "DUR: '$DB' bir baseline snapshot adı değil (almanyauni_baseline* bekleniyor)." >&2; exit 2 ;; esac
case "$DB" in *_test|*forum*) echo "DUR: test/forum veritabanı hedeflenmez." >&2; exit 2 ;; esac

n=$(mysql -h"$HOST" -u"$USER" -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB' AND table_name IN ('migrations','faqs','posts','users')")
if [ "$n" != "4" ]; then echo "DUR: '$DB' uygulama şemasını içermiyor (migrations/faqs/posts/users bulunamadı: $n/4)." >&2; exit 2; fi

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
mysql -h"$HOST" -u"$USER" --default-character-set=utf8mb4 "$DB" < "$DIR/sanitize-local-snapshot.sql"
