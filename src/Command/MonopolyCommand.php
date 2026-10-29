<?php

declare(strict_types=1);

namespace Monopoly\Command;

use InvalidArgumentException;
use Monopoly\Game\Game;
use RuntimeException;

readonly class MonopolyCommand
{
    /**
     * @param Game|null $game
     * @return void
     */
    public function __construct(
        private ?Game $game = null
    ) {}

    /**
     * @param array $argv
     * @return void
     */
    public function run(array $argv): void
    {
        ['showJson' => $showJson, 'boardPath' => $boardPath, 'rollsPath' => $rollsPath] = $this->validateAndParseArguments($argv);

        $game = $this->game ?? new Game();
        echo 'Running Monopoly simulation...', PHP_EOL;
        $gameOutput = $game->run($this->verifyInputFileAndParseInputFile($boardPath), $this->verifyInputFileAndParseInputFile($rollsPath), $showJson);
        echo $gameOutput, ($showJson ? PHP_EOL : '');
        echo 'Simulation completed successfully!', PHP_EOL;
    }

    /**
     * @param array $argv
     * @return array
     */
    private function validateAndParseArguments(array $argv): array
    {
        $showJson = false;

        // Parse arguments manually
        $args = array_slice($argv, 1); // Skip script name
        $positionalArgs = [];

        foreach ($args as $arg) {
            if ($arg === '--json' || $arg === '-j') {
                $showJson = true;
            } elseif (str_starts_with($arg, '-')) {
                // Handle unknown options if needed
                throw new RuntimeException("Unknown option: $arg");
            } else {
                $positionalArgs[] = $arg;
            }
        }

        // Validate required arguments
        if (count($positionalArgs) < 2) {
            echo 'Error: Missing required arguments.', PHP_EOL;
            echo "Usage: php simulate.php <board.json> <rolls.json> [-j|--json]", PHP_EOL;
            throw new InvalidArgumentException('Missing required arguments.');
        }

        return [
            'showJson' => $showJson,
            'boardPath' => $positionalArgs[0],
            'rollsPath' => $positionalArgs[1],
        ];
    }

    /**
     * @param string $inputFilePath
     * @return array
     * @throws InvalidArgumentException
     */
    private function verifyInputFileAndParseInputFile(string $inputFilePath): array
    {
        // Validation: Check if files exist
        if (!is_readable($inputFilePath)) {
            echo "Error: Input file $inputFilePath do not exist or cannot be read.", PHP_EOL;
            throw new InvalidArgumentException("Input file '$inputFilePath' does not exist or is not readable.");
        }

        // Validation: Ensure JSON input file is a valid JSON.
        $data = json_decode(file_get_contents($inputFilePath), true);
        if (!$data) {
            echo "Error: Failed to decode JSON input file: $inputFilePath.", PHP_EOL;
            throw new InvalidArgumentException("Input file '$inputFilePath' does not contain a valid JSON.");
        }

        return $data;
    }
}