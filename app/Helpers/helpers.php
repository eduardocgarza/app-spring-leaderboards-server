<?php

// app/Helpers/helpers.php

use Illuminate\Support\Str;

function generateUsername($firstName)
{
    $randomChars = Str::random(10);
    $firstName = strtolower(str_replace(' ', '', $firstName));
    $username = $firstName . '-' . $randomChars;
    return $username;
}

function generateEndpoint($route)
{
    return "http://localhost:8000/api" . $route;
}
