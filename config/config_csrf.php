<?php
/*
 * Prevent the CsrfProtectionMiddleware from loading for these particular Controller/Action combinations
 * controller string
 * action string|string[]
 * requestType string|string[] http request methods such as GET POST PUT DELETE AJAX
 */
return [
    'Csrf' => [
        'ignore' => [
            ['controller' => 'users', 'action' => 'preLogin', 'requestType' => 'ajax'],
            ['controller' => 'mySchools', 'action' => ['validate', 'configure', 'reviewDates', 'reviewNotifications'], 'requestType' => 'ajax'],
            ['controller' => 'developers', 'action' => ['spreadsheet'], 'requestType' => 'ajax'],
            ['controller' => 'myFolders', 'action' => ['validate'], 'requestType' => 'ajax'],
            ['controller' => 'invitations', 'action' => ['inviteFranchiseeMulti', 'invitePhotoCoordinatorMulti', 'inviteTeacherMulti'], 'requestType' => 'ajax'],
            ['controller' => 'mySubjects', 'action' => ['validate'], 'requestType' => 'ajax'],
            ['controller' => 'sync', 'action' => ['activeSeasons', 'activeSchools'], 'requestType' => 'ajax'],
            ['controller' => 'singleSignOn', 'action' => ['check', 'login'], 'requestType' => 'ajax'],
            ['controller' => 'singleSignOn', 'action' => ['check', 'login'], 'requestType' => 'post'],
            ['controller' => 'singleSignOn', 'action' => ['check', 'login'], 'requestType' => 'get'],
            ['controller' => 'myMessages', 'action' => ['index'], 'requestType' => 'post'],
        ]
    ],
];
