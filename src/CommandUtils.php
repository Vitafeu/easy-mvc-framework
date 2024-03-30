<?php

namespace Vitafeu\EasyMVC;

use Vitafeu\EasyMVC\Maker;
use Vitafeu\EasyMVC\Database;

class CommandUtils {
    public function handleCommand($args) {
        $args = array_slice($args, 1);
        $command = array_shift($args);

        if (empty($args)) {
            $command = 'help';
        }
        
        switch ($command) {
            case 'serve':
                echo "Starting server on http://localhost:8080 \n";
                exec('php -S localhost:8080 -t public');
                break;
            case 'make:controller':
                Maker::makeController(array_shift($args));
                break;
            case 'make:model':
                $name = array_shift($args);
                Maker::makeModel($name);

                // check for controller flag
                $nextArg = array_shift($args);
                if ($nextArg === '-c' || $nextArg === '--controller') {
                    Maker::makeController($name);
                }
                break;
            case 'make:tables':
                Database::createTables();
                break;
            case 'drop:tables':
                Database::dropTables();
                break;
            case 'refresh:tables':
                Database::dropTables();
                Database::createTables();
                break;
            case 'help':
                echo "Available commands:\n";
                echo "  make:controller <name>\n";
                echo "  make:model <name>\n";
                echo "  make:tables\n";
                echo "  drop:tables\n";
                break;
            default:
                echo "Unknown command: $command\n";
                echo "Type 'help' for a list of available commands.\n";
                break;
        }
    }
}