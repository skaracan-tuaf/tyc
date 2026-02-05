# TYC - Mühimmat Karşılaştırma ve Yönetim Sistemi

Laravel 11 tabanlı, mühimmat karşılaştırma ve yönetim sistemi. Bu proje, farklı mühimmat türlerini kategorilere göre filtreleyip karşılaştırma yapmanıza olanak sağlar.

## Özellikler

- 🎯 **Mühimmat Karşılaştırma**: Kategori, hedef tipi ve hava durumuna göre mühimmat karşılaştırması
- 📝 **Blog Sistemi**: Makale ve etiket yönetimi
- 🏷️ **Kategori Yönetimi**: Hiyerarşik kategori yapısı
- 🖼️ **Görsel Yönetimi**: Mühimmat görselleri yönetimi
- 🔍 **Arama**: Gelişmiş arama özellikleri
- 👤 **Kullanıcı Yönetimi**: Laravel Jetstream ile kullanıcı yönetimi

## Teknolojiler

- **Backend**: Laravel 11
- **Frontend**: Blade Templates, Tailwind CSS
- **Authentication**: Laravel Jetstream
- **Database**: MySQL/PostgreSQL/SQLite

## Gereksinimler

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite

## Kurulum

1. Projeyi klonlayın:
```bash
git clone <repository-url>
cd tyc
```

2. Bağımlılıkları yükleyin:
```bash
composer install
npm install
```

3. Ortam değişkenlerini yapılandırın:
```bash
cp .env.example .env
php artisan key:generate
```

4. Veritabanını yapılandırın ve migration'ları çalıştırın:
```bash
php artisan migrate
```

5. Asset'leri derleyin:
```bash
npm run build
# veya development için:
npm run dev
```

6. Sunucuyu başlatın:
```bash
php artisan serve
```

## Proje Yapısı

```
app/
├── Http/
│   └── Controllers/
│       ├── FrontendController.php    # Frontend sayfaları
│       ├── BackendController.php     # Admin paneli
│       └── ...
├── Models/                           # Eloquent modelleri
├── Services/                         # İş mantığı servisleri
└── ...

resources/
├── views/
│   └── Frontend/                     # Frontend görünümleri
└── ...

public/
├── frontend_assets/                  # Frontend asset'leri
└── backend_assets/                   # Admin panel asset'leri
```

## Optimizasyonlar

- ✅ Cache kullanımı ile performans iyileştirmeleri
- ✅ N+1 sorgu probleminin çözülmesi
- ✅ Kod organizasyonu ve servis katmanı
- ✅ PSR standartlarına uyum

## Lisans

MIT License

## Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## İletişim

Sorularınız için issue açabilirsiniz.
