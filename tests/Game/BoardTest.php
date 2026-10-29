<?php

declare(strict_types=1);

namespace Game;

use BcMath\Number;
use Closure;
use InvalidArgumentException;
use Monopoly\Exception\BankruptException;
use Monopoly\Game\Board;
use Monopoly\Game\Player;
use Monopoly\Game\Space\Go;
use Monopoly\Game\Space\Property;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{
    /**
     * @param Closure $playerFactory
     * @param int $playerIndex
     * @param int $rollAmount
     * @param string $expectedExceptionClass
     * @param string $expectedExceptionMessage
     * @return void
     * @throws BankruptException
     * @throws Exception
     */
    #[DataProvider('provideTestDataForTestRollThrowsException')]
    public function testRollThrowsException(Closure $playerFactory, int $playerIndex, int $rollAmount, string $expectedExceptionClass, string $expectedExceptionMessage): void
    {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessageIs($expectedExceptionMessage);
        new Board(16, [], $playerFactory($this))->roll($playerIndex, $rollAmount);
    }

    /**
     * @return array
     */
    public static function provideTestDataForTestRollThrowsException(): array
    {
        return [
            'invalid player index' => [
                fn(TestCase $testCase) => [$testCase->createMock(Player::class), $testCase->createMock(Player::class)],
                2,
                5,
                InvalidArgumentException::class,
                'Invalid player index 2',
            ],
            'invalid roll amount' => [
                fn(TestCase $testCase) => [$testCase->createMock(Player::class), $testCase->createMock(Player::class)],
                1,
                0,
                InvalidArgumentException::class,
                'Invalid roll amount 0',
            ]
        ];
    }

    /**
     * @param Player $player1
     * @param int $rollAmount
     * @param string $expectedPlayerOneMoney
     * @return void
     * @throws BankruptException
     */
    #[DataProvider('provideTestDataForTestRoll')]
    public function testRoll(Player $player1, int $rollAmount, string $expectedPlayerOneMoney): void
    {
        new Board(1, [
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