### System design considerations:
1) Decoupling payment and shipping services. Using asynchronous workflow by using queue.
     - Its necessary because a failure or latency spike in a third-party shipping API should not block customers from completing checkouts and paying for goods
2) Extensive logging
3) Sufficient error handling, handling edge cases.

### UI
Just go to main page (`/`) where dashboard is located

### Note
 - shipping api is done through serverless php function
 - Reload the page manually to see invoices and log changes

### Installation

```bash
mv .env.example .env

cd docker
mv .env.example .env

# here adjust port of the caddy webserver if your 80 port is occupied
docker compose up -d

docker exec -it einstoffen-php-1 bash

# now everything inside php container
composer install

chown -R www-data:www-data /var/www/html/writable
chmod -R 755 /var/www/html/writable

php spark migrate
```
