<?php

declare(strict_types=1);

use Monopoly\Exception\BankruptException;
use Monopoly\Game\Player;
use Monopoly\Game\Space\Property;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    /**
     * @param string $name
     * @param string $price
     * @param string $rent
     * @param int $ownedAllColourGroupMultiplier
     * @param string $colour
     * @param Player|null $owner
     * @param bool $expected
     * @return void
     */
    #[DataProvider('provideTestDataForTestIsOwned')]
    public function testIsOwned(string $name, string $price, string $rent, int $ownedAllColourGroupMultiplier, string $colour, ?Player $owner, bool $expected): void
    {
        $this->assertSame($expected, new Property($name, $price, $rent, $ownedAllColourGroupMultiplier, $colour, $owner)->isOwned());
    }

    /**
     * @return array
     */
    public static function provideTestDataForTestIsOwned(): array
    {
        return [
            'unowned property' => ['name', '100', '50', 2, 'colour', null, false],
            'owned property' => ['name', '100', '50', 2, 'colour', new Player('Test', '100', 0), true],
        ];
    }

    /**
     * @param Closure $dataFactory
     * @return void
     * @throws BankruptException
     */
    #[DataProvider('provideTestDataForTestLandOn')]
    public function testLandOn(Closure $dataFactory): void
    {
        [
            'name' => $name,
            'price' => $price,
            'rent' => $rent,
            'ownedAllColourGroupMultiplier' => $ownedAllColourGroupMultiplier,
            'colour' => $colour,
            'owner' => $owner,
            'player' => $player,
            'otherProperties' => $properties,
            'performAssertions' => $performAssertions
        ] = $dataFactory();
        $property = new Property($name, $price, $rent, $ownedAllColourGroupMultiplier, $colour, $owner);
        $property->setOtherPropertiesInTheSameColourGroup($properties);
        $property->landOn($player);
        $performAssertions($this, $property);
    }

    /**
     * @return array
     */
    public static function provideTestDataForTestLandOn(): array
    {
        return [
            'land on unowned property and have enough money' => [
                static function () {
                    $player = new Player('Test', '105', 0);
                    return [
                        'name' => 'name',
                        'price' => '100',
                        'rent' => '50',
                        'ownedAllColourGroupMultiplier' => 2,
                        'colour' => 'green',
                        'owner' => null,
                        'player' => $player,
                        'otherProperties' => [new Property('name', '150', '75', 2, 'green')],
                        'performAssertions' => static function(TestCase $testCase, Property $property) use ($player): void
                        {
                            $testCase->assertTrue($property->isOwned());
                            $testCase->assertSame('5', $player->money);
                        },
                    ];
                },
            ],
            'land on owned property and have enough money to pay rent' => [
                static function () {
                    $owner = new Player('owner', '100', 1);
                    $player = new Player('Test', '60', 0);
                    return [
                        'name' => 'name',
                        'price' => '100',
                        'rent' => '50',
                        'ownedAllColourGroupMultiplier' => 2,
                        'colour' => 'green',
                        'owner' => $owner,
                        'player' => $player,
                        'otherProperties' => [new Property('name', '150', '75', 2, 'green')],
                        'performAssertions' => static function(TestCase $testCase) use ($player, $owner): void
                        {
                            $testCase->assertSame('150', $owner->money);
                            $testCase->assertSame('10', $player->money);
                        },
                    ];
                },
            ],
            'land on a player which have all the colours group and have enough money to pay rent' => [
                static function () {
                    $owner = new Player('owner', '100', 1);
                    $player = new Player('Test', '500', 0);
                    return [
                        'name' => 'name',
                        'price' => '100',
                        'rent' => '50',
                        'ownedAllColourGroupMultiplier' => 2,
                        'colour' => 'green',
                        'owner' => $owner,
                        'player' => $player,
                        'otherProperties' => [new Property('name', '150', '75', 2, 'green', $owner)],
                        'performAssertions' => static function(TestCase $testCase) use ($player, $owner): void
                        {
                            $testCase->assertSame('200', $owner->money);
                            $testCase->assertSame('400', $player->money);
                        },
                    ];
                },
            ],
        ];
    }

    /**
     * @return void
     */
    public function testLandOnCausesBankruptcy(): void
    {
        $owner = new Player('owner', '100', 1);
        $player = new Player('Test', '10', 0);
        $property = new Property('name', '100', '50', 2, 'green', $owner);
        $property->setOtherPropertiesInTheSameColourGroup([new Property('other', '150', '75', 2, 'green')]);
        $this->expectException(BankruptException::class);
        $this->expectExceptionMessageIs("Player Test is bankrupt");
        $property->landOn($player);
    }
}