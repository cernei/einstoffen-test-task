### System design considerations:
1) Decoupling payment and shipping services. Using asynchronous workflow by using **queue**.
     - Its necessary because a failure or latency spike in a third-party shipping API should not block customers from completing checkouts and paying for goods
2) Logic against duplicate webhooks, transaction safety, and race conditions 

### Where to start? How to test?
 - Just go to main page (`/`) where dashboard is located
 - Left button `Send batch of invoices` sends invoices with different test cases (`comment` attribute).
 - Right button `Start` runs **two** workers through api, that handle invoices to shipping api:
   ```bash
   queue:work shipments -max-jobs 1 --stop-when-empty
   ```
 - Center button clears everything for repeated tests.

#### Why run workers manually?
 - Easier to set up and run, compared to cron daemon
 - Running two concurrent `fetch` request tests the system against issues of **race conditions**
    
### Notes
 - 3d party shipping api is done through serverless php function
 - Invoice payload body has obligatory `comment` which contains case for mock api
 - Invoice web hook returns http code `200` on existent invoice ID

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

php spark migrate --all
```
