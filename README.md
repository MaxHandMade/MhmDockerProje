# Docker Compose Çoklu Uygulama Ortamı

Bu proje, Docker Compose kullanarak çeşitli web uygulamalarını ve servislerini tek bir ortamda çalıştırmanızı sağlar. İçerik yönetimi, veritabanı yönetimi, otomasyon ve yerel yapay zeka (LLM) ile görüntü/video oluşturma yeteneklerini bir araya getirir.

## 🚀 İçerdiği Servisler

- **WordPress:** Blog ve içerik yönetimi platformu.
- **phpMyAdmin:** MariaDB veritabanı için web tabanlı yönetim arayüzü.
- **MariaDB:** WordPress ve phpMyAdmin için ilişkisel veritabanı sistemi.
- **n8n:** Güçlü bir açık kaynaklı otomasyon ve entegrasyon aracı.
- **PostgreSQL:** n8n için kullanılan ilişkisel veritabanı sistemi.
- **Ollama:** Yerel olarak dil modellerini (LLM) çalıştırmak için bir platform.
- **OpenWebUI:** Ollama için kullanıcı dostu bir web arayüzü.
- **ComfyUI:** Görüntü ve video oluşturma için güçlü ve modüler bir yapay zeka aracı.

## ✨ Özellikler

- Tüm servisler `my-app-network` isimli özel bir Docker ağı üzerinden iletişim kurar.
- Verilerinizin kalıcı olması için her servis için ayrı Docker volumes tanımlanmıştır. ComfyUI için ise ana makinedeki yerel klasörler (bind mounts) kullanılmıştır.
- Servis bağımlılıkları (`depends_on`) doğru başlatma sırasını garanti eder.
- Ollama ve ComfyUI servisleri isteğe bağlı olarak Nvidia GPU desteği için yapılandırılabilir.
- ComfyUI, `--lowvram` argümanı ile daha az GPU belleği kullanarak büyük modelleri çalıştırma yeteneğine sahiptir.
- n8n, otomasyon iş akışlarında Ollama'yı kullanacak şekilde ayarlanabilir.

## prerequisites

- Docker yüklü ve çalışıyor olmalı.
- Docker Compose (veya Docker Desktop ile gelen compose) yüklü olmalı.
- **Windows / WSL2 Kullanıcıları İçin Ek Not:** Büyük yapay zeka modellerini çalıştırırken bellek sorunları yaşamamak için `C:\Users\<KULLANICI_ADINIZ>\.wslconfig` dosyasını oluşturarak veya düzenleyerek WSL2'ye ayrılan bellek (RAM) miktarını artırmanız önerilir. Örnek:
    ```ini
    [wsl2]
    memory=12GB  # Sistem RAM'inizin büyük bir kısmını ayırın (örneğin 16GB için 12GB)
    processors=6 # Kullanmak istediğiniz CPU çekirdeği sayısı (isteğe bağlı)
    ```
    Değişikliklerden sonra Komut İstemi'nden `wsl --shutdown` komutunu çalıştırın ve Docker Desktop'ı yeniden başlatın.

## 🔧 Kurulum ve Başlatma

1.  Projeyi indirin veya dosyaları (özellikle `docker-compose.yml`, `extra_model_paths.yaml` ve `nginx` klasörlerini) çalışma dizininize yerleştirin.

2.  **Gerekli Klasörleri Oluşturma:** ComfyUI ve diğer servisler için ana makinenizde aşağıdaki klasör yapılarını oluşturun:
    ```bash
    # PowerShell (Windows) için örnek komutlar:
    New-Item -ItemType Directory -Force comfyui_models/checkpoints
    New-Item -ItemType Directory -Force comfyui_models/loras
    New-Item -ItemType Directory -Force comfyui_models/vae
    # ... ve extra_model_paths.yaml'da tanımlı diğer tüm modeller için
    New-Item -ItemType Directory -Force comfyui_data_io/input
    New-Item -ItemType Directory -Force comfyui_data_io/output
    New-Item -ItemType Directory -Force comfyui_data_io/custom_nodes
    ```

3.  **LLM ve Görüntü Modellerini Yerleştirme:**
    *   İndirdiğiniz **ComfyUI modellerini** (`.safetensors`, `.ckpt`, `.pth` vb.) `comfyui_models/checkpoints/` veya `comfyui_models/loras/`, `comfyui_models/vae/` gibi ilgili alt dizinlere yerleştirin.
    *   Ollama için indirdiğiniz LLM modelleri Docker volumes içinde yönetildiği için ayrı bir klasöre taşımanıza gerek yoktur.

4.  **.env Dosyası Oluşturma (İsteğe bağlı ama önerilir):**

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

5.  **Docker Servislerini Başlatma:**

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
- **WordPress:** `http://localhost:80`
- **phpMyAdmin:** `http://localhost:8080`
- **n8n:** `http://localhost:5678`
- **Ollama Arayüzü (OpenWebUI):** `http://localhost:3000`
- **ComfyUI:** `http://localhost:8188`
- **MariaDB:** `localhost:3306` (Genellikle sadece diğer container'lar tarafından kullanılır)
- **PostgreSQL:** `localhost:5432` (Genellikle sadece n8n container'ı tarafından kullanılır)

## 📁 Klasör Yapısı

```
.
├── docker-compose.yml
├── extra_model_paths.yaml      # ComfyUI için ek model yolları yapılandırması
├── README.md
├── comfyui_models/             # ComfyUI modelleri için ana klasör (bind mount)
│   ├── checkpoints/            # Checkpoint modelleri (.safetensors, .ckpt)
│   ├── loras/                  # LoRA modelleri
│   ├── vae/                    # VAE modelleri
│   ├── text_encoders/          # Metin kodlayıcı modelleri
│   ├── diffusion_models/       # Difüzyon modelleri
│   ├── clip_vision/            # CLIP Vision modelleri
│   ├── style_models/           # Stil modelleri
│   ├── embeddings/             # Embedding modelleri
│   ├── diffusers/              # Diffusers modelleri
│   ├── controlnet/             # ControlNet modelleri
│   ├── gligen/                 # GLIGEN modelleri
│   ├── upscale_models/         # Upscale modelleri
│   ├── hypernetworks/          # Hypernetwork modelleri
│   ├── photomaker/             # PhotoMaker modelleri
│   ├── classifiers/            # Sınıflandırıcı modelleri
│   ├── download_model_base/    # İndirilen temel modeller (varsa)
│   └── vae_approx/             # VAE Approximation modelleri
├── comfyui_data_io/            # ComfyUI giriş/çıkış ve özel düğüm verileri için (bind mount)
│   ├── input/                  # İş akışları için giriş dosyaları
│   ├── output/                 # Oluşturulan çıktı görüntüleri/videoları
│   └── custom_nodes/           # Özel ComfyUI düğümleriniz
├── wp-config.php               # WordPress yapılandırma dosyası (varsa)
├── nginx/                      # Nginx ters proxy yapılandırması
│   └── nginx.conf
└── wp-content/                 # WordPress içerik dosyaları
    └── mu-plugins/             # WordPress "Must-Use" eklentileri
        └── enable-app-passwords.php

```

## 🛑 Uygulamaları Durdurma

Tüm çalışan servisleri durdurmak ve silmek için proje dizininde aşağıdaki komutu kullanın:
```bash
docker compose down
```

Bu komut container'ları durdurur ve siler, ancak `volumes` belirtildiği için verileriniz kalıcı olur.

## 📝 Notlar

- MariaDB ve PostgreSQL portları (`3306` ve `5432`) yerel makinenize dışarıdan erişim için açıktır ancak genellikle diğer container'lar tarafından kullanılır.
- OpenWebUI, Ollama container'ına `OLLAMA_BASE_URL: http://ollama:11434` ortam değişkeni ile bağlanır.
- Ollama ve ComfyUI'nin GPU kullanabilmesi için Docker Desktop veya Docker Engine ayarlarınızda GPU desteğinin etkinleştirildiğinden ve uygun Nvidia sürücülerinin yüklü olduğundan emin olun. ComfyUI, `--lowvram` argümanı ile daha az GPU belleği kullanmaya çalışır.

## 🐛 Bilinen Sorunlar ve Çözümleri

### WordPress Uygulama Parolalarının Etkinleştirilmesi

**Sorun:** WordPress, varsayılan olarak uygulama parolalarının kullanılabilmesi için HTTPS bağlantısı gerektirir. Bu proje ilk kurulduğunda ve WordPress doğrudan HTTP üzerinden (`http://localhost:80`) çalıştığında, uygulama parolaları arayüzde görünmez ve etkinleştirilemez.
**Çözüm:** Bu sorunu aşmak ve uygulama parolalarını geliştirme ortamında etkinleştirmek için aşağıdaki adımlar izlenmiştir:
1.  **Nginx Ters Proxy Kurulumu:** WordPress servisi önüne bir Nginx ters proxy konulmuştur. Nginx, gelen istekleri karşılar ve dahili ağ üzerinden WordPress servisine yönlendirir. Bu, WordPress'in bir ters proxy arkasında çalıştığını algılamasını sağlar.
2.  **Port Değişikliği:** Nginx servisi, yerel makinede 80 portu yerine 8081 portuna (`http://localhost:8081`) bağlanacak şekilde yapılandırılmıştır. Bu, 80 portunu kullanabilecek diğer uygulamalarla çakışmayı önler.
3.  **Must-Use Plugin:** WordPress'in HTTPS gereksinimini atlamak ve HTTP ortamında bile uygulama parolalarını zorla etkinleştirmek için bir Must-Use plugin (`wp-content/mu-plugins/enable-app-passwords.php`) eklenmiştir. Bu plugin, `wp_is_application_passwords_available` filtresini kullanarak uygulama parolaları özelliğini her zaman açık hale getirir.
4.  **Volume Mount:** Yerel `wp-content/mu-plugins` klasörü, Docker volume yerine doğrudan WordPress container'ının `/var/www/html/wp-content/mu-plugins` yoluna bağlanmıştır. Bu, plugin dosyasının container içinde doğru şekilde bulunmasını sağlar.
Bu adımlar sonucunda, WordPress sitesine `http://localhost:8081` adresinden erişildiğinde uygulama parolaları özelliği kullanılabilir hale gelmiştir. 
