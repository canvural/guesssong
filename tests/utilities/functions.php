<?php

function get_fake_data(string $fileName): array
{
    return \json_decode(\file_get_contents(base_path("tests/data/{$fileName}")), TRUE);
}