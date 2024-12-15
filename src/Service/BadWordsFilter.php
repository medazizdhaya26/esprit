<?php

namespace App\Service;

class BadWordsFilter
{
    private $badWords = ['merde', 'putin', 'debil','zero'];

    public function containsBadWords(string $commentaire): bool
    {
        foreach ($this->badWords as $badWord) {
            if (stripos($commentaire, $badWord) !== false) {
                return true;
            }
        }
        return false;
    }
}
