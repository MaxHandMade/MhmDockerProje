# Docker Compose Çoklu Uygulama Ortamı

Bu proje, Docker Compose kullanarak çeşitli web uygulamalarını ve servislerini tek bir ortamda çalıştırmanızı sağlar. İçerik yönetimi, veritabanı yönetimi, otomasyon ve yerel yapay zeka (LLM) yeteneklerini bir araya getirir.

## 🚀 İçerdiği Servisler

- **WordPress:** Blog ve içerik yönetimi platformu.
- **phpMyAdmin:** MariaDB veritabanı için web tabanlı yönetim arayüzü.
- **MariaDB:** WordPress ve phpMyAdmin için ilişkisel veritabanı sistemi.
- **n8n:** Güçlü bir açık kaynaklı otomasyon ve entegrasyon aracı.
- **PostgreSQL:** n8n için kullanılan ilişkisel veritabanı sistemi.
- **Ollama:** Yerel olarak dil modellerini (LLM) çalıştırmak için bir platform.
- **OpenWebUI:** Ollama için kullanıcı dostu bir web arayüzü.
- **ComfyUI:** Görüntü ve video oluşturma için güçlü ve esnek bir nod tabanlı arayüz.

## ✨ Özellikler

- Tüm servisler `my-app-network` isimli özel bir Docker ağı üzerinden iletişim kurar.
- Verilerinizin kalıcı olması için her servis için ayrı Docker volumes tanımlanmıştır.
- Servis bağımlılıkları (`depends_on`) doğru başlatma sırasını garanti eder.
- Ollama servisi isteğe bağlı olarak GPU desteği için yapılandırılabilir.
- n8n, otomasyon iş akışlarında Ollama'yı kullanacak şekilde ayarlanabilir.

##  prerequisites

- Docker yüklü ve çalışıyor olmalı.
- Docker Compose (veya Docker Desktop ile gelen compose) yüklü olmalı.

## 🔧 Kurulum ve Başlatma

1.  Projeyi indirin veya dosyaları (özellikle `docker-compose.yml` ve eğer kullanacaksanız `.env` ve `nginx` klasörlerini) çalışma dizininize yerleştirin.

2.  **.env Dosyası Oluşturma (İsteğe bağlı ama önerilir):**

    Güvenlik için veritabanı şifrelerinizi ve diğer hassas bilgileri yönetmek amacıyla `.env` dosyası oluşturabilirsiniz. `docker-compose.yml` dosyasındaki `environment` bölümlerinde tanımlı şifreleri kendi seçeceğiniz güvenli şifrelerle değiştirmeniz **önemle tavsiye edilir**. Şu anki yapılandırma şifreleri doğrudan `docker-compose.yml` içinde barındırmaktadır.

    Örnek `.env` içeriği (Eğer `.env` kullanacaksanız `docker-compose.yml` içindeki `environment` değerlerini `$VARIABLE_NAME` şeklinde güncelleyin):

    ```env
    MYSQL_ROOT_PASSWORD=guvenli_mariadb_root_sifresi
    MYSQL_DATABASE=mydatabase
    MYSQL_USER=myuser
    MYSQL_PASSWORD=guvenli_mariadb_kullanici_sifresi

    POSTGRES_DB=n8n_database
    POSTGRES_USER=n8n_user
    POSTGRES_PASSWORD=guvenli_postgres_sifresi
    # n8n için ek ayarlar (isteğe bağlı)
    # GENERIC_TIMEZONE=Europe/Istanbul
    ```

3.  **Docker Servislerini Başlatma:**

    Proje dizininde ( `docker-compose.yml` dosyasının bulunduğu yerde) terminali açın ve aşağıdaki komutu çalıştırın:

    ```bash
    docker compose up -d
    ```

    Bu komut, tüm servisleri arka planda başlatacaktır. Servislerin ilk defa kurulması biraz zaman alabilir.

4.  **LLM Modelini Yükleme (Ollama için):**

    Ollama servisi çalıştıktan sonra, kullanmak istediğiniz dil modellerini indirebilirsiniz. Örneğin, `mistral:7b` modelini yüklemek için:

    ```bash
    docker exec ollama_container ollama pull mistral:7b
    ```

## 🖥️ Servislere Erişim

Servisler başladıktan sonra aşağıdaki adreslerden erişebilirsiniz:

- **WordPress:** `http://localhost:8081` (Nginx üzerinden)
- **phpMyAdmin:** `http://localhost:8080`
- **n8n:** `http://localhost:5678`
- **OpenWebUI (Ollama Arayüzü):** `http://localhost:3000`
- **ComfyUI:** `http://localhost:8188` (Görüntü ve video oluşturma arayüzü)
- **MariaDB:** `localhost:3306` (Genellikle sadece diğer container'lar tarafından kullanılır)
- **PostgreSQL:** `localhost:5432` (Genellikle sadece n8n container'ı tarafından kullanılır)

## 📁 Klasör Yapısı

```
.
├── docker-compose.yml
├── README.md
├── wp-config.php
├── nginx/
│   └── nginx.conf
└── wp-content/
    └── mu-plugins/
        └── enable-app-passwords.php
```

**Not:** `.env` dosyası isteğe bağlıdır ve volume klasörleri (örneğin `mariadb_data`, `wordpress_data`, vb.) Docker tarafından yönetilir ve genellikle proje dizininin dışında veya Docker'ın kendi depolama alanında bulunur.

## 🛑 Uygulamaları Durdurma

Tüm çalışan servisleri durdurmak ve silmek için proje dizininde aşağıdaki komutu kullanın:

```bash
docker compose down
```

Bu komut container'ları durdurur ve siler, ancak `volumes` belirtildiği için verileriniz kalıcı olur.

## 📝 Notlar

- MariaDB ve PostgreSQL portları (`3306` ve `5432`) yerel makinenize dışarıdan erişim için açıktır ancak genellikle diğer container'lar tarafından kullanılır.
- OpenWebUI, Ollama container'ına `OLLAMA_BASE_URL: http://ollama:11434` ortam değişkeni ile bağlanır.
- Ollama'nın GPU kullanabilmesi için Docker Desktop veya Docker Engine ayarlarınızda GPU desteğinin etkinleştirildiğinden ve uygun sürücülerin yüklü olduğundan emin olun.
- ComfyUI için modelleri (`stable-diffusion`, `ControlNet`, `VAE` vb.) ComfyUI web arayüzü üzerinden veya Docker Volume'unuza manuel olarak kopyalayarak yükleyebilirsiniz. Modeller `comfyui_data` Docker volume'unda kalıcı olarak saklanır.

## 🐛 Bilinen Sorunlar ve Çözümleri

### WordPress Uygulama Parolalarının Etkinleştirilmesi

**Sorun:** WordPress, varsayılan olarak uygulama parolalarının kullanılabilmesi için HTTPS bağlantısı gerektirir. Bu proje ilk kurulduğunda ve WordPress doğrudan HTTP üzerinden (`http://localhost:80`) çalıştığında, uygulama parolaları arayüzde görünmez ve etkinleştirilemez.

**Çözüm:** Bu sorunu aşmak ve uygulama parolalarını geliştirme ortamında etkinleştirmek için aşağıdaki adımlar izlenmiştir:

1.  **Nginx Ters Proxy Kurulumu:** WordPress servisi önüne bir Nginx ters proxy konulmuştur. Nginx, gelen istekleri karşılar ve dahili ağ üzerinden WordPress servisine yönlendirir. Bu, WordPress'in bir ters proxy arkasında çalıştığını algılamasını sağlar.
2.  **Port Değişikliği:** Nginx servisi, yerel makinede 80 portu yerine 8081 portuna (`http://localhost:8081`) bağlanacak şekilde yapılandırılmıştır. Bu, 80 portunu kullanabilecek diğer uygulamalarla çakışmayı önler.
3.  **Must-Use Plugin:** WordPress'in HTTPS gereksinimini atlamak ve HTTP ortamında bile uygulama parolalarını zorla etkinleştirmek için bir Must-Use plugin (`wp-content/mu-plugins/enable-app-passwords.php`) eklenmiştir. Bu plugin, `wp_is_application_passwords_available` filtresini kullanarak uygulama parolaları özelliğini her zaman açık hale getirir.
4.  **Volume Mount:** Yerel `wp-content/mu-plugins` klasörü, Docker volume yerine doğrudan WordPress container'ının `/var/www/html/wp-content/mu-plugins` yoluna bağlanmıştır. Bu, plugin dosyasının container içinde doğru şekilde bulunmasını sağlar.

Bu adımlar sonucunda, WordPress sitesine `http://localhost:8081` adresinden erişildiğinde uygulama parolaları özelliği kullanılabilir hale gelmiştir.

### WordPress Multisite Kurulumu

**Sorun:** WordPress Multisite özelliğini etkinleştirmek ve birden çok WordPress sitesi barındırmak için ek yapılandırma adımları gereklidir.

**Çözüm:** WordPress Multisite'ı (alt dizin modunda) etkinleştirmek için aşağıdaki adımlar izlenmiştir:

1.  **`wp-config.php` Güncellemesi:** Yerel `wp-config.php` dosyanıza WordPress Yönetici Paneli'ndeki "Ağ Kurulumu" sayfasında belirtilen Multisite yapılandırma kuralları eklenmiştir. Bu kurallar genellikle `define('MULTISITE', true);` ve diğer ilgili sabitleri içerir.
2.  **Nginx Yönlendirme Kuralları:** WordPress'in `.htaccess` dosyasına eklenmesi gereken Multisite yönlendirme kuralları, Nginx ters proxy yapılandırması olan `nginx/nginx.conf` dosyasına eklenmiştir. Bu kurallar, Multisite alt dizin yapısına uygun şekilde istekleri doğru WordPress dizinlerine yönlendirir.
3.  **Docker Compose'da WordPress Ortam Değişkeni:** `docker-compose.yml` dosyasındaki WordPress servisine `WORDPRESS_CONFIG_EXTRA` ortam değişkeni ile Multisite için gerekli temel tanımlar (örn: `define('WP_ALLOW_MULTISITE', true);`) eklenmiştir. Ancak yerel `wp-config.php` dosyasının bind mount edilmesi durumunda, bu ortam değişkeni yerine yerel dosyadaki tanımlar öncelik kazanır. Bu nedenle, yerel `wp-config.php` dosyasının Multisite yapılandırmasını içermesi önemlidir.

Bu adımlar tamamlandıktan sonra, WordPress sitenize `http://localhost:8081` üzerinden erişerek ağ kurulumunu tamamlayabilir ve birden çok siteyi yönetmeye başlayabilirsiniz. 