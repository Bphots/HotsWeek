<?php

namespace hotsweek\parser\mapping;

trait GameModes
{
    protected $gameModesMapping = [
        GAMEMODE_QUICKMATCH => 'QuickMatch',
        GAMEMODE_HEROLEAGUE => 'HeroLeague',
        GAMEMODE_TEAMLEAGUE => 'TeamLeague',
        GAMEMODE_UNRANKEDDRAFT => 'UnrankedDraft',
    ];
}