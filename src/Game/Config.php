<?php

declare(strict_types=1);

namespace Monopoly\Game;

readonly class Config
{
    /**
     * @param int $goSalary
     * @param float $baseRentMultiplier
     * @param array $players
     * @param int $startingSalary
     * @param int $ownedAllColourGroupMultiplier
     */
    public function __construct(
        public int   $goSalary = 1,
        public float $baseRentMultiplier = 0.5,
        public array $players = ['Peter', 'Billy', 'Charlotte', 'Sweedal'],
        public int   $startingSalary = 16,
        public int   $ownedAllColourGroupMultiplier = 2
    ) {}
}