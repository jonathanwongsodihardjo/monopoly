<?php

declare(strict_types=1);

use Monopoly\Exception\BankruptException;
use Monopoly\Game\Player;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /**
     * @return void
     * @throws BankruptException
     */
    public function testPay(): void
    {
        $player = new Player('Test', '100', 0);
        $player->pay('10');
        $this->assertSame('90', $player->money);
    }

    /**
     * @param Player $player
     * @param string $payAmount
     * @param string $exceptionClass
     * @param string $exceptionMessage
     * @return void
     * @throws BankruptException
     */
    #[DataProvider('provideTestDataForTestPayThrowsException')]
    public function testPayThrowsException(Player $player, string $payAmount, string $exceptionClass, string $exceptionMessage): void
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessageIs($exceptionMessage);
        $player->pay($payAmount);
    }

    /**
     * @return array
     */
    public static function provideTestDataForTestPayThrowsException(): array
    {
        return [
            'player is bankrupt' => [
                new Player('Test', '100', 0),
                '100',
                BankruptException::class,
                'Player Test is bankrupt',
            ],
            'invalid pay amount' => [
                new Player('Test', '100', 0),
                '-10',
                InvalidArgumentException::class,
                'Payment amount must be positive.',
            ],
        ];
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function testReceive(): void
    {
        $player = new Player('Test', '100', 0);
        $player->receive('10');
        $this->assertSame('110', $player->money);
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function testReceiveThrowsException(): void
    {
        $player = new Player('Test', '100', 0);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Receive amount must be positive.');
        $player->receive('0');
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function testMoveTo(): void
    {
        $player = new Player('Test', '100', 0);
        $player->moveTo(1);
        $this->assertSame(1, $player->position);
    }

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function testMoveToThrowsException(): void
    {
        $player = new Player('Test', '100', 0);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Move to position must be positive or zero.');
        $player->moveTo(-1);
    }
}