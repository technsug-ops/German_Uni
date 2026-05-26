@echo off
REM AlmanyaUni queue worker - Windows
REM Webhook'lar ve diger async job'lari isler.
REM
REM Kurulum:
REM   1. Windows Task Scheduler -> Yeni gorev
REM   2. Tetikleyici: Bilgisayar baslarken
REM   3. Eylem: bu .bat dosyasini calistir
REM   4. "En yuksek ayricaliklarla calistir" + "Kullanici oturum acmasa da calistir"
REM
REM Manuel calistirma (dev):
REM   queue-worker.bat
REM
REM Worker --max-time=3600 ile saatte bir restart eder (memory leak guvenligi).

cd /d "%~dp0"

:loop
php artisan queue:work --sleep=3 --tries=4 --max-time=3600 --queue=default
echo Worker bitti, 5 sn sonra yeniden baslat...
timeout /t 5 /nobreak >nul
goto loop
