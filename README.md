Instalasi Program BE Sejiwa
1. Lakukan command composer install
2. Copy .env.example menjadi .env 
3. Ubah .env sesuai dengan pengaturan database anda
4. Lakukan command php artisan key:generate
5. Lakukan command php artisan migrate --seed
6. Lakukan command php artisan storage:link
7. Lakukan command php artisan jwt:secret

Untuk mencoba API berada di route /api-doc