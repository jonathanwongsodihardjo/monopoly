<?php

declare(strict_types=1);

namespace Monopoly\Game\Space;

use Monopoly\Game\Player;

abstract class Space
{
    /**
     * @param string $name
     */
    public function __construct(
        public private(set) readonly string $name
    ) {}

    // Every space MUST implement how a player interacts with it
    abstract public function landOn(Player $player): void;
}