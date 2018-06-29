<?php

namespace hotsweek\parser\mapping;

trait GameModes
{
    protected $gameModesMapping = [
        'QuickMatch'    =>  GAMEMODE_QUICKMATCH,
        'HeroLeague'    =>  GAMEMODE_HEROLEAGUE,
        'TeamLeague'    =>  GAMEMODE_TEAMLEAGUE,
        'UnrankedDraft' =>  GAMEMODE_UNRANKEDDRAFT,
    ];
}