<?php

declare(strict_types=1);

namespace Monopoly\Game;

use BcMath\Number;
use InvalidArgumentException;
use Monopoly\Exception\BankruptException;
use Monopoly\Game\Space\Go;
use Monopoly\Game\Space\Property;
use Monopoly\Game\Space\Space;

class Game
{
    /**
     * @param Config|null $config
     */
    public function __construct(
        private ?Config $config = null,
    ) {
        $this->config = $config ?? new Config();
    }

    /**
     * @param array $board
     * @param array $rolls
     * @param bool $isJsonOutput
     * @return string
     */
    public function run(array $board, array $rolls, bool $isJsonOutput = false): string
    {
        $boardData = $this->parseBoard($board);
        $players = $this->getPlayers();

        $board = new Board($this->config->goSalary, $boardData, $players);

        $currentPlayerIndex = 0;
        foreach ($rolls as $roll) {
            try {
                $board->roll($currentPlayerIndex, $roll);
                $currentPlayerIndex = ($currentPlayerIndex + 1) % count($players);
            } catch (BankruptException) {
                // Current player has gone bankrupt. Ends the game here.
                break;
            }
        }

        return $isJsonOutput ? json_encode($this->formatOutput($players, $boardData), JSON_PRETTY_PRINT) : print_r($this->formatOutput($players, $boardData), true);
    }

    /**
     * @param array $board
     * @return Space[]
     */
    private function parseBoard(array $board): array
    {
        return $this->groupBoardBasedOnColours(
            array_map(fn($data) => match ($data['type'] ?? 'unknown') {
                'go' => new Go($data['name'], $this->config->goSalary),
                'property' => new Property($data['name'], (string)$data['price'], (string)(new Number((string)$this->config->baseRentMultiplier)->mul(new Number((string)$data['price']))), $this->config->ownedAllColourGroupMultiplier, $data['colour']),
                default => throw new InvalidArgumentException("Unknown space type: " . ($data['type'] ?? 'none')),
            }, $board)
        );
    }

    /**
     * @param Space[] $boardData
     * @return array
     */
    private function groupBoardBasedOnColours(array $boardData): array
    {
        $properties = array_values(
            array_filter($boardData, fn($space) => $space instanceof Property)
        );

        $groupedByColour = [];
        /**
         * @var Property $property
         */
        foreach ($properties as $property) {
            $groupedByColour[$property->colour][] = $property;
        }

        foreach ($properties as $property) {
            // Filter out the current property itself so it only contains *others*
            $otherProperties = array_values(
                array_filter(
                    $groupedByColour[$property->colour],
                    fn($p) => $p !== $property
                )
            );

            $property->setOtherPropertiesInTheSameColourGroup($otherProperties);
        }

        return $boardData;
    }

    /**
     * @return array
     */
    private function getPlayers(): array
    {
        return array_map(fn($playerName) => new Player($playerName, (string)$this->config->startingSalary, 0), $this->config->players);
    }

    /**
     * @param Player[] $players
     * @param Space[] $boardData
     * @return array
     */
    private function formatOutput(array $players, array $boardData): array
    {
        $updatedPlayers = array_map(
            fn($player) => [
                'name' => $player->name,
                'money' => $player->money,
                'position' => $boardData[$player->position]->name,
            ],
            $players
        );

        usort($updatedPlayers, fn($playerA, $playerB) => new Number($playerB['money'])->compare(new Number($playerA['money'])));

        return [
            'winners' => array_values(
                array_map(
                    fn($player) => $player['name'],
                    array_filter($updatedPlayers, fn($player) => $player['money'] === $updatedPlayers[0]['money'])
                )
            ),
            'players' => $updatedPlayers,
        ];
    }
}