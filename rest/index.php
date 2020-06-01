<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

require __DIR__ . '/ext/Twilio/autoload.php';



include_once 'lib/sub-accounts.php';
include_once 'lib/phone-numbers.php';
include_once 'lib/recordings.php';

include_once 'ext/epiphany/Epi.php';
Epi::setSetting('exceptions', true);
Epi::setPath('base', 'ext/epiphany');
Epi::init('route', 'session');
EpiSession::employ(array(EpiSession::PHP));

getRoute()->get('/sub-accounts/all/(.*)/(.*)/([0-9]+)', array('SubAccounts', 'all'));
getRoute()->post('/sub-accounts/status/(.*)/(.*)/(.*)/(.*)', array('SubAccounts', 'status'));

getRoute()->get('/phone-numbers/all/(.*)/(.*)/([0-9]+)', array('PhoneNumbers', 'all'));
getRoute()->delete('/phone-numbers/delete/(.*)/(.*)/(.*)', array('PhoneNumbers', 'delete'));

getRoute()->get('/recordings/all/(.*)/(.*)/([0-9]+)', array('Recordings', 'all'));
getRoute()->delete('/recordings/delete/(.*)/(.*)/(.*)', array('Recordings', 'delete'));

getRoute()->run();

