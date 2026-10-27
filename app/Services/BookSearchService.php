<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class BookSearchService
{
    public function search(string $question, int $limit = 5): string
    {
        $path = storage_path(
            'app/private/books/ordem-paranormal-rpg-completo.txt'
        );

        if (!File::exists($path)) {
            return '';
        }

        $content = File::get($path);

        $pages = preg_split(
            '/={20}\s*PÁGINA\s+(\d+)\s*={20}/u',
            $content,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );

        if ($pages === false || count($pages) < 3) {
            return '';
        }

        $normalizedQuestion = $this->normalize($question);

        if ($normalizedQuestion === '') {
            return '';
        }

        $words = $this->getSearchWords($normalizedQuestion);

        $asksAboutRitualResistance =
            str_contains($normalizedQuestion, 'resistencia')
            && str_contains($normalizedQuestion, 'rituais')
            && (
                str_contains($normalizedQuestion, 'criaturas paranormais')
                || str_contains($normalizedQuestion, 'efeitos paranormais')
                || str_contains($normalizedQuestion, 'bonus')
            );

        $asksAboutAttributeSkills =
            str_contains($normalizedQuestion, 'pericia')
            && (
                str_contains($normalizedQuestion, 'atributo')
                || str_contains($normalizedQuestion, 'agilidade')
                || str_contains($normalizedQuestion, 'forca')
                || str_contains($normalizedQuestion, 'intelecto')
                || str_contains($normalizedQuestion, 'presenca')
                || str_contains($normalizedQuestion, 'vigor')
                || str_contains($normalizedQuestion, 'lista completa')
            );

        $asksForAttributeNames =
            str_contains($normalizedQuestion, 'atributo')
            && (
                str_contains($normalizedQuestion, 'cinco')
                || str_contains($normalizedQuestion, 'quais')
            )
            && !str_contains($normalizedQuestion, 'pericia');

        $asksAboutEffort =
            str_contains($normalizedQuestion, 'ponto de esforco')
            || str_contains($normalizedQuestion, 'pontos de esforco')
            || str_contains($normalizedQuestion, 'como funciona o pe');

        $asksAboutDefense =
            str_contains($normalizedQuestion, 'defesa');

        $attributeNames = [
            'agilidade',
            'forca',
            'intelecto',
            'presenca',
            'vigor',
        ];

        $attributeSkillPhrases = [
            'agilidade e o atributo base das pericias',
            'forca e o atributo base de atletismo',
            'intelecto e o atributo base de atualidades',
            'presenca e o atributo base de diplomacia',
            'vigor e o atributo base de fortitude',
        ];

        $results = [];

        for ($i = 1; $i < count($pages); $i += 2) {
            if (!isset($pages[$i + 1])) {
                continue;
            }

            $number = (int) $pages[$i];
            $text = trim($pages[$i + 1]);

            if ($text === '') {
                continue;
            }

            $normalizedText = $this->normalize($text);
            $searchableText = ' ' . $normalizedText . ' ';

            $score = 0;
            $matchedWords = 0;

            foreach ($words as $word) {
                if (str_contains($searchableText, ' ' . $word . ' ')) {
                    $matchedWords++;

                    $occurrences = substr_count(
                        $searchableText,
                        ' ' . $word . ' '
                    );

                    $score += min($occurrences, 3);
                }
            }

            if ($words !== []) {
                $score += (int) round(
                    ($matchedWords / count($words)) * 10
                );
            }

            // Busca pelos cinco atributos.
            if ($asksForAttributeNames || $asksAboutAttributeSkills) {
                $foundAttributes = 0;

                foreach ($attributeNames as $attribute) {
                    if (
                        str_contains(
                            $searchableText,
                            ' ' . $attribute . ' '
                        )
                    ) {
                        $foundAttributes++;
                    }
                }

                if ($foundAttributes === 5) {
                    $score += 25;
                }

                if (
                    str_contains($normalizedText, 'cinco atributos')
                    || str_contains($normalizedText, 'cada atributo')
                ) {
                    $score += 15;
                }
            }

            // Relação entre atributos e perícias.
            if ($asksAboutAttributeSkills) {
                $matchedAttributeSkillPhrases = 0;

                foreach ($attributeSkillPhrases as $phrase) {
                    if (str_contains($normalizedText, $phrase)) {
                        $matchedAttributeSkillPhrases++;
                    }
                }

                $score += $matchedAttributeSkillPhrases * 25;

                if ($matchedAttributeSkillPhrases >= 3) {
                    $score += 50;
                }

                if (
                    str_contains($normalizedText, 'atributo base')
                    && str_contains($normalizedText, 'pericias')
                ) {
                    $score += 20;
                }
            }

            // Pontos de Esforço.
            if ($asksAboutEffort) {
                if (str_contains($normalizedText, 'pontos de esforco')) {
                    $score += 35;
                }

                if (
                    str_contains($normalizedText, 'quantas habilidades')
                    && str_contains($normalizedText, 'presenca')
                ) {
                    $score += 15;
                }

                if (
                    str_contains(
                        $normalizedText,
                        'uma medida de sua energia determinacao e forca interior'
                    )
                ) {
                    $score += 25;
                }

                if (
                    str_contains(
                        $normalizedText,
                        'recuperando pv pe e san'
                    )
                ) {
                    $score -= 10;
                }
            }

            // Defesa base e modificadores.
            if ($asksAboutDefense) {
                if (
                    str_contains(
                        $normalizedText,
                        'a defesa representa o quao dificil e acerta lo em combate'
                    )
                ) {
                    $score += 100;
                }

                if (
                    str_contains(
                        $normalizedText,
                        'sua defesa e igual a 10'
                    )
                    && str_contains($normalizedText, 'agilidade')
                ) {
                    $score += 100;
                }

                if (
                    str_contains(
                        $normalizedText,
                        'modificadores por habilidades equipamentos e condicoes'
                    )
                ) {
                    $score += 50;
                }

                if (
                    str_contains($normalizedText, 'defesa')
                    && str_contains($normalizedText, 'protecao')
                    && !str_contains($normalizedText, 'sua defesa e igual a 10')
                ) {
                    $score += 5;
                }
            }

            // Resistência contra rituais.
            if ($asksAboutRitualResistance) {
                if (
                    str_contains(
                        $normalizedText,
                        'protecao contra rituais'
                    )
                ) {
                    $score += 50;
                }

                if (
                    str_contains(
                        $normalizedText,
                        'testes de resistencia contra rituais'
                    )
                ) {
                    $score += 35;
                }

                if (
                    str_contains(
                        $normalizedText,
                        'criaturas paranormais'
                    )
                ) {
                    $score += 20;
                }
            }

            if ($matchedWords === 0 && $score === 0) {
                continue;
            }

            $results[] = [
                'page' => $number,
                'score' => $score,
                'text' => $text,
                'normalized_text' => $normalizedText,
            ];
        }

        if ($results === []) {
            return '';
        }

        usort(
            $results,
            fn (array $a, array $b): int =>
                ($b['score'] <=> $a['score'])
                ?: ($a['page'] <=> $b['page'])
        );

        // Rituais: mantém apenas a página com a regra específica.
        if ($asksAboutRitualResistance) {
            $exactMatches = array_values(array_filter(
                $results,
                fn (array $item): bool =>
                    str_contains(
                        $item['normalized_text'],
                        'protecao contra rituais'
                    )
                    && str_contains(
                        $item['normalized_text'],
                        'testes de resistencia contra rituais'
                    )
            ));

            if ($exactMatches !== []) {
                $results = $exactMatches;
            }
        }

        // Perícias: prioriza páginas que relacionam vários atributos.
        if ($asksAboutAttributeSkills) {
            $skillPages = array_values(array_filter(
                $results,
                function (array $item) use (
                    $attributeSkillPhrases
                ): bool {
                    $matches = 0;

                    foreach ($attributeSkillPhrases as $phrase) {
                        if (
                            str_contains(
                                $item['normalized_text'],
                                $phrase
                            )
                        ) {
                            $matches++;
                        }
                    }

                    return $matches >= 3;
                }
            ));

            if ($skillPages !== []) {
                $results = $skillPages;
            }
        }

        // Atributos: prioriza páginas com os cinco nomes.
        if ($asksForAttributeNames) {
            $attributePages = array_values(array_filter(
                $results,
                function (array $item) use ($attributeNames): bool {
                    $text = ' ' . $item['normalized_text'] . ' ';

                    foreach ($attributeNames as $attribute) {
                        if (!str_contains($text, ' ' . $attribute . ' ')) {
                            return false;
                        }
                    }

                    return true;
                }
            ));

            if ($attributePages !== []) {
                $results = $attributePages;
            }
        }

        // Defesa: prioriza a regra que apresenta o cálculo.
        if ($asksAboutDefense) {
            $defensePages = array_values(array_filter(
                $results,
                fn (array $item): bool =>
                    str_contains(
                        $item['normalized_text'],
                        'sua defesa e igual a 10'
                    )
                    && str_contains(
                        $item['normalized_text'],
                        'agilidade'
                    )
            ));

            if ($defensePages !== []) {
                $results = $defensePages;
            }
        }

        // PE: prioriza a definição principal dos Pontos de Esforço.
        if ($asksAboutEffort) {
            $effortPages = array_values(array_filter(
                $results,
                fn (array $item): bool =>
                    str_contains(
                        $item['normalized_text'],
                        'pontos de esforco'
                    )
                    && str_contains(
                        $item['normalized_text'],
                        'uma medida de sua energia determinacao e forca interior'
                    )
            ));

            if ($effortPages !== []) {
                $results = $effortPages;
            }
        }

        $bestScore = $results[0]['score'] ?? 0;
        $minimumScore = max(5, (int) ceil($bestScore * 0.35));

        $results = array_values(array_filter(
            $results,
            fn (array $item): bool =>
                $item['score'] >= $minimumScore
        ));

        $selected = array_slice($results, 0, max(1, $limit));

        return implode("\n\n---\n\n", array_map(
            function (array $item) use (
                $asksAboutRitualResistance,
                $asksForAttributeNames,
                $asksAboutEffort,
                $asksAboutDefense,
                $asksAboutAttributeSkills
            ): string {
                $text = $item['text'];

                if ($asksAboutRitualResistance) {
                    $excerpt = $this->extractRitualExcerpt($text);

                    if ($excerpt !== '') {
                        $text = $excerpt;
                    }
                } elseif ($asksForAttributeNames) {
                    $excerpt = $this->extractAround(
                        $text,
                        'cinco atributos',
                        250,
                        700
                    );

                    if ($excerpt !== '') {
                        $text = $excerpt;
                    }
                } elseif ($asksAboutEffort) {
                    $excerpt = $this->extractSection(
                        $text,
                        'Pontos de Esforço',
                        [
                            'Uma medida de sua saúde',
                            'Recuperando PV, PE e SAN',
                            'PONTOS TEMPORÁRIOS',
                        ],
                        800
                    );

                    if ($excerpt !== '') {
                        $text = $excerpt;
                    }
                } elseif ($asksAboutDefense) {
                    // Começa na regra de Defesa e termina antes
                    // do próximo tópico da página.
                    $excerpt = $this->extractSection(
                        $text,
                        'A Defesa representa',
                        [
                            'Deslocamento',
                            'DESCRICAO',
                        ],
                        650
                    );

                    if ($excerpt !== '') {
                        $text = $excerpt;
                    }
                } elseif ($asksAboutAttributeSkills) {
                    // Mantém a relação entre atributos e perícias,
                    // removendo o exemplo de criação de personagem.
                    $excerpt = $this->extractSection(
                        $text,
                        'Agilidade é o atributo-base',
                        [
                            'EXEMPLO DE CRIAÇÃO DE PERSONAGEM',
                        ],
                        3500
                    );

                    if ($excerpt !== '') {
                        $text = $excerpt;
                    }
                }

                $maxLength = $asksAboutAttributeSkills ? 6000 : 1800;

                return "PÁGINA {$item['page']}\n"
                    . mb_substr($text, 0, $maxLength, 'UTF-8');
            },
            $selected
        ));
    }

    private function getSearchWords(string $question): array
    {
        $ignored = [
            'que', 'qual', 'quais', 'como', 'cada',
            'uma', 'uns', 'das', 'dos', 'para', 'sobre',
            'com', 'sem', 'sao', 'ser', 'por', 'em',
            'de', 'do', 'da', 'e', 'o', 'a', 'no',
            'na', 'um', 'meu', 'minha', 'livro', 'regras',
            'regra', 'funciona', 'funcionam', 'personagem',
            'ordem', 'paranormal', 'relacionadas', 'relacionada',
            'cinco', 'atributo', 'atributos', 'pericia', 'pericias',
            'pontos', 'ponto', 'calculo', 'modificadores',
            'aplicam', 'usado', 'usada', 'lista', 'completa',
        ];

        $questionWords = preg_split(
            '/\s+/u',
            $question,
            -1,
            PREG_SPLIT_NO_EMPTY
        ) ?: [];

        return array_values(array_unique(array_filter(
            $questionWords,
            fn (string $word): bool =>
                mb_strlen($word) >= 3
                && !in_array($word, $ignored, true)
        )));
    }

    private function extractAround(
        string $text,
        string $term,
        int $before,
        int $length
    ): string {
        $position = mb_stripos($text, $term);

        if ($position === false) {
            return '';
        }

        $start = max(0, $position - $before);

        return trim(mb_substr($text, $start, $length, 'UTF-8'));
    }

    private function extractSection(
        string $text,
        string $startTerm,
        array $endTerms,
        int $maxLength
    ): string {
        $start = mb_stripos($text, $startTerm);

        if ($start === false) {
            return '';
        }

        $excerpt = mb_substr($text, $start);
        $endPosition = null;

        foreach ($endTerms as $endTerm) {
            $position = mb_stripos(
                $excerpt,
                $endTerm,
                mb_strlen($startTerm)
            );

            if (
                $position !== false
                && ($endPosition === null || $position < $endPosition)
            ) {
                $endPosition = $position;
            }
        }

        if ($endPosition !== null) {
            $excerpt = mb_substr($excerpt, 0, $endPosition);
        }

        return trim(mb_substr($excerpt, 0, $maxLength, 'UTF-8'));
    }

    private function extractRitualExcerpt(string $text): string
    {
        $title = 'Proteção contra Rituais';
        $start = mb_stripos($text, $title);

        if ($start === false) {
            return '';
        }

        $excerpt = mb_substr($text, $start);

        $pattern = '/Requer\s+4.{0,30}c.rculo\.?/iu';

        if (
            preg_match(
                $pattern,
                $excerpt,
                $matches,
                PREG_OFFSET_CAPTURE
            )
        ) {
            $byteEnd = $matches[0][1] + strlen($matches[0][0]);

            $characterEnd = mb_strlen(
                substr($excerpt, 0, $byteEnd),
                'UTF-8'
            );

            $excerpt = mb_substr($excerpt, 0, $characterEnd, 'UTF-8');
        } else {
            $excerpt = mb_substr($excerpt, 0, 1200, 'UTF-8');
        }

        return trim($excerpt);
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);

        $text = preg_replace(
            '/(\p{L})-[ \t]*\R[ \t]*(?=\p{L})/u',
            '$1',
            $text
        ) ?? $text;

        $text = strtr($text, [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a',
            'é' => 'e', 'ê' => 'e', 'í' => 'i',
            'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ü' => 'u', 'ç' => 'c',
        ]);

        $text = preg_replace(
            '/[^\p{L}\p{N}%]+/u',
            ' ',
            $text
        ) ?? $text;

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }
}