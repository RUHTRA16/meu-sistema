<?php

namespace App\Services;

use InvalidArgumentException;

class DiceRollService
{
    /**
     * Identifica pedidos explícitos de rolagem.
     *
     * Exemplos:
     * - Role 1d20
     * - Role 3d20 para testar
     * - Rolar 2d6+3
     * - Jogue d20
     */
    public function parseRequest(string $message): ?array
    {
        $pattern = '/\b(?:role|rolar|jogue|jogar|lance|lançar)\s+'
            . '(?:(\d{1,2})\s*)?d(\d{1,4})'
            . '(?:\s*([+-])\s*(\d{1,4}))?/iu';

        if (! preg_match($pattern, $message, $matches)) {
            return null;
        }

        $quantity = isset($matches[1]) && $matches[1] !== ''
            ? (int) $matches[1]
            : 1;

        $sides = (int) $matches[2];

        $modifier = 0;

        if (isset($matches[4]) && $matches[4] !== '') {
            $modifier = (int) $matches[4];

            if (($matches[3] ?? '+') === '-') {
                $modifier *= -1;
            }
        }

        if ($quantity < 1 || $quantity > 20) {
            throw new InvalidArgumentException(
                'Você pode rolar entre 1 e 20 dados por vez.'
            );
        }

        if ($sides < 2 || $sides > 1000) {
            throw new InvalidArgumentException(
                'Cada dado precisa ter entre 2 e 1000 faces.'
            );
        }

        if (abs($modifier) > 1000) {
            throw new InvalidArgumentException(
                'O modificador máximo permitido é 1000.'
            );
        }

        return [
            'quantity' => $quantity,
            'sides' => $sides,
            'modifier' => $modifier,
            'notation' => $quantity . 'd' . $sides
                . ($modifier > 0 ? '+' . $modifier : '')
                . ($modifier < 0 ? (string) $modifier : ''),
        ];
    }

    /**
     * Executa a rolagem no servidor.
     */
    public function roll(array $request): array
    {
        $quantity = (int) $request['quantity'];
        $sides = (int) $request['sides'];
        $modifier = (int) $request['modifier'];

        if ($quantity < 1 || $quantity > 20 || $sides < 2 || $sides > 1000) {
            throw new InvalidArgumentException(
                'Os parâmetros da rolagem são inválidos.'
            );
        }

        $dice = [];
        $subtotal = 0;

        for ($i = 0; $i < $quantity; $i++) {
            $result = random_int(1, $sides);

            $dice[] = [
                'sides' => $sides,
                'result' => $result,
            ];

            $subtotal += $result;
        }

        return [
            'notation' => $request['notation'],
            'dice' => $dice,
            'modifier' => $modifier,
            'subtotal' => $subtotal,
            'total' => $subtotal + $modifier,
        ];
    }
}
