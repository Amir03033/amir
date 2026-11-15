<?php

// Dutch messages for the rules used by the public forms (contact, Ask Amir, ideas). Everything else falls back to English.
return [
    'required' => ':Attribute is verplicht.',
    'string' => ':Attribute moet tekst zijn.',
    'email' => 'Vul een geldig e-mailadres in.',
    'url' => 'Vul een geldige URL in.',
    'in' => 'Kies een geldige waarde voor :attribute.',
    'boolean' => ':Attribute moet ja of nee zijn.',
    'integer' => ':Attribute moet een heel getal zijn.',
    'exists' => 'Deze :attribute bestaat niet (meer).',
    'min' => [
        'string' => ':Attribute moet minstens :min tekens zijn.',
        'numeric' => ':Attribute moet minstens :min zijn.',
    ],
    'max' => [
        'string' => ':Attribute mag maximaal :max tekens zijn.',
        'numeric' => ':Attribute mag maximaal :max zijn.',
    ],

    'attributes' => [
        'name' => 'naam',
        'email' => 'e-mailadres',
        'message' => 'bericht',
        'body' => 'vraag',
        'title' => 'idee',
        'detail' => 'toelichting',
    ],
];
