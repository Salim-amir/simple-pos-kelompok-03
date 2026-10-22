# Pengukuran Memori: get() vs lazyById()

Dataset: sekitar 52.500 row pada tabel `transactions`.

| Cara baca data | Peak memory (MB) |
|---|---|
| `Transaction::lazyById()` | 22.2 |
| `Transaction::get()` | 80.6 |

Kesimpulan kelompok: Penggunaan `Transaction::lazyById()` jauh lebih hemat memori dibandingkan `Transaction::get()` saat memproses data dalam jumlah besar. Metode `get()` mengambil dan memuat seluruh data ke dalam memori sekaligus sebagai koleksi objek Eloquent sehingga menyebabkan lonjakan memori (mencapai 80.6 MB). Sebaliknya, `lazyById()` membaca data secara bertahap (*chunking* berbasis ID) sehingga memori tetap stabil dan rendah (hanya 22.2 MB). Untuk kebutuhan pemrosesan data berjumlah banyak seperti ekspor laporan atau batch processing, `lazyById()` sangat disarankan untuk mencegah terjadinya *memory exhausted* (*out of memory*).
