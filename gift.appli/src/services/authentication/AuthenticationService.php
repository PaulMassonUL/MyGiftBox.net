<?php

namespace gift\app\services\authentication;

use gift\app\models\User;

class AuthenticationService
{
    public function checkPasswordStrength(string $pass, int $minimumLength): bool
    {
        $length = strlen($pass) > $minimumLength; // longueur minimale
        $digit = preg_match("#\d#", $pass); // au moins un digit
        $special = preg_match("#\W#", $pass); // au moins un car. spécial
        $lower = preg_match("#[a-z]#", $pass); // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $pass); // au moins une majuscule

        return $length && $digit && $special && $lower && $upper;
    }

    public function checkEmailComposition(string $email): bool
    {
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

        return preg_match($pattern, $email);
    }

    public function authenticate(array $data): string
    {
        if (!empty($data['email']) && !empty($data['password'])) {
            $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $email = htmlspecialchars($email);
            $pass = htmlspecialchars($data['password']);

            try {
                $user = User::findOrFail($email);
                if (!password_verify($pass, $user['password'])) throw new \Exception();
            } catch (\Exception) {
                throw new AuthenticationFailedException("Email ou mot de passe incorrect.");
            }

            return $user['email'];
        }
        throw new \Exception("Missing email or password in user connection.");
    }
}