# lidstech

Environment variable:

TIMEOUT - маскимальное время выполнения

LEADS_NUM - кол-во генерируемых лидов

Run (create log.txt in cur dir):
```bash
composer install

docker build -t lead .

docker run --rm -e TIMEOUT=30 -e LEADS_NUM=10 -v $(pwd):/usr/src/lead/runtime lead
```

Windows run:

sh
```bash
composer install

docker build -t lead .

MSYS_NO_PATHCONV=1 docker run --rm -e TIMEOUT=30 -e LEADS_NUM=10 -v $(pwd):/usr/src/lead/runtime lead
```

cmd
```cmd
composer install

docker build -t lead .

docker run --rm -e TIMEOUT=30 -e LEADS_NUM=10 -v %cd%:/usr/src/lead/runtime lead
```

