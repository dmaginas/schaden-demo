<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | German messages for the validation rules used in this application.
    | Rules without a German line fall back to the English framework text.
    |
    */

    'before_or_equal' => 'Das Feld :attribute muss ein Datum vor oder gleich :date sein.',
    'date' => 'Das Feld :attribute muss ein gültiges Datum sein.',
    'decimal' => 'Das Feld :attribute muss :decimal Nachkommastellen haben.',
    'enum' => 'Der gewählte Wert für :attribute ist ungültig.',
    'exists' => 'Der gewählte Wert für :attribute ist ungültig.',
    'max' => [
        'numeric' => 'Das Feld :attribute darf nicht größer als :max sein.',
        'string' => 'Das Feld :attribute darf maximal :max Zeichen haben.',
    ],
    'min' => [
        'numeric' => 'Das Feld :attribute muss mindestens :min sein.',
        'string' => 'Das Feld :attribute muss mindestens :min Zeichen haben.',
    ],
    'numeric' => 'Das Feld :attribute muss eine Zahl sein.',
    'required' => 'Das Feld :attribute ist erforderlich.',
    'required_if' => 'Das Feld :attribute ist erforderlich, wenn :other :value ist.',
    'string' => 'Das Feld :attribute muss ein Text sein.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Values
    |--------------------------------------------------------------------------
    |
    | Display names for rule parameters, e.g. ":date" in "before_or_equal:today".
    |
    */

    'values' => [
        'occurred_on' => [
            'today' => 'heute',
        ],
    ],

    'attributes' => [],

];
