<?php
if (!function_exists('numberBetween')) {
    function numberBetween($firstNumber,$numberBetween,$lastNumber): bool
    {
        return $firstNumber <= $numberBetween && $numberBetween <= $lastNumber;
    }
}