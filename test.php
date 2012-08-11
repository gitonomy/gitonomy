<?php

$content = file_get_contents($argv[1]);
echo zlib_decode($content);

