<?php

$arquivo = 'resources/views/characters/details.blade.php';
$conteudo = file_get_contents($arquivo);

if ($conteudo === false) {
    exit("Erro ao ler o arquivo.\n");
}

$corrigido = iconv('UTF-8', 'Windows-1252', $conteudo);

if ($corrigido === false) {
    exit("Erro ao converter o arquivo. Nenhuma alteração foi salva.\n");
}

if (file_put_contents($arquivo, $corrigido) === false) {
    exit("Erro ao salvar o arquivo.\n");
}

echo "Arquivo corrigido com sucesso.\n";
