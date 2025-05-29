<?php

namespace Interface;

interface TreshholdInterface
{
    public function validateList($list);
    public function initConfig();
    public function decideLineRoute($list);
}
