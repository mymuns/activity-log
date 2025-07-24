<?php

namespace ActivityLog\Writers;

interface LogWriterInterface
{
    public function write(array $data): void;
}

