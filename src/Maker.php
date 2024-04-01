<?php

namespace Vitafeu\EasyMVC;

use Vitafeu\EasyMVC\Globals;

class Maker {
    public static function makeController($name)
    {
        $name = ucfirst($name);
        $template = <<<EOT
                    <?php
    
                    namespace App\Controllers;
    
                    use Vitafeu\EasyMVC\Controller;
    
                    class {$name}Controller extends Controller {
                        public function home() {   
                            \$this->render('$name/Home');
                        }
                    }
                    EOT;
    
        $fileName = Globals::getProjectRoot() . "app/Controllers/{$name}Controller.php"; 
        
        if (!file_exists($fileName)) {
            file_put_contents($fileName, $template);
            echo "$name controller created successfully\n";
        } else {
            echo "$name controller already exists\n";
        }
    }
    
    public static function makeModel($name)
    {
        $name = ucfirst($name);
        $lowerName = strtolower($name);
        $template = <<<EOT
                    <?php
    
                    namespace App\Models;
    
                    use Vitafeu\EasyMVC\Model;
    
                    class {$name} extends Model {
                        protected static \$table = '{$lowerName}s';
    
                        protected static \$attributes = [
                            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                            // Complete with your own attributes
                        ];
                    }
                    EOT;

        self::saveModel($name, $template);
    }

    public static function makeFirestoreModel($name) {
        $name = ucfirst($name);
        $lowerName = strtolower($name);
        $template = <<<EOT
                    <?php

                    namespace App\Models;

                    use Vitafeu\EasyMVC\Firebase\FirestoreModel;

                    class {$name} extends FirestoreModel {
                        protected static \$collection = '{$lowerName}s';
                    }
                    EOT;

        self::saveModel($name, $template);
    }

    private static function saveModel($name, $template) {
        $rootDir = Globals::getProjectRoot();
        $fileName = $rootDir . "app/Models/{$name}.php"; 
    
        if (!file_exists($rootDir . "/app/Models")) {
            mkdir($rootDir . "/app/Models");
            echo "Created Models folder\n";
        }
    
        if (!file_exists($fileName)) {
            file_put_contents($fileName, $template);
            echo "$name model created successfully\n";
        } else {
            echo "$name model already exists\n";
        }
    }
}