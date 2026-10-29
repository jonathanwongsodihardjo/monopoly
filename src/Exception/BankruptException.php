<?php

declare(strict_types=1);

namespace Monopoly\Exception;

use Exception;
use Monopoly\Game\Player;
use Throwable;

class BankruptException extends Exception
{
    /**
     * @param Player $player
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(Player $player, int $code = 0, ?Throwable $previous = null)
    {
        $message = "Player $player->name is bankrupt";
        parent::__construct($message, $code, $previous);
    }
}