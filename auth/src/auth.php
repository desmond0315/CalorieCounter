<?php

function register_user(string $email, string $password): bool
{
    $sql = 'INSERT INTO users(email, password)
            VALUES(:email, :password)';

    $statement = db()->prepare($sql);

    $statement->bindValue(':email', $email, PDO::PARAM_STR);
    $statement->bindValue(':password', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);

    return $statement->execute();
}