<?php

namespace App\Support;

use Illuminate\Http\Request;

class MathCaptcha
{
    public static function ensure(Request $request, string $scope): void
    {
        if (
            $request->boolean('refresh_captcha')
            || !$request->session()->has(self::questionKey($scope))
            || !$request->session()->has(self::answerKey($scope))
        ) {
            self::regenerate($request, $scope);
        }
    }

    public static function regenerate(Request $request, string $scope): void
    {
        $left = random_int(1, 9);
        $right = random_int(1, 9);

        $request->session()->put(self::questionKey($scope), "{$left} + {$right}");
        $request->session()->put(self::answerKey($scope), $left + $right);
    }

    public static function question(Request $request, string $scope): string
    {
        return (string) $request->session()->get(self::questionKey($scope), '0 + 0');
    }

    public static function isValid(Request $request, string $scope, string $inputKey = 'captcha_answer'): bool
    {
        $expectedAnswer = (int) $request->session()->get(self::answerKey($scope), -1);
        $providedAnswer = (int) $request->input($inputKey);

        return $providedAnswer === $expectedAnswer;
    }

    private static function questionKey(string $scope): string
    {
        return "{$scope}_captcha_question";
    }

    private static function answerKey(string $scope): string
    {
        return "{$scope}_captcha_answer";
    }
}
