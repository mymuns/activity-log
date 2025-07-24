# ActivityLog for Laravel 12

`ActivityLog` paketi, Laravel 12 API projeleri için geliştirilmiş hibrit bir loglama sistemidir. Hem `Request/Response` loglarını hem de model değişikliklerini kaydeder. Loglar veritabanında veya Elasticsearch, Loki, Sentry, Logstash gibi servislerde tutulabilir.

## 🚀 Özellikler

- API `Request` ve `Response` loglama
- Eloquent model değişikliklerinin versiyonlanması
- Önceki versiyonlara geri dönebilme (`revert`)
- Logları veri tabanı veya dış servislerde saklama desteği
- Yetkilendirme kontrolü ile log geri alma
- Config dosyası üzerinden özelleştirilebilir

---

## 🛠 Kurulum

### 1. Paketi ekleyin

```bash
composer require vendor/activity-log
```
### 2. Yayınlama
```bash
php artisan vendor:publish --tag=activitylog-config
```

### 3. Yapılandırma
config/activitylog.php dosyasını açarak aşağıdaki değerleri düzenleyin:
```php
'storage' => env('ACTIVITY_LOG_STORAGE', 'database'), // database, file, logstash, elasticsearch, sentry

'channels' => [
    'database',
    'daily',
    'logstash',
    'loki',
    'sentry',
    'elasticsearch',
],
```
### 4. Middleware
LogRequestResponseMiddleware sadece API için otomatik olarak tanımlanır. Gerekirse App\Http\Kernel.php içinde elle tanımlanabilir:
```php
'api' => [
    \ActivityLog\Middleware\LogRequestResponseMiddleware::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```
### 5. Kullanım
#### 1. Model Aktivite Loglama
Modelinize trait ekleyin:
```php
use ActivityLog\Traits\LogsModelActivity;

class Post extends Model
{
    use LogsModelActivity;

    protected $logAttributes = ['title', 'content'];
}
```
#### 2. Revert Özelliği
Artisan Komutu:
```bash
php artisan activitylog:revert {id}
```
API Endpoint:
```bash
POST /api/activity-log/{id}/revert
Authorization: Bearer {token}
```
### 6. Veritabanı Yapısı
Paket kurulduğunda aşağıdaki tablo otomatik oluşur:
- activity_logs: Request, response ve model değişikliklerini içerir

### 7. Yetkilendirme
revert işlemleri için aşağıdaki izin zorunludur:
- activity-log.revert