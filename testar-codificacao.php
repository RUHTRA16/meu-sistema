<?php

$origem = 'resources/views/characters/details.blade.php';
$destino = 'resources/views/details.preview.blade.php';

$conteudo = file_get_contents($origem);

if ($conteudo === false) {
    exit("Erro ao ler o arquivo original.\n");
}

$corrigido = iconv('UTF-8', 'Windows-1252', $conteudo);

if ($corrigido === false) {
    exit("Erro na conversão. O arquivo original foi preservado.\n");
}

if (file_put_contents($destino, $corrigido) === false) {
    exit("Erro ao criar a prévia.\n");
}

echo "Prévia criada em resources/views/details.preview.blade.php\n";
