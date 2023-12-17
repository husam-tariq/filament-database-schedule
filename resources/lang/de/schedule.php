<?php
return [

    'resource'=>[
        'single' => 'Zeitplan',
        'plural' => 'Zeitpläne',
        'navigation' => 'Einstellungen',
        'history' => 'Laufhistorie anzeigen',
    ],
    'fields' => [
        'command' => 'Befehl',
        'arguments' => 'Argumente',
        'options' => 'Optionen',
        'options_with_value' => 'Optionen mit Wert',
        'expression' => 'Cron-Ausdruck',
        'log_filename' => 'Logdateiname',
        'output' => 'Ausgabe',
        'even_in_maintenance_mode' => 'Auch im Wartungsmodus',
        'without_overlapping' => 'Ohne Überlappung',
        'on_one_server' => 'Ausführung nur auf einem Server',
        'webhook_before' => 'URL vorher',
        'webhook_after' => 'URL nachher',
        'email_output' => 'E-Mail für die Ausgabe senden',
        'sendmail_success' => 'E-Mail bei erfolgreicher Ausführung des Befehls senden',
        'sendmail_error' => 'E-Mail bei fehlgeschlagener Ausführung des Befehls senden',
        'log_success' => 'Befehlsausgabe bei erfolgreicher Ausführung in die Verlaufstabelle schreiben',
        'log_error' => 'Befehlsausgabe bei fehlgeschlagener Ausführung in die Verlaufstabelle schreiben',
        'status' => 'Status',
        'actions' => 'Aktionen',
        'data-type' => 'Datentyp',
        'run_in_background' => 'Im Hintergrund ausführen',
        'created_at' => 'Erstellt am',
        'updated_at' => 'Aktualisiert am',
        'never' => 'Nie',
        'environments' => 'Umgebungen'
    ],
    'messages' => [
        'no-records-found' => 'Keine Datensätze gefunden.',
        'save-success' => 'Daten erfolgreich gespeichert.',
        'save-error' => 'Fehler beim Speichern der Daten.',
        'timezone' => 'Alle Zeitpläne werden in der Zeitzone ausgeführt: ',
        'select' => 'Wählen Sie einen Befehl aus',
        'custom' => 'Benutzerdefinierter Befehl',
        'custom-command-here' => 'Benutzerdefinierter Befehl hier (z. B. `cat /proc/cpuinfo` oder `artisan db:migrate`)',
        'help-cron-expression' => 'Wenn nötig, klicken Sie hier und verwenden Sie ein Tool, um die Erstellung des Cron-Ausdrucks zu erleichtern',
        'help-log-filename' => 'Wenn eine Logdatei festgelegt ist, werden die Lognachrichten dieses Cron in storage/logs/<Logdateiname>.log geschrieben',
        'help-type' => 'Mehrere :type können durch Kommas getrennt angegeben werden',
        'attention-type-function' => "ACHTUNG: Parameter vom Typ 'function' werden vor der Ausführung des Zeitplans ausgeführt, und das Ergebnis wird als Parameter übergeben. Verwenden Sie es mit Vorsicht, es kann Ihre Aufgabe unterbrechen",
        'delete_cronjob' => 'Zeitplan löschen',
        'delete_cronjob_confirm' => 'Möchten Sie den Zeitplan ":cronjob" wirklich löschen?'
    ],
    'status' => [
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'trashed' => 'Gelöscht',
    ],
    'buttons' => [
        'inactivate' => 'Inaktivieren',
        'activate' => 'Aktivieren',
        'history' => 'Verlauf',

    ],
    'validation' => [
        'cron' => 'Das Feld muss im Format des Cron-Ausdrucks ausgefüllt werden.',
        'regex' => __('validation.alpha_dash') . ' ' . 'Komma ist ebenfalls erlaubt.'
    ]
];
