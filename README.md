## Stock Market Demo App

## Setting up the application

There are two options in order to set up the application.

### First Option

#### Requirements
<ul>
<li>PHP >= 8.3</li>
</ul>

If you use some local development tool like Laravel Herd or Laravel Valet you can simply `cd` into the directory you cloned the project and run the commands below.

`cp .env.example .env`

`composer install`

`npm install && npm run build`

`php artisan key:generate`

`php artisan migrate`

When running `php artisan migrate` you will be asked if you want to create an sqlite database. Select `Yes` in order to create it.

In the `.env` file you created, you have to change the variables `APP_URL` to match your application's url and the `REDIS_HOST` to `127.0.0.1`, as they are already predefined for use with docker. 

If you chose to get started with this option, then you have to manually start Laravel Horizon in order for the queues to work and also manually run the scheduler using the commands below.

`php artisan horizon`

`php artisan schedule:run`


### Second Option
If you are a fan of docker, after cloning the repo you can run the commands below in order to set the project up and running.

`cp .env.example .env`

`cd docker`

`docker-compose build`

`docker-compose up`

Migrations will be forced, Laravel Horizon and Laravel Scheduler will start automatically with supervisor.

After all containers are up, you will see the messages below that indicate that all processes are up.

`stock-app-php    | 2024-03-02 22:13:05,914 INFO success: cron entered RUNNING state, process has stayed up for > than 1 seconds (startsecs)`

`stock-app-php    | 2024-03-02 22:13:05,914 INFO success: horizon entered RUNNING state, process has stayed up for > than 1 seconds (startsecs)`

`stock-app-php    | 2024-03-02 22:13:05,914 INFO success: php-fpm entered RUNNING state, process has stayed up for > than 1 seconds (startsecs)`

If you want to attach to the container run `docker exec -it stock-app-php bash` and then change directory using `cd ../sites`.

### Finally

Go to your application's url and you will see the Laravel welcome page. 

There are two endpoints.

`/stocks` returns a json response.

`/stocks-table` returns the data presented by a table using Laravel Livewire, while polling results every 60s to feel more real-time. 

No authentication is implemented for these two endpoints because the project is for demo purposes only.

In the `.env` file you created, you have to provide `STOCK_API_KEY` in order for the stock prices to be updated. You can obtain an API key from https://www.alphavantage.co

If you want to manually run the stock update console command  and not rely on the Laravel Scheduler, you can run `php artisan app:get-stock-prices-from-alpha-vantage-api`

## TESTS
`./vendor/bin/pest`

## Some thoughts behind the application structure

The main goal was to integrate with the Alpha Vantage API. For that purpose `AlphaVantageApiServiceProvider` was created along with `AlphaVantageApiService` class and the `AlphaVantageApi` facade, in order to be the starting point for the api communication.

The communication between the app and the Alpha Vantage API is initiated through the `GetStockPricesFromAlphaVantageApi` console command which dispatches, every minute, the `UpdateStockPrices` job in order for the update to happen in the background.

The  `UpdateStockPrices` job fetches the predefined stocks from the database using Laravel's query builder. We use query builder in order to chunk the results and then the `AlphaVantageApiService` communicates with Alpha Vantage API with the Laravel's `Htpp` facade. But in order to be more efficient we create concurrent requests by using the `pool` method of the `Htpp` facade

After we fetch the results we use `AlphaVantageResponseDto` class in order to format the response in a more readable way, and then update the database.

All errors are handled and even if the process fails, the user will just see empty results, when hitting the endpoints, instead of errors. All errors are logged to `laravel.log`.

## License

[MIT license](https://opensource.org/licenses/MIT).
