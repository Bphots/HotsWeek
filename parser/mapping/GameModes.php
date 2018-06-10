<?php

namespace hotsweek\parser\mapping;

trait GameModes
{
    protected $gameModesMapping = [
        3 => 'QuickMatch',
        4 => 'HeroLeague',
        5 => 'TeamLeague',
        6 => 'UnrankedDraft',
    ];
}