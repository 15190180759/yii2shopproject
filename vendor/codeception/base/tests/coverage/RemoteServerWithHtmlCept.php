<?php
$I = new CoverGuy($scenario);
$I->wantTo('try generate remote codecoverage xml report');
$I->amInPath('tests/data/sandbox');
$I->executeCommand('run remote_server --coverage-html remote_server');
$I->seeFileFound('index.php', 'tests/_output/remote_server');
