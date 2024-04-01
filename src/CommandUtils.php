<?php

namespace Vitafeu\EasyMVC;

use Vitafeu\EasyMVC\Maker;
use Vitafeu\EasyMVC\Database;

class CommandUtils {
    public function handleCommand($argv) {
        array_shift($argv);

        $command = array_shift($argv);
        $args = $argv;
    
        switch ($command) {
            case 'serve':
                echo "Starting server on http://localhost:8080 \n";
                exec('php -S localhost:8080 -t public');
                break;
            case 'make:controller':
                if (empty($args)) {
                    echo "Usage: make:controller <name>\n";
                    break;
                }
                Maker::makeController(array_shift($args));
                break;

            case 'make:middleware':
                if (empty($args)) {
                    echo "Usage: make:middleware <name>\n";
                    break;
                }
                Maker::makeMiddleware(array_shift($args));
                break;
            case 'make:model':
                $options = ['--controller', '-c'];
    
                // Check if options are present
                $name = null;
                foreach ($args as $arg) {
                    if (!in_array($arg, $options)) {
                        $name = $arg;
                        break;
                    }
                }
    
                if (empty($name)) {
                    echo "Usage: make:model <name> [-c|--controller]\n";
                    break;
                }
    
                Maker::makeModel($name);
    
                // Check for controller flag
                if (in_array('-c', $args) || in_array('--controller', $args)) {
                    Maker::makeController($name);
                }
                break;
            case 'make:firestore-model':
                $options = ['--controller', '-c'];

                // Check if options are present
                $name = null;
                foreach ($args as $arg) {
                    if (!in_array($arg, $options)) {
                        $name = $arg;
                        break;
                    }
                }

                if (empty($name)) {
                    echo "Usage: make:firestore-model <name> [-c|--controller]\n";
                    break;
                }
                
                Maker::makeFirestoreModel($name);

                // Check for controller flag
                if (in_array('-c', $args) || in_array('--controller', $args)) {
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
                echo "  serve\n";
                echo "  make:controller <name>\n";
                echo "  make:model <name> [-c|--controller]\n";
                echo "  make:firestore-model <name> [-c|--controller]\n";
                echo "  make:tables\n";
                echo "  drop:tables\n";
                echo "  refresh:tables\n";
                break;
            default:
                echo "Unknown command: $command\n";
                echo "Type 'help' for a list of available commands.\n";
                break;
        }
    }    
}