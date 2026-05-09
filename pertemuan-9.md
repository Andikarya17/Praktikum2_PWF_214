# Modul Pertemuan 9: API(CRUD)

**Tujuan Pembelajaran**
Materi ini bertujuan untuk mengetahui bagaimana cara membuat CRUD API Laravel

### A. Pengenalan API

---

1. Apa itu API?
   API _(Application Programming Interface)_ adalah jembatan komunikasi yang memungkinkan dua aplikasi atau sistem yang berbeda untuk saling bertukar data.

![erd](/screenshoot/pertemuan-9/api-analogy.jpeg)

> **Analogi**
> Bayangkan Anda di restoran. Anda (sebagai aplikasi Frontend) tidak mungkin masuk ke dapur (sebagai Database/Backend) untuk memasak sendiri. Anda memanggil pelayan (API) untuk mencatat pesanan, pelayan membawanya ke dapur, lalu pelayan kembali membawa makanan ke meja Anda.

2. Perbedaan Route Biasa vs Route API
   Di Laravel, perbedaan utamanya terletak pada output yang dihasilkan:

- **Route Biasa (routes/web.php)**: Mengembalikan halaman web utuh (HTML/CSS/JS) yang siap dilihat mata.
- **Route API (routes/api.php)**: Hanya mengembalikan data mentah tanpa desain, biasanya dalam format JSON (JavaScript Object Notation). Aplikasi pemanggil yang akan mendesain sendiri bagaimana data tersebut ditampilkan.

3. Mengapa Kita Menggunakan API?

- Satu Backend, Banyak Platform: Dengan menyediakan API, satu sistem backend Laravel dapat digunakan secara bersamaan oleh Website (misal: React/Next.js) dan Aplikasi Mobile (misal: Flutter).
- Pembagian Kerja (Separation of Concerns): Tim Backend bisa fokus pada logika data dan keamanan, sementara tim Frontend fokus pada tampilan tanpa saling mengganggu kode satu sama lain.

### B. Standar HTTP Status Code

---

Berikut adalah format balasan status API yang akan kita gunakan dalam praktik:

- **200 OK**: Request berhasil, server mengembalikan data (Digunakan untuk Read/Search).
- **201 Created**: Request berhasil, data baru berhasil disimpan (Digunakan untuk Create).
- **204 No Content**: Request berhasil, tapi tidak ada data untuk dikembalikan (Misal: setelah Delete).
- **400 Bad Request**: Request ditolak karena input tidak valid.
- **401 Unauthorized**: Client belum login atau tidak menyertakan token autentikasi.
- **403 Forbidden**: Client login, tetapi tidak punya hak akses ke data tersebut.
- **404 Not Found**: Data atau endpoint tidak ditemukan.
- **405 Method Not Allowed**: Salah menggunakan metode HTTP (misal: rute POST ditembak dengan GET).
- **500 Internal Server Error**: Terjadi error pada kode sistem/server.

### C. Persiapan dan Instalasi API

---

![Diagram API](/screenshoot/pertemuan-9/diagram.jpg)

> **Diagram API**
> Diagram diatas proses kita membuat API untuk CRUD table `Product`, tetapi khusus untuk method `POST`, `EDIT`, dan `DELETE` kita menggunakan _access_token_ yang akan digunakan untuk otentikasi dan otorisasi saat melakukan request.

1. **Tahapan Awal (Setup & Instalasi)**
   Tambahkan `HasApiTokens` pada model `User` untuk mengaktifkan fitur token API Laravel Sanctum.

```php
use Laravel\Sanctum\HasApiTokens;
```

Kemudian jalankan perintah berikut untuk install laravel sanctum:

```bash
php artisan install:api
```

ketika ada pernyataan `One new database migration has been published. Would you like to run all pending database migrations? (yes/no) [yes]:` pilih `yes` untuk menjalankan migrasi yang diperlukan oleh Sanctum.

![Scramble](/screenshoot/pertemuan-9/scramble.png)

> Scramble adalah library untuk dokumentasi API yang terintegrasi dengan Laravel.

setelah menginstall laravel sanctum, kita lanjut menginstall _Scramble_ sebuah library untuk dokumentasi API yang mudah digunakan.

```bash
composer require scramble/scramble
```

Setelah itu, jalankan perintah berikut untuk mempublikasikan file konfigurasi Scramble:

```bash
php artisan vendor:publish --provider="Dedoc\Scramble\ScrambleServiceProvider" --tag="scramble-config"
```

kemudian buka file `AppServiceProvider` kemudian tambahkan kode berikut pada method `boot`.

```php
use Illuminate\Support\Str;
use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Route;

Scramble::configure()
    ->routes(function (Route $route) {
        return Str::startsWith($route->uri, 'api/');
    });
```

Tambahkan juga kode berikut pada method `boot` untuk dokumentasi API bisa dilihat saat production.

```php
Gate::define('viewApiDocs', function () {
    return true;
});
```

2. **Membuat Controller API**
   Pertama kita buat controller API untuk `Product` di dalam folder `Api` dengan perintah berikut:

```bash
php artisan make:controller Api/ProductController --api
```

kemudian buka file `ProductController` yang sudah dibuat, lalu tambahkan kode berikut untuk membuat method `store` menyimpan data.

```php
public function store(StoreProductRequest $request)
{
    try {
        $validated = $request->validated();

        $validated['user_id'] = Auth::id();

        $product = Product::create($validated);

        Log::info('Menambah data produk', [
            'list' => $product
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan!!',
            'data' => $product,
        ], 201);
    } catch (\Throwable $e) {
        Log::error('Error saat menambah product', [
            'message' => $e->getMessage(),
        ]);
    }
}
```

dan method `show` untuk menampilkan data berdasarkan id.

```php
public function show(int $id)
{
    try {
        $product = Product::with('category')->find($id);

        if (!$product)
        {
            return response()->json([
                'message' => 'Product tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => $product
        ], 200);
    } catch (\Throwable $e) {
        Log::error('Gagal mengambil data produk', [
            'message' => $e->getMessage(),
        ]);
    }
```

Tambahkan di file `routes/api.php` untuk menghubungkan route dengan controller yang sudah dibuat.

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/product', [ProductApiController::class, 'store']);
});

Route::get('/product/{id}', [ProductApiController::class, 'show']);
```

Buat *Auth* controller untuk authentikasi dengan nama method `getToken` untuk mendapatkan `access_token`, di folder `Api` dan tambahkan kode berikut:

```php
public function getToken(Request $request)
{
    try {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($data)) {
            Log::info('[Auth - API] Email atau password salah');

            return response()->json([
                'message' => 'Email atau password salah',
            ], 401);
        }
        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('api_token')->plainTextToken;
        Log::info($token);

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    } catch (\Throwable $e) {
        Log::error('Error saat login', [
            'message' => $e->getMessage()
        ]);
    }
}
```

Panggil method diatas di file `routes/api.php`:

```php
Route::post('/login', [AuthController::class, 'getToken']);
```

### Berikut simulasinya
![access_token](/screenshoot/pertemuan-9/login-access-token.png)
> Untuk mendapatkan token API, login sesuai email dan password yg sudah terdaftar di database, lalu copy token untuk digunakan pada header saat melakukan request API `POST`, `EDIT`, dan `DELETE`.

![post-request](/screenshoot/pertemuan-9/post-request.png)
> Saat melakukan request API `POST`, `EDIT`, dan `DELETE` pastikan menyertakan token API pada header dengan format `Authorization: Bearer {token}` untuk otentikasi dan otorisasi akses ke endpoint tersebut. Dan pastikan juga terdapat data `Category` supaya bisa menyimpan data.

### Tugas Praktikum
1. Buat API untuk `Category` dengan method `POST`, `GET`, `PUT`, dan `DELETE` dengan otentikasi menggunakan token API.
2. Lengkapi method pada `ProductApiController` *GET*, *PUT*, dan *DELETE*.
