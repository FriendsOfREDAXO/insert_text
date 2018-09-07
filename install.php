<?php

rex_file::copy(
    $this->getPath('assets/oswald-bold.ttf'),
    rex_path::media('oswald-bold.ttf')
);

rex_mediapool_syncFile('oswald-bold.ttf', 0, 'Font Oswald Bold');