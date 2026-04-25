<?php
echo json_encode(['status' => 'ok', 'port' => getenv('PORT')]);