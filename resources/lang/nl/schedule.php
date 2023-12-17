<?php
return [

    'resource'=>[
        'single' => 'Taak',
        'plural' => 'Taken',
        'navigation' => 'Instellingen',
        'history' => 'Toon geschiedenis',
    ],
    'fields' => [
        'command' => 'Commando',
        'arguments' => 'Arguments',
        'arguments' => 'Argumenten',
        'options' => 'Opties',
        'options_with_value' => 'Opties met waarde',
        'expression' => 'Cron Uitdrukking',
        'log_filename' => 'Log bestandsnaam',
        'output' => 'Uitvoer',
        'even_in_maintenance_mode' => 'Zelfs in onderhoudsmodus',
        'without_overlapping' => 'Zonder overlapping',
        'on_one_server' => 'Uitvoeren van planning alleen op één server',
        'webhook_before' => 'Webhook Vóór',
        'webhook_after' => 'Webhook Na',
        'email_output' => 'E-mail voor het verzenden van uitvoer',
        'sendmail_success' => 'Stuur e-mail bij succesvolle uitvoering van het commando',
        'sendmail_error' => 'Stuur e-mail bij mislukte uitvoering van het commando',
        'log_success' => 'Schrijf commando-uitvoer in geschiedenistabel bij succesvolle uitvoering van het commando',
        'log_error' => 'Schrijf commando-uitvoer in geschiedenistabel bij mislukte uitvoering van het commando',
        'status' => 'Status',
        'actions' => 'Acties',
        'data-type' => 'Gegevenstype',
        'run_in_background' => 'Uitvoeren op de achtergrond',
        'created_at' => 'Aangemaakt op',
        'updated_at' => 'Bijgewerkt op',
        'never' => 'Nooit',
        'environments' => 'Omgevingen',
    ],
    'messages' => [
        'no-records-found' => 'Geen records gevonden.',
        'save-success' => 'Gegevens succesvol opgeslagen.',
        'save-error' => 'Fout bij het opslaan van gegevens.',
        'timezone' => 'Alle planningen worden uitgevoerd in de tijdzone: ',
        'select' => 'Selecteer een commando',
        'custom' => 'Aangepast Commando',
        'custom-command-here' => 'Aangepast commando hier (bijv. `cat /proc/cpuinfo` of `artisan db:migrate`)',
        'help-cron-expression' => 'Klik hier indien nodig en gebruik een tool om het maken van de cron-uitdrukking te vergemakkelijken',
        'help-log-filename' => 'Als het logbestand is ingesteld, worden de logberichten van deze cron geschreven naar opslag/logs/<log bestandsnaam>.log',
        'help-type' => 'Meerdere :type kunnen worden gespecificeerd, gescheiden door komma\'s',
        'attention-type-function' => "LET OP: parameters van het type 'functie' worden uitgevoerd vóór de uitvoering van de planning en de terugkeerwaarde wordt doorgegeven als parameter. Gebruik met zorg, het kan uw taak breken",
        'delete_cronjob' => 'Verwijder cronjob',
        'delete_cronjob_confirm' => 'Wil je de cronjob ":cronjob" echt verwijderen?',
    ],
    'status' => [
        'active' => 'Actief',
        'inactive' => 'Inactief',
        'trashed' => 'Verwijderd',
    ],
    'buttons' => [
        'inactivate' => 'Deactiveren',
        'activate' => 'Activeren',
        'history' => 'Geschiedenis',

    ],
    'validation' => [
        'cron' => 'Het veld moet worden ingevuld in het cron-uitdrukking formaat.',
        'regex' => __('validation.alpha_dash') . ' ' . 'Komma is ook toegestaan.'
    ]
];
