<?php

namespace hotsweek\builder;

use hotsweek\builder\BuilderCommon;

class EnemiesDataBuilder extends BuilderCommon
{
    protected $player2HeroID;
    protected $presets = [
        SET => [
            ['last_game_time', TIMESTAMP, true],
        ],
        COUNTER => [
            ['game_length', GAME_LENGTH],
            ['game_total', INC],
            ['game_win', INC, WIN],
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
            ['hero_player', BUILD_JSON_FIELD_DATA, PLAYER_HERO_ID, INC],
            ['hero_player2', BUILD_JSON_FIELD_DATA, PLAYER2_HERO_ID, INC],
        ],
    ];

    public function __construct($model, $base, $personal, $player2)
    {
        parent::__construct($model, $base, $personal);
        if (isset($this->heroesMapping[$player2['Character']])) {
            $this->player2HeroID = $this->heroesMapping[$player2['Character']];
        }
    }
}
