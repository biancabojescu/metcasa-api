<?php

return [

    'common' => [
        'server_error' => 'A aparut o eroare interna. Reincearca mai tarziu.',
        'not_found' => 'Resursa nu a fost gasita.',
        'forbidden' => 'Nu ai acces la aceasta resursa.',
        'unauthenticated' => 'Neautentificat.',
        'validation_failed' => 'Datele introduse nu sunt valide.',
    ],

    'auth' => [
        'registered' => 'Cont creat cu succes. Verifica emailul pentru confirmare.',
        'logged_in' => 'Autentificare reusita.',
        'logged_out' => 'Deconectat.',
        'invalid_credentials' => 'Emailul sau parola sunt incorecte.',
        'user_not_found' => 'Nu exista cont cu acest email.',
    ],

    'profile' => [
        'updated' => 'Profilul a fost actualizat.',
    ],

    'favorite' => [
        'added' => 'Anuntul a fost adaugat la favorite.',
        'removed' => 'Anuntul a fost scos de la favorite.',
        'cleared' => 'Toate favoritele au fost sterse.',
    ],

    'email_verification' => [
        'invalid_link' => 'Link-ul de confirmare este invalid.',
        'expired_link' => 'Link-ul de confirmare a expirat sau a fost modificat.',
        'already_verified' => 'Emailul a fost deja confirmat.',
        'verified' => 'Email confirmat cu succes.',
        'resent' => 'Link-ul de confirmare a fost retrimis pe email.',
    ],

    'listing' => [
        'created' => 'Anuntul a fost creat.',
        'updated' => 'Anuntul a fost actualizat.',
        'deleted' => 'Anuntul a fost sters.',
        'not_found' => 'Anuntul nu a fost gasit.',
    ],

    'password_reset' => [
        'link_sent' => 'Daca exista un cont cu acel email, ai primit un link de resetare.',
        'invalid_token' => 'Token-ul de resetare este invalid sau a expirat.',
        'success' => 'Parola a fost schimbata cu succes. Te poti autentifica acum.',
        'throttled' => 'Asteapta inainte sa ceri un alt link.',
    ],

];
