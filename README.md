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
1. Install Laravel Herd: https://github.com/laravel/herd
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
5. A dice roll is a random number between 1 and 6

## Rolls_3
- I aimed to create a 10-roll scenario where all the players have the same amount of money, so all of them are the winnners
- This scenario is prepared in order to test the game's ability to handle multiple winners.
- My expectations were that all players would end up with the same amount of money after the rolls, and that the game would correctly identify all players as winners.
- My expectations were met, and all players ended up with the same amount of money after the rolls, and the game correctly identified all players as winners.
- Output (JSON):
```shell
Running Monopoly simulation...
{
    "winners": [
        "Peter",
        "Billy",
        "Charlotte",
        "Sweedal"
    ],
    "players": [
        {
            "name": "Peter",
            "money": "11.0",
            "position": "Gami Chicken"
        },
        {
            "name": "Billy",
            "money": "11.0",
            "position": "Massizim"
        },
        {
            "name": "Charlotte",
            "money": "11.0",
            "position": "Gami Chicken"
        },
        {
            "name": "Sweedal",
            "money": "11.0",
            "position": "Massizim"
        }
    ]
}
Simulation completed successfully!
```

## Rolls_1 Output (JSON)
```shell
Running Monopoly simulation...
{
    "winners": [
        "Peter"
    ],
    "players": [
        {
            "name": "Peter",
            "money": "47.5",
            "position": "Lanzhou Beef Noodle"
        },
        {
            "name": "Billy",
            "money": "9.0",
            "position": "Gami Chicken"
        },
        {
            "name": "Charlotte",
            "money": "3.5",
            "position": "Betty's Burgers"
        },
        {
            "name": "Sweedal",
            "money": "1.0",
            "position": "Gami Chicken"
        }
    ]
}
Simulation completed successfully!
```

## Rolls_2 Output (JSON)
```shell
Running Monopoly simulation...
{
    "winners": [
        "Charlotte"
    ],
    "players": [
        {
            "name": "Charlotte",
            "money": "38.0",
            "position": "GO"
        },
        {
            "name": "Billy",
            "money": "25.0",
            "position": "Gami Chicken"
        },
        {
            "name": "Peter",
            "money": "9.0",
            "position": "Massizim"
        },
        {
            "name": "Sweedal",
            "money": "-1.0",
            "position": "Gami Chicken"
        }
    ]
}
Simulation completed successfully!
```