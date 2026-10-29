<?php

declare(strict_types=1);

namespace Monopoly\Game;

use InvalidArgumentException;
use Monopoly\Exception\BankruptException;
use Monopoly\Game\Space\Space;

class Board
{
    /**
     * @param int $income
     * @param Space[] $boardData
     * @param Player[] $players
     */
    public function __construct(
        private readonly int $income,
        private readonly array $boardData,
        private(set) readonly array $players = []
    ) {}

    /**
     * @param int $playerIndex
     * @param int $rollAmount
     * @return void
     * @throws InvalidArgumentException
     * @throws BankruptException
     */
    public function roll(int $playerIndex, int $rollAmount): void
    {
        if (!($this->players[$playerIndex] ?? null) instanceof Player) {
            throw new InvalidArgumentException("Invalid player index $playerIndex");
        }

        // Calculate new position with board wrap-around
        $oldPosition = $this->players[$playerIndex]->position;
        $newPosition = ($oldPosition + $rollAmount) % count($this->boardData);

        // Player has passed through go, collect income
        if ($newPosition > 0 && $newPosition < $oldPosition) {
            $this->players[$playerIndex]->receive((string)$this->income);
        }
        $this->players[$playerIndex]->moveTo($newPosition);
        $this->boardData[$newPosition]->landOn($this->players[$playerIndex]);
    }
}