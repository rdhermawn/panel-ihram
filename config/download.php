<?php

return [
    // Browser metadata and introductory copy.
    'title' => 'Etherna Perfect World | Download',
    'description' => 'Download the Etherna Perfect World client and find the tools needed to prepare it for launch.',
    'intro' => 'Choose a client mirror, unpack the archive, then start the game with Launcher.exe. The launcher can update the client when needed.',

    // Main client download. The archive size and game version are not listed by the source page.
    'client' => [
        'title' => 'Etherna Perfect World Client',
        'description' => 'Download the full client archive and extract it to a location of your choice.',
        'button_label' => 'Download full client',
        'url' => 'https://drive.usercontent.google.com/download?id=1d1PBXxTOdAlEu_GoY0jKM0htUcPZnWGq&export=download&authuser=0',
        'os' => 'Windows',
        'size' => 'Not listed',
        'version' => 'Current release',
    ],
    'mirrors' => [
        [
            'label' => 'MediaFire',
            'url' => 'https://www.mediafire.com/file/aj9v28ahyczik2d/Perfect_World_Offline.7z',
            'size' => 'Not listed',
            'note' => 'Full client mirror',
        ],
        [
            'label' => 'PixelDrain',
            'url' => 'https://bit.ly/4kuq03Y?r=lp&m=Mnb7n2McHZC',
            'size' => 'Not listed',
            'note' => 'Full client mirror',
        ],
    ],
    'patches' => [
        [
            'label' => 'MediaFire · patch archive',
            'url' => 'https://www.mediafire.com/file/m2s775osoph3w4n/ARCADIA_PATCH.rar/file',
            'note' => 'Patcher download',
        ],
        [
            'label' => 'Google Drive · patch archive',
            'url' => 'https://drive.google.com/file/d/1RyETX5TNSWuUVokHEDhIoYx8AHA3Zapu/view?usp=sharing',
            'note' => 'Patcher download',
        ],
        [
            'label' => 'MediaFire · patch mirror',
            'url' => 'https://www.mediafire.com/file/fxmiitk8s7yntlo/ARCADIA_PATCH.rar/file',
            'note' => 'Alternate patch download',
        ],
    ],

    // Four short steps and facts for the download overview.
    'features' => [
        [
            'number' => '01',
            'title' => 'Get the full client',
            'text' => 'Download the game files from the primary link or choose a mirror.',
        ],
        [
            'number' => '02',
            'title' => 'Unpack the archive',
            'text' => 'Extract the client to any convenient location with WinRAR or 7-Zip.',
        ],
        [
            'number' => '03',
            'title' => 'Install the components',
            'text' => 'Use the official runtime links below if your system needs them.',
        ],
        [
            'number' => '04',
            'title' => 'Launch the game',
            'text' => 'Open Launcher.exe in the client folder. It can update the client when needed.',
        ],
    ],

    // Official runtime downloads and recommended archive utilities.
    'components_heading' => 'System components & tools',
    'components_intro' => 'Install a component only if it is missing from your system. Download utilities are optional.',
    'components' => [
        [
            'name' => 'DirectX End-User Runtime',
            'subtitle' => 'Microsoft component',
            'what' => 'DirectX runtime components',
            'when' => 'If the game reports missing DirectX files',
            'url' => 'https://www.microsoft.com/en-us/download/details.aspx?id=35',
        ],
        [
            'name' => 'Microsoft Visual C++ Redistributable',
            'subtitle' => '64-bit package',
            'what' => 'Visual C++ runtime libraries',
            'when' => 'If the game reports a missing runtime or DLL',
            'url' => 'https://aka.ms/vs/17/release/vc_redist.x64.exe',
        ],
        [
            'name' => 'WinRAR',
            'subtitle' => 'Archive utility',
            'what' => 'Extract the downloaded archive',
            'when' => 'Optional',
            'url' => 'https://www.win-rar.com/start.html?&L=0',
        ],
        [
            'name' => '7-Zip',
            'subtitle' => 'Archive utility',
            'what' => 'Extract the downloaded archive',
            'when' => 'Optional',
            'url' => 'https://www.7-zip.org/',
        ],
    ],
    'note' => 'After extraction, launch the game with Launcher.exe from the client folder. The launcher can update files when needed. For help, contact the community on Discord.',
    'discord_nav' => 'https://discord.gg/ZBPj44CuSb',
    'discord_support' => 'https://discord.gg/ZFfvZcuRnj',

    'labels' => [
        'breadcrumb' => 'Downloads / Game client',
        'client_section' => 'Game client',
        'primary_download' => 'Primary download',
        'size' => 'Archive size',
        'version' => 'Version',
        'platform' => 'Platform',
        'current' => 'Always up to date',
        'mirrors' => 'Choose a mirror',
        'download' => 'Download',
        'patches' => 'Patcher downloads',
        'patcher' => 'Patcher',
        'steps' => 'How to get started',
        'name' => 'Name',
        'purpose' => 'Purpose',
        'when' => 'When to install',
        'visit' => 'Visit official site',
        'support' => 'Need help?',
        'discord' => 'Join our Discord',
        'open_external' => 'Opens in a new tab',
        'brand' => 'ETHERNA',
        'game' => 'PERFECT WORLD',
    ],
    'footer' => [
        'tagline' => 'A world of your own. Find your legend among the clouds.',
        'copyright' => 'Etherna Perfect World',
        'back_to_top' => 'Back to top',
        'support_label' => 'Contact support',
        'discord' => 'Discord',
    ],

    'navigation' => [
        'home' => 'Home',
        'download' => 'Download',
        'guide' => 'Guide',
        'rankings' => 'Rankings',
        'login' => 'Log in',
        'register' => 'Create account',
        'main_label' => 'Main navigation',
        'mobile_label' => 'Mobile navigation',
        'footer_label' => 'Footer navigation',
        'open_menu' => 'Open navigation',
    ],
];
