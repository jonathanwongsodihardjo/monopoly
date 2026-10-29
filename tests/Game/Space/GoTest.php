<?php

declare(strict_types=1);

use Monopoly\Game\Player;
use Monopoly\Game\Space\Go;
use PHPUnit\Framework\TestCase;

class GoTest extends TestCase
{
    /**
     * @return void
     */
    public function testLandOn(): void
    {
        $mockPlayer = $this->createMock(Player::class);
        $mockPlayer->expects($this->once())->method('receive')->with(200);
        new Go('Go', 200)->landOn($mockPlayer);
    }
}