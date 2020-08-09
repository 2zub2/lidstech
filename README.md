# lidstech

Run:
```bash
composer install

docker build -t lead .

mkdir ~/leadlog

docker run --rm -v ~/leadlog/:/usr/src/lead/runtime lead
```