<?php

$origem = 'resources/views/characters/details.blade.php';
$destino = 'resources/views/details.preview.blade.php';

$texto = file_get_contents($origem);

if ($texto === false) {
    exit("Erro ao ler o arquivo original.\n");
}


$correcoes = [
    'Arquivo confidencial â€” Agente' => 'Arquivo confidencial — Agente',
    'CriaÃ§Ã£o' => 'Criação',
    'criaÃ§Ã£o' => 'criação',
    'HistÃ³ria' => 'História',
    'histÃ³ria' => 'história',
    'HISTÃ“RIA' => 'HISTÓRIA',
    'aparÃªncia' => 'aparência',
    'AparÃªncia' => 'Aparência',
    'APARÃŠNCIA' => 'APARÊNCIA',
    'fÃsica' => 'física',
    'serÃ£o' => 'serão',
    'serÃ¡' => 'será',
    'prÃ³ximas' => 'próximas',
    'PrÃ³xima' => 'Próxima',
    'NÃ£o' => 'Não',
    'possÃvel' => 'possível',
    'NÃvel' => 'Nível',
    'ExposiÃ§Ã£o' => 'Exposição',
    'padrÃ£o' => 'padrão',
    'versÃ£o' => 'versão',
    'permitido Ã©' => 'permitido é',
    'provisÃ³ria' => 'provisória',
    'ForÃ§a' => 'Força',
    'PresenÃ§a' => 'Presença',
    'AcadÃªmico' => 'Acadêmico',
    'Agente de SaÃºde' => 'Agente de Saúde',
    'AmnÃ©sico' => 'Amnésico',
    'FanÃ¡tico' => 'Fanático',
    'FotÃ³grafo' => 'Fotógrafo',
    'MercenÃ¡rio' => 'Mercenário',
    'OperÃ¡rio' => 'Operário',
    'Servidor PÃºblico' => 'Servidor Público',
    'TeÃ³rico da ConspiraÃ§Ã£o' => 'Teórico da Conspiração',
    'UniversitÃ¡rio' => 'Universitário',
    'VÃtima' => 'Vítima',
    'â€”' => '—',
    'â†’' => '→',
    'â†' => '←', 
    'inÃcio' => 'início',
    'DistribuiÃ§Ã£o' => 'Distribuição',
    'vÃ¡lida' => 'válida',
    'VocÃª' => 'Você',
    'NAVEGAÃ‡ÃƒO' => 'NAVEGAÇÃO',
];


$texto = str_replace(
    array_keys($correcoes),
    array_values($correcoes),
    $texto
);

if (file_put_contents($destino, $texto) === false) {
    exit("Erro ao criar a prévia.\n");
}

echo "Prévia criada com sucesso.\n";
