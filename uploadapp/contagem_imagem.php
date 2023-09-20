<?php
header("Access-Control-Allow-Origin: *");

if(file_exists("uploads/number.txt")) {
    $conteudo = file_get_contents(("uploads/number.txt"));
} else {
    $conteudo = 0;
}

echo $conteudo;
?>