<?php

declare(strict_types=1);

use Monopoly\Command\MonopolyCommand;
use Monopoly\Game\Game;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MonopolyCommandTest extends TestCase
{
    /**
     * @return void
     * @throws Throwable
     */
    public function testCommandExecutesGameSuccessfully(): void
    {
        // 1. Create a mock of the Game class
        $mockGame = $this->createMock(Game::class);

        // 2. Set an expectation that $game->run() will be called once with specific args
        $mockGame->expects($this->once())
            ->method('run')->willReturn("Runs Successfully.");

        ob_start();
        // 3. Inject the mock into the command via constructor promotion
        new MonopolyCommand($mockGame)->run(['bin/monopoly', 'board.json', 'rolls_1.json']);
        $this->assertStringContainsString('Runs Successfully', ob_get_clean());
    }

    /**
     * @param string $boardInput
     * @param string $rollInput
     * @param string $expectedExceptionClass
     * @param string $expectedExceptionMessage
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    #[DataProvider('provideTestDataForTestCommandInvalidInputFile')]
    public function testCommandInvalidInputFile(string $boardInput, string $rollInput, string $expectedExceptionClass, string $expectedExceptionMessage): void
    {
        // 1. Create a mock of the Game class
        $mockGame = $this->createMock(Game::class);

        $mockGame->expects($this->never())->method('run');

        // 3. Inject the mock into the command via constructor promotion
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessageIsOrContains($expectedExceptionMessage);
        new MonopolyCommand($mockGame)->run(['bin/monopoly', $boardInput, $rollInput]);
    }

    /**
     * @return array
     */
    public static function provideTestDataForTestCommandInvalidInputFile(): array
    {
        return [
            'invalid non readable board input file' => [
                'boardInput' => '/etc/random',
                'rollInput' => 'rolls_1.json',
                'expectedExceptionClass' => InvalidArgumentException::class,
                'expectedExceptionMessage' => "Input file '/etc/random' does not exist or is not readable.",
            ],
            'invalid non-valid json roll input file' => [
                'boardInput' => 'board.json',
                'rollInput' => '/etc/hosts',
                'expectedExceptionClass' => InvalidArgumentException::class,
                'expectedExceptionMessage' => "Input file '/etc/hosts' does not contain a valid JSON.",
            ]
        ];
    }
}