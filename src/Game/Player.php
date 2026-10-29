<?php

declare(strict_types=1);

namespace Monopoly\Game;

use BcMath\Number;
use InvalidArgumentException;
use Monopoly\Exception\BankruptException;

class Player
{
    /**
     * @param string $name
     * @param string $money
     * @param int $position
     */
    public function __construct(
        public private(set) readonly string $name,
        public private(set) string          $money,
        public private(set) int             $position,
    ) {}

    /**
     * @param string $amount
     * @return void
     * @throws InvalidArgumentException
     * @throws BankruptException
     */
    public function pay(string $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Payment amount must be positive.");
        }

        $totalMoney = new Number($this->money)->sub(new Number($amount));
        $lessThanOrEqualToZero = $totalMoney->compare(new Number('0'));
        $this->money = (string)$totalMoney;
        if ($lessThanOrEqualToZero === 0 || $lessThanOrEqualToZero === -1) {
            throw new BankruptException($this);
        }
    }

    /**
     * @param string $amount
     * @return void
     * @throws InvalidArgumentException
     */
    public function receive(string $amount): void
    {
        $amountNumber = new Number($amount);
        $lessThanOrEqualToZero = $amountNumber->compare(new Number('0'));
        if ($lessThanOrEqualToZero === 0 || $lessThanOrEqualToZero === -1) {
            throw new InvalidArgumentException("Receive amount must be positive.");
        }

        $this->money = (string)new Number($this->money)->add($amountNumber);
    }

    /**
     * @param int $position
     * @return void
     */
    public function moveTo(int $position): void
    {
        if ($position < 0) {
            throw new InvalidArgumentException("Move to position must be positive or zero.");
        }

        $this->position = $position;
    }
}