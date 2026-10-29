<?php

declare(strict_types=1);

namespace Game;

use BcMath\Number;
use InvalidArgumentException;
use Monopoly\Exception\BankruptException;
use Monopoly\Game\Board;
use Monopoly\Game\Player;
use Monopoly\Game\Space\Go;
use Monopoly\Game\Space\Property;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{
    /**
     * @return void
     * @throws InvalidArgumentException
     * @throws BankruptException
     */
    public function testRollThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Invalid player index 2');
        new Board(16, [], [$this->createMock(Player::class), $this->createMock(Player::class)])->roll(2, 5);
    }

    /**
     * @param Player $player1
     * @param string $expectedPlayerOneMoney
     * @return void
     * @throws BankruptException
     */
    public function testRoll(Player $player1, int $rollAmount, string $expectedPlayerOneMoney): void
    {
        new Board(16, [
            new Go('Go', 1),
            new Property('Property 1', '16', '8', 2, 'green'),
            new Property('Property 2', '11', '5.5', 2, 'green'),
        ],
        [
            $player1,
            new Player('Player 2', '16', 0),
        ])->roll(0, $rollAmount);
        $this->assertSame(0, new Number($player1->money)->compare(new Number($expectedPlayerOneMoney)));
    }

    /**
     * @return array
     */
    public static function provideTestDataForTestRoll(): array
    {
        return [
            'player one lands on go' => [
                new Player('Player 1', '16', 2),
                1,
                '17',
            ],
            'player one lands on property 2 but passes through go' => [
                new Player('Player 1', '16', 2),
                3,
                '6',
            ],
        ];
    }
}