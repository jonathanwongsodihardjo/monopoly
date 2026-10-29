<?php

declare(strict_types=1);

namespace Monopoly\Game\Space;

use Monopoly\Game\Player;

class Go extends Space
{
    /**
     * @param string $name
     * @param int $income
     */
    public function __construct(
        string               $name,
        private readonly int $income
    ) {
        parent::__construct($name);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function landOn(Player $player): void
    {
        $player->receive((string)$this->income);
    }
}