# Monopoly Simulator

## Full Installation (Includes Automated Testing)
1. Install Laravel Herd: https://github.com/laravel/herd
2. Install PHP version >= 8.4
3. Composer package management should be automatically installed
4. Install PHPUnit: `composer install`
5. Running automated tests: `/vendor/bin/phpunit tests --testdox`
6. Running script: `php bin/monopoly <BOARD_FILE> <ROLL_FILE>`
7. Running script with JSON Output: `php bin/monopoly <BOARD_FILE> <ROLL_FILE> -j` or `php bin/monopoly <BOARD_FILE> <ROLL_FILE> --json`

## Normal Installation (Install only required items)
1. Install Laravel Herd: 1. Install Laravel Herd: https://github.com/laravel/herd
2. Install PHP version >= 8.4
3. Composer package management should be automatically installed
4. Generate auto-load files via: `composer install --no-dev`
5. Running script: `php bin/monopoly <BOARD_FILE> <ROLL_FILE>`
6. Running script with JSON Output: `php bin/monopoly <BOARD_FILE> <ROLL_FILE> -j` or `php bin/monopoly <BOARD_FILE> <ROLL_FILE> --json`

## Design Decisions
1. Exceptions are not caught except for `BankruptException`, to reduce code boilerplate
2. Created a `Config` class to contain all the constants required to configure the game, instead of attempting to read from an `.env` file to speed up development time.
3. Created an abstract class `Space` to reduce code boilerplate and allow `Go` and `Property` to extend it.

## Assumptions
1. A player who doesn't have enough money to purchase a property they landed on won't purchase the property to avoid bankruptcy
2. Rent price for each property is not mentioned in the `board.json`, I have assumed a flat 0.5 multiplier of the actual property price to charge as rent
3. A property colour group must have at least 2 properties, it cannot be just one property
4. Winners are the player(s) with the highest amount of money. There can be multiple winners as long as they have the same amount 

## Rolls_3