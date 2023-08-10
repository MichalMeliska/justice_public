<?php

return [
    'LOCALHOST' => intval(isset($_SERVER['REMOTE_ADDR']) and in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])),
    'OWNER_MAIL_ADDRESS' => 'michal.meliska@justice.sk',
    'USER' => user()
];

function user()
{
    $hostname = str_replace('.justice.sk', '', strtolower(gethostbyaddr($_SERVER['REMOTE_ADDR'])));

    if ($hostname === 'ksttmeliska') return 'owner';
    elseif (in_array($hostname, [
        'ksttbenko',
        'ksttbedec',
        'kstthejcik',
        'kstthrubala',
        'ksttcepko',
        'osseminarik'
    ])) return 'admin';
    else return 'user';
}