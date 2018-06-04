<?php

namespace hotsweek\builder;

use hotsweek\builder\BuilderCommon;

class HeroesDataBuilder extends BuilderCommon
{
    protected $presets = [
        SET => [
            ['last_game_time', TIMESTAMP, true],
            ['CharacterLevel'],
        ],
        COUNTER => [
            ['game_length', GAME_LENGTH],
            ['game_total', INC],
            ['game_win', INC, WIN],
            ['party_total', INC, PARTY],
            ['party_win', INC, PARTY_WIN],
        ],
        COUNTER_SR => [],
        COUNTER_MSRED => [],
        FUNC => [
            ['game_length', BUILD_FIELD_DATA, GAME_MODE, GAME_LENGTH],
            ['game_total', BUILD_FIELD_DATA, GAME_MODE, INC],
            ['maps_length', BUILD_JSON_FIELD_DATA, MAP, GAME_LENGTH],
            ['maps_total', BUILD_JSON_FIELD_DATA, MAP, INC],
            ['game_win', BUILD_FIELD_DATA, GAME_MODE, INC, WIN],
            ['maps_win', BUILD_JSON_FIELD_DATA, MAP, INC, WIN],
            ['SkinAndSkinTint', BUILD_JSON_FIELD_DATA, 'SkinAndSkinTint', INC],
            ['MountAndMountTint', BUILD_JSON_FIELD_DATA, 'MountAndMountTint', INC],
            ['Tier1Talent', BUILD_JSON_FIELD_DATA, MSRED.SEP.'Tier1Talent', INC],
            ['Tier2Talent', BUILD_JSON_FIELD_DATA, MSRED.SEP.'Tier2Talent', INC],
            ['Tier3Talent', BUILD_JSON_FIELD_DATA, MSRED.SEP.'Tier3Talent', INC],
            ['Tier4Talent', BUILD_JSON_FIELD_DATA, MSRED.SEP.'Tier4Talent', INC],
            ['Tier5Talent', BUILD_JSON_FIELD_DATA, MSRED.SEP.'Tier5Talent', INC],
            ['Tier6Talent', BUILD_JSON_FIELD_DATA, MSRED.SEP.'Tier6Talent', INC],
            ['Tier7Talent', BUILD_JSON_FIELD_DATA, MSRED.SEP.'Tier7Talent', INC],
        ],
    ];
}
