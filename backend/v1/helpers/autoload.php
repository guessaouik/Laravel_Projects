<?php 

spl_autoload_register(function ($class){
    $namespaceStructure = explode("\\", $class);
    $className = array_pop($namespaceStructure);
    $path = ROOT_DIR . implode(DIRECTORY_SEPARATOR, array_map(fn($dir) => lcfirst($dir), $namespaceStructure));
    @include_once $path . DIRECTORY_SEPARATOR . $className . ".php"; 
});

require_once ROOT_DIR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";