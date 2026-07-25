# MarketLink — Production Notes

آخر تحديث: 2026-07-25

مرجع بيئة الإنتاج وطريقة التشغيل والنشر. نسخة موثّقة على GitHub مع الكود.

---

## الروابط

| النوع | الرابط |
|------|--------|
| الموقع العام | https://marketlink.app/ |
| GitHub (البرودكشن الحي) | https://github.com/MahmedRahman/marketlink |
| GitHub (نسخة Web أقدم / متوقفة على السيرفر) | https://github.com/MahmedRahman/MarketLink-Web |
| GitHub (مشروع الإيميل) | https://github.com/MahmedRahman/email |

> **مهم:** الموقع الحي `marketlink.app` يشتغل من ريبو **`marketlink`** وليس من `MarketLink-Web`.

---

## السيرفر

| البند | القيمة |
|------|--------|
| SSH | `test@192.168.68.223` |
| مسار الكود الحي | `/home/test/marketlink` |
| مسار MarketLink-Web (احتياطي) | `/home/test/MarketLink-Web` |

```bash
ssh test@192.168.68.223
```

---

## كيف يشتغل الموقع

```
المستخدم
  → Cloudflare (DNS + Proxy على marketlink.app)
  → cloudflared tunnel (systemd: cloudflared.service)
  → Docker container: marketlink_web_app  (host port 8006 → container 80)
  → داخل الحاوية: nginx + php-fpm (supervisor)
  → Laravel من /home/test/marketlink-web

> **ملاحظة 2026-07-25:** الموقع الحي بقى يشغّل **`marketlink-web`** (النظام الداخلي) على نفس منفذ الـ tunnel `8006`.
> النسخة القديمة `marketlink` متاحة احتياطيًا على المنفذ `8008`.
```

### Docker Compose

- ملف: [`docker-compose.yml`](../docker-compose.yml)
- Container: `marketlink_app`
- Image: `Dockerfile` (`php:8.4-fpm-alpine` + nginx + supervisor)
- المنفذ: `8008:80` (احتياطي — الحي على `marketlink-web` منفذ `8006`)
- البيئة: من `.env` على السيرفر (`env_file`) مع قيم افتراضية production في compose
- DB: SQLite (لا تُمسَح عند النشر)

قيم `.env` المتوقعة على السيرفر:

```env
APP_NAME=MarketLink
APP_ENV=production
APP_DEBUG=false
APP_URL=https://marketlink.app
DB_CONNECTION=sqlite
```

أوامر حالة:

```bash
cd /home/test/marketlink
docker compose ps
docker compose logs -f app
curl -I http://127.0.0.1:8006/
curl -I https://marketlink.app/
```

### Cloudflare Tunnel

```bash
systemctl status cloudflared
```

الـ tunnel يشير للمنفذ المحلي `8006`. لا توقفه مع إعادة نشر التطبيق.

---

## النشر

السكربت المعتمد: [`deploy.sh`](../deploy.sh)

على السيرفر:

```bash
cd /home/test/marketlink
./deploy.sh
```

ما يفعله:

1. `git pull --ff-only origin main`
2. `docker compose build` عند تغيّر Dockerfile / compose / `docker/`
3. `docker compose up -d`
4. `artisan migrate --force` + `config/route/view:cache`
5. health check على `http://127.0.0.1:8006/`

إعادة بناء إجبارية:

```bash
FORCE_BUILD=1 ./deploy.sh
```

### Webhook (auto-deploye)

| الأداة | المسار / المنفذ |
|--------|------------------|
| auto-deploye | `/home/test/auto-deploye` — port `777` |
| سكربت marketlink | `scripts/git-pull-marketlink.sh` → يستدعي `/home/test/marketlink/deploy.sh` |
| n8n | container `n8n` — port `5678` |

---

## تشغيل يدوي (بدون deploy.sh)

```bash
cd /home/test/marketlink
git pull origin main
docker compose build
docker compose up -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

إيقاف التطبيق فقط (دون إيقاف الـ tunnel أو n8n):

```bash
cd /home/test/marketlink
docker compose stop
```

---

## ملخص سريع

- **الدومين:** https://marketlink.app/
- **الكود:** https://github.com/MahmedRahman/marketlink
- **السيرفر:** `test@192.168.68.223:/home/test/marketlink`
- **التشغيل:** Docker Compose (`marketlink_app` على `8006`) خلف Cloudflare Tunnel
- **النشر:** `./deploy.sh`
