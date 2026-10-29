<?php

declare(strict_types=1);

namespace Monopoly\Game\Space;

use BcMath\Number;
use Monopoly\Exception\BankruptException;
use Monopoly\Game\Player;

class Property extends Space
{
    /**
     * @var Property[]
     */
    private array $otherPropertiesInTheSameColourGroup = [];

    /**
     * @param string $name
     * @param int $price
     * @param int $rent
     * @param int $ownedAllColourGroupMultiplier
     * @param string $colour
     * @param Player|null $owner
     */
    public function __construct(
        string                              $name,
        private readonly string             $price,
        private readonly string             $rent,
        private readonly int                $ownedAllColourGroupMultiplier,
        public private(set) readonly string $colour,
        public private(set) ?Player         $owner = null, // Starts unowned (null)
    ) {
        parent::__construct($name);
    }

    /**
     * @return bool
     */
    public function isOwned(): bool
    {
        return $this->owner !== null;
    }

    /**
     * @param Player $owner
     * @return void
     * @codeCoverageIgnore
     */
    public function setOwner(Player $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @param Property[] $properties
     * @return void
     * @codeCoverageIgnore
     */
    public function setOtherPropertiesInTheSameColourGroup(array $properties): void
    {
        $this->otherPropertiesInTheSameColourGroup = $properties;
    }

    /**
     * @param Player $player
     * @return void
     * @throws BankruptException
     */
    public function landOn(Player $player): void
    {
        $playerMoney = new Number($player->money);
        $price = new Number($this->price);
        $greaterThanOrEqualTo = $playerMoney->compare($price);
        if (!$this->isOwned() && $greaterThanOrEqualTo >= 0) {
            // Logic: Player buys the property if they have enough money
            $player->pay((string)$this->price);
            $this->setOwner($player);
        } elseif ($this->isOwned() && $this->owner !== $player) {
            // Logic: Property is owner, player must pay rent to owner
            $rentAmount = $this->doesPlayerOwnsAllColourGroup() ? (string)(new Number((string)$this->ownedAllColourGroupMultiplier)->mul(new Number($this->rent))) : $this->rent;
            $player->pay($rentAmount);
            $this->owner->receive($rentAmount);
        }
    }

    /**
     * @return bool
     */
    private function doesPlayerOwnsAllColourGroup(): bool
    {
        return count($this->otherPropertiesInTheSameColourGroup) === count(array_filter($this->otherPropertiesInTheSameColourGroup, fn($p) => $p->owner === $this->owner));
    }
}