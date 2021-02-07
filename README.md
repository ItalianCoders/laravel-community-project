<p align="center">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
</p>

## About the Project
We are going to build something amazing together.

## Run the Project
1. Copy file `.env.example` to `.env`, configure your database params
2. Install project dependencies running `composer install` command
3. Run migration running `php artisan migrate` command
4. Generate application encryption key running `php artisan key:generate` command
5. You can run the project using the php builtin webserver running `php artisan serve` command

## Run Test Cases
You can run test cases invoking the following command:
 - Using artisan `php artisan test`
 - Or calling phpunit directly `vendor/bin/phpunit`

You can also generate a code coverage html report with the following command:
```
vendor/bin/phpunit --coverage-html reports/
```
The report will be stored in `reports` folder on project root directory.

If you got a message like `No code coverage driver is available` maybe you should install Xdebug on your computer.

## Contributing
Join us on [Discord](https://s.italiancoders.it/discord) under the channel #laravel-prj.
Feel free to make your pull request (please use the [Conventional Commit](https://www.conventionalcommits.org/) specification, for your commits).

## License
The software is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
