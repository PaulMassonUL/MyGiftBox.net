<?php

namespace gift\app\services\authentication;

use gift\app\models\User;

class AuthenticationService
{
    const MINIMUM_PASSWORD_LENGTH = 8;
    const MAXIMUM_PASSWORD_LENGTH = 50;

    public function checkPasswordStrength(string $pass): bool
    {
        $length = strlen($pass) >= self::MINIMUM_PASSWORD_LENGTH; // longueur minimale
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
        if (empty($data['email'])) throw new AuthenticationFailedException("Vous devez saisir une adresse e-mail.");
        if (empty($data['password'])) throw new AuthenticationFailedException("Vous devez saisir un mot de passe.");

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

    public function register(array $data): string
    {
        if (empty($data['email'])) throw new RegistrationFailedException("Vous devez saisir une adresse e-mail.");
        if (empty($data['nom'])) throw new RegistrationFailedException("Vous devez saisir un nom.");
        if (empty($data['prenom'])) throw new RegistrationFailedException("Vous devez saisir un prénom.");
        if (empty($data['password'])) throw new RegistrationFailedException("Vous devez saisir un mot de passe.");
        if (empty($data['confirm_password'])) throw new RegistrationFailedException("Vous devez confirmer votre mot de passe.");

        $email = htmlspecialchars($data['email']);
        $nom = htmlspecialchars($data['nom']);
        $prenom = htmlspecialchars($data['prenom']);
        $pass = htmlspecialchars($data['password']);
        $confirm_pass = htmlspecialchars($data['confirm_password']);

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || !self::checkEmailComposition($email)) throw new RegistrationFailedException("L'adresse email n'est pas valide.");

        if (User::find($email)) throw new RegistrationFailedException("L'adresse email est déjà utilisée.");

        if (strlen($nom) < 2) throw new RegistrationFailedException("Le nom doit contenir au moins 2 caractères.");
        if (strlen($prenom) < 2) throw new RegistrationFailedException("Le prénom doit contenir au moins 2 caractères.");

        if (strlen($pass) > self::MAXIMUM_PASSWORD_LENGTH) throw new RegistrationFailedException("Le mot de passe ne peut pas faire plus de " . self::MAXIMUM_PASSWORD_LENGTH . " caractères.");

        if (!self::checkPasswordStrength($pass)) throw new RegistrationFailedException("Le mot de passe n'est pas assez robuste : il faut un mot de passe d'au moins " . self::MINIMUM_PASSWORD_LENGTH . " caractères qui inclut : des chiffres, des lettres majuscules et minuscules, des caractères spéciaux.");

        if ($pass !== $confirm_pass) throw new RegistrationFailedException("Les mots de passe ne correspondent pas.");

        $user = new User();
        $user->email = $email;
        $user->password = password_hash($pass, PASSWORD_DEFAULT, ['cost' => 12]);
        $user->nom = $nom;
        $user->prenom = $prenom;
        if ($user->save()) return $email;
        throw new RegistrationFailedException("Une erreur est survenue lors de l'enregistrement de l'utilisateur.");
    }
}