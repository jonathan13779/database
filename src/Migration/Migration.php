<?php

namespace Jonathan13779\Database\Migration;

class Migration{
    public static function create(string $name)
    {
        /**crear fichero */
        $migration = fopen("database/migrations/".date('Ymd_His')."_$name.php", "w+");
        fwrite($migration, "<?php\n\n");
        fwrite($migration, "use Jonathan13779\Database\Migration\Schema;\n\n");
        fwrite($migration, "Schema::createTable('$name', function(\$table){\n\n");
        fwrite($migration, "});\n");
        fclose($migration);
    }

    public static function remove(?string $name = null)
    {
        if($name){
            unlink("database/migrations/$name");
        }else{
            $files = scandir('database/migrations');
            foreach($files as $file){
                if($file == '.' || $file == '..'){
                    continue;
                }
                unlink("database/migrations/$file");
            }
        }
    }

    public static function execute(){
        $files = scandir('database/migrations');
        foreach($files as $file){
            if($file == '.' || $file == '..'){
                continue;
            }
            require_once 'database/migrations/'.$file;
        }
    }
}