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

        // Separa o conteúdo pelos marcadores de página do OCR.
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

        $ignored = [
            'que', 'qual', 'quais', 'como', 'cada',
            'uma', 'uns', 'das', 'dos', 'para', 'sobre',
            'com', 'sem', 'sao', 'ser', 'por', 'em',
            'de', 'do', 'da', 'e', 'o', 'a', 'no',
            'na', 'um', 'meu', 'minha', 'consulte',
            'livro', 'regras', 'regra', 'qualquer',
            'funciona', 'concedido', 'concedida',
            'personagem', 'ordem', 'paranormal',
            'explique', 'informe', 'diga',
        ];

        $questionWords = preg_split(
            '/\s+/u',
            $normalizedQuestion,
            -1,
            PREG_SPLIT_NO_EMPTY
        ) ?: [];

        $words = array_values(array_unique(array_filter(
            $questionWords,
            fn (string $word): bool =>
                mb_strlen($word) >= 3
                && !in_array($word, $ignored, true)
        )));

        $phrases = [
            'testes de resistencia',
            'testes de resistencia contra rituais',
            'habilidades de criaturas paranormais',
            'efeitos paranormais',
            'criacao de personagem',
            'pontos de vida',
            'pontos de esforco',
            'testes de fortitude',
            'testes de vontade',
            'testes de reflexos',
            'protecao contra rituais',
            'bonus em testes de resistencia',
            'atributo base',
            'pericias',
        ];

        $normalizedPhrases = array_map(
            fn (string $phrase): string => $this->normalize($phrase),
            $phrases
        );

        $asksAboutResistanceBonus =
            str_contains($normalizedQuestion, 'resistencia')
            && (
                str_contains($normalizedQuestion, 'bonus')
                || str_contains($normalizedQuestion, 'rituais')
                || str_contains($normalizedQuestion, 'criaturas')
            );

        $asksAboutRitualResistance =
            str_contains($normalizedQuestion, 'resistencia')
            && str_contains($normalizedQuestion, 'rituais')
            && str_contains($normalizedQuestion, 'criaturas paranormais');

        // Reconhece perguntas sobre os cinco atributos.
        $asksAboutAttributes =
            str_contains($normalizedQuestion, 'atributos');

        $asksForAttributeNames =
            $asksAboutAttributes
            && (
                str_contains($normalizedQuestion, 'cinco')
                || str_contains($normalizedQuestion, 'quais sao')
                || str_contains($normalizedQuestion, 'quais')
            );

        // Reconhece perguntas que relacionam atributos e perícias.
        $asksAboutAttributeSkills =
            str_contains($normalizedQuestion, 'pericias')
            && (
                str_contains($normalizedQuestion, 'atributo base')
                || str_contains($normalizedQuestion, 'atributos')
                || str_contains($normalizedQuestion, 'agilidade')
                || str_contains($normalizedQuestion, 'forca')
                || str_contains($normalizedQuestion, 'intelecto')
                || str_contains($normalizedQuestion, 'presenca')
                || str_contains($normalizedQuestion, 'vigor')
            );

        $attributeNames = [
            'agilidade',
            'forca',
            'intelecto',
            'presenca',
            'vigor',
        ];

        $results = [];

        // Analisa cada página do livro.
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

            // Pontua os termos encontrados na página.
            foreach ($words as $word) {
                $needle = ' ' . $word . ' ';

                if (!str_contains($searchableText, $needle)) {
                    continue;
                }

                $matchedWords++;

                $occurrences = substr_count(
                    $searchableText,
                    $needle
                );

                $score += min($occurrences, 3);
            }

            // Valoriza expressões completas.
            foreach ($normalizedPhrases as $phrase) {
                if (
                    str_contains($normalizedQuestion, $phrase)
                    && str_contains(
                        $searchableText,
                        ' ' . $phrase . ' '
                    )
                ) {
                    $score += 12;
                }
            }

            // Considera a proporção de termos encontrados.
            if ($words !== []) {
                $coverage = $matchedWords / count($words);
                $score += (int) round($coverage * 10);
            }

            // Prioriza a regra Proteção contra Rituais.
            if ($asksAboutResistanceBonus) {
                if (
                    str_contains(
                        $searchableText,
                        'protecao contra rituais'
                    )
                ) {
                    $score += 30;
                }

                if (
                    str_contains(
                        $searchableText,
                        'habilidades de criaturas paranormais'
                    )
                ) {
                    $score += 25;
                }

                if (
                    str_contains(
                        $searchableText,
                        'testes de resistencia'
                    )
                ) {
                    $score += 10;
                }
            }

            // Avalia páginas que apresentam os cinco atributos.
            if ($asksAboutAttributes || $asksAboutAttributeSkills) {
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
                    $score += 30;
                }

                if (
                    preg_match(
                        '/\b(atributos|agilidade|forca|intelecto|presenca|vigor)\b/u',
                        $normalizedText
                    )
                ) {
                    $score += 5;
                }

                // Evita favorecer páginas que só apresentam exemplos.
                if (
                    $asksForAttributeNames
                    && str_contains(
                        $normalizedText,
                        'exemplos de testes de atributo'
                    )
                ) {
                    $score -= 20;
                }

                // Favorece páginas que relacionam atributos e perícias.
                if (
                    $asksAboutAttributeSkills
                    && str_contains($normalizedText, 'atributo base')
                    && $foundAttributes === 5
                ) {
                    $score += 40;
                }
            }

            if ($matchedWords === 0) {
                continue;
            }

            $results[] = [
                'page' => $number,
                'score' => $score,
                'matched_words' => $matchedWords,
                'text' => $text,
                'normalized_text' => $normalizedText,
            ];
        }

        if ($results === []) {
            return '';
        }

        // Ordena por pontuação e, em caso de empate, pela página.
        usort(
            $results,
            fn (array $a, array $b): int =>
                ($b['score'] <=> $a['score'])
                ?: ($a['page'] <=> $b['page'])
        );

        // Para perguntas específicas sobre resistência contra rituais,
        // mantém páginas que contêm a regra procurada.
        if ($asksAboutRitualResistance) {
            $exactMatches = array_values(array_filter(
                $results,
                fn (array $item): bool =>
                    str_contains(
                        $item['normalized_text'],
                        'testes de resistencia contra rituais y habilidades de criaturas paranormales'
                    )
                    || str_contains(
                        $item['normalized_text'],
                        'testes de resistencia contra rituais e habilidades de criaturas paranormais'
                    )
            ));

            if ($exactMatches !== []) {
                $results = $exactMatches;
            }
        }

        // Para perguntas sobre atributos, prioriza páginas
        // que apresentam todos os cinco nomes.
        if ($asksAboutAttributes) {
            $completeAttributePages = array_values(array_filter(
                $results,
                function (array $item) use ($attributeNames): bool {
                    $text = ' ' . $item['normalized_text'] . ' ';

                    foreach ($attributeNames as $attribute) {
                        if (
                            !str_contains(
                                $text,
                                ' ' . $attribute . ' '
                            )
                        ) {
                            return false;
                        }
                    }

                    return true;
                }
            ));

            if ($completeAttributePages !== []) {
                $results = $completeAttributePages;
            }

            // Para perguntas que pedem os nomes, prioriza a lista direta.
            if ($asksForAttributeNames) {
                $directListPages = array_values(array_filter(
                    $results,
                    function (array $item) use ($attributeNames): bool {
                        $text = $item['normalized_text'];

                        return str_contains(
                            $text,
                            'cinco atributos'
                        ) && count(array_filter(
                            $attributeNames,
                            fn (string $attribute): bool =>
                                str_contains(
                                    ' ' . $text . ' ',
                                    ' ' . $attribute . ' '
                                )
                        )) === 5;
                    }
                ));

                if ($directListPages !== []) {
                    $results = $directListPages;
                }
            }
        }

        // Para perguntas sobre perícias, evita páginas genéricas
        // quando existe uma página que reúne os cinco atributos.
        if ($asksAboutAttributeSkills) {
            $attributeSkillPages = array_values(array_filter(
                $results,
                function (array $item) use ($attributeNames): bool {
                    $text = ' ' . $item['normalized_text'] . ' ';

                    foreach ($attributeNames as $attribute) {
                        if (
                            !str_contains(
                                $text,
                                ' ' . $attribute . ' '
                            )
                        ) {
                            return false;
                        }
                    }

                    return str_contains(
                        $item['normalized_text'],
                        'atributo base'
                    );
                }
            ));

            if ($attributeSkillPages !== []) {
                $results = $attributeSkillPages;
            }
        }

        // Remove resultados muito abaixo da melhor correspondência.
        $bestScore = $results[0]['score'] ?? 0;

        $minimumScore = max(
            5,
            (int) ceil($bestScore * 0.35)
        );

        $results = array_values(array_filter(
            $results,
            fn (array $item): bool =>
                $item['score'] >= $minimumScore
        ));

        $selected = array_slice(
            $results,
            0,
            max(1, $limit)
        );

        // Monta o contexto que será enviado à IA.
        return implode("\n\n---\n\n", array_map(
            function (array $item) use (
                $asksAboutRitualResistance,
                $asksAboutAttributeSkills
            ): string {
                $text = $item['text'];

                if ($asksAboutRitualResistance) {
                    $excerpt = $this->extractRitualExcerpt($text);

                    if ($excerpt !== '') {
                        $text = $excerpt;
                    }
                }

                // Não corta a lista de atributos e perícias no meio.
                $maxLength = $asksAboutAttributeSkills ? 6000 : 1800;

                return "PÁGINA {$item['page']}\n"
                    . mb_substr($text, 0, $maxLength, 'UTF-8');
            },
            $selected
        ));
    }

    /**
     * Extrai Proteção contra Rituais até o fim da versão Verdadeiro.
     */
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

            $excerpt = mb_substr(
                $excerpt,
                0,
                $characterEnd,
                'UTF-8'
            );
        } else {
            $excerpt = mb_substr($excerpt, 0, 1200, 'UTF-8');
        }

        return trim($excerpt);
    }

    /**
     * Padroniza o texto para facilitar as comparações.
     */
    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);

        // Une palavras separadas por hífen no fim da linha pelo OCR.
        $text = preg_replace(
            '/(\p{L})-[ \t]*\R[ \t]*(?=\p{L})/u',
            '$1',
            $text
        ) ?? $text;

        // Remove acentos para comparar termos equivalentes.
        $text = strtr($text, [
            'á' => 'a',
            'à' => 'a',
            'ã' => 'a',
            'â' => 'a',
            'é' => 'e',
            'ê' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ç' => 'c',
        ]);

        // Substitui pontuação e quebras de linha por espaços.
        $text = preg_replace(
            '/[^\p{L}\p{N}%]+/u',
            ' ',
            $text
        ) ?? $text;

        return trim(
            preg_replace('/\s+/u', ' ', $text) ?? $text
        );
    }
}