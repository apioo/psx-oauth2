<?php

require_once __DIR__ . '/../vendor/autoload.php';

\PSX\Framework\Test\Environment::setup(__DIR__, function(\Doctrine\DBAL\Schema\Schema $fromSchema){
    return;
});

