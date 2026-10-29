<?php

declare(strict_types=1);

namespace Game;

use InvalidArgumentException;
use Monopoly\Game\Board;
use Monopoly\Game\Game;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    /**
     * @return void
     */
    public function testRunThrowsExceptionWhenParsingBoardData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Unknown space type: lorem');
        new Game(null, $this->createMock(Board::class))
            ->run(
                [
                    ["name" => "GO", "type" => "go"],
                    ["name" => "The Burvale", "price" => 1, "colour" => "Brown", "type" => "property"],
                    ["name" => "Lorem", "price" => 10, "colour" => "Brown", "type" => "lorem"],
                ],
                [1,2,3,4],
                true
            );
    }

    /**
     * @return void
     *
     */
    public function testRun(): void
    {
        $output = json_decode(new Game()
            ->run(
                [
                    ["name" => "GO", "type" => "go"],
                    ["name" => "The Burvale", "price" => 1, "colour" => "Brown", "type" => "property"],
                    ["name" => "Lorem", "price" => 10, "colour" => "Brown", "type" => "property"],
                ],
                [1,2,3,4],
                true
            ),
            true
        );
        $this->assertIsArray($output);
        $this->assertSame('Charlotte', $output['winners'][0] ?? '');
    }

}