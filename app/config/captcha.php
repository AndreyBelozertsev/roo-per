<?php

if (!class_exists('CaptchaConfiguration')) { return; }

return [
    'TypoCaptcha' => [
        'UserInputID' => 'captchaCode',
        'CodeLength' => CaptchaRandomization::GetRandomCodeLength(4, 5),
        'ImageWidth' => 250,
        'ImageHeight' => 60,
        'Locale' => 'ru',
        'SoundEnabled' => false
    ],
];
