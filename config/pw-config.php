<?php




/*
 * @author Harris Marfel <hrace009@gmail.com>
 * @link https://youtube.com/c/hrace009
 * @copyright Copyright (c) 2022.
 */

return [
    'chat_log_path' => '/root/pwserver/logs',
    'currency_name' => 'Coin',
    'encryption_type' => 'base64',
    'level_up_cap' => '150',
    'logo' => 'LOGO PANEL.png',
    'server_ip' => '127.0.0.1',
    'server_name' => 'Perfect World Eterna',
    'gmwa' => '628997200007',
    'fakeonline' => '100',
    'server_version' => '156',
    'teleport_world_tag' => '1',
    'teleport_x' => '1280.6788',
    'teleport_y' => '219.61784',
    'teleport_z' => '1021.2097',

    'item_id' => '600000',
    'price_per_unit' => '1',
    'target' => 'bonuses',
    'bonuses' => 'on',
    'money' => 'on',
	
    'version' => '1.0',
    'ignoreRoles' => '1024,1040,1056,1072',
    'ignoreFaction' => '16,60',
    'system' => [
        'apps' => [
            'news' => true,
            'shop' => true,
            'donate' => true,
            'voucher' => true,
            'inGameService' => true,
            'ranking' => true,
            'vote' => false,
            'captcha' => false
        ],
    ],
    'news' => [
        'page' => '15',
    ],
    'shop' => [
        'page' => '15'
    ],
    'payment' => [
        'paymentwall' => [
            'status' => false,
            'widget_code' => 'p4_1',
            'widget_width' => '371',
            'widget_high' => '450',
            'project_key' => '',
            'secret_key' => '',
            'pingback_path' => 'pingback/paymentwall'
        ],
        'bank_transfer' => [
            'status' => false,
            'double' => false,
            'accountOwner' => '',
            'bankAccountNo1' => '',
            'bankName1' => '',
            'bankAccountNo2' => '',
            'bankName2' => '',
            'bankAccountNo3' => '',
            'bankName3' => '',
            'multiply' => '100000',
            'limit' => '5000000',
            'CurrencySign' => 'Rp.',
            'payment_price' => '1750',
        ],
        'paypal' => [
            'status' => false,
            'double' => false,
            'client_id' => '',
            'secret' => '',
            'currency' => 'USD',
            'currency_per' => '1',
            'minimum' => '1',
            'maximum' => '50',
            'mingetbonus' => '500',
            'bonusess' => '10',
            'sandbox' => true
        ],
        'ipaymu' => [
            'status' => false,
            'double' => false,
            'va' => '',
            'apikey' => '',
            'sandbox' => true
        ]
    ],
    'discord' => 'https://discord.gg/KkNvpg2cf'
];
