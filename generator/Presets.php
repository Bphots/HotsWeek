<?php
namespace hotsweek\generator;

const TYPE_SUM = 0;
const TYPE_AVG = 1;
const TYPE_MAX = 2;
const TYPE_MIN = 3;
const FUNC_SUM = 'sum';
const FUNC_AVG = 'avg';
const FUNC_MAX = 'max';
const FUNC_MIN = 'min';
const SAVE_EXT = '.json';
const FILENAME_PLAYERINFO = 'PlayerInfo';
const FILENAME_BASE = 'PlayerBase';
const FILENAME_HEROES = 'PlayerHeroes';
const FILENAME_ENEMIES = 'PlayerEnemies';
const FILENAME_MATES = 'PlayerMates';
const FILENAME_RANKINGS = 'PlayerRankings';
const UNKNOWN = 'Unknown';

class Presets
{
    protected $items = [FILENAME_PLAYERINFO, FILENAME_BASE, FILENAME_HEROES, FILENAME_ENEMIES, FILENAME_MATES, FILENAME_RANKINGS];
    protected $presets = [
        // 0: Field name
        // 1: Is it JSON ?
        // 2: Items (base,heroes,enemies,mates,rankings) - Methods (sum,avg,max,min)
        [[
            'game_length', 'game_total', 'game_win',
            'game_length_QuickMatch', 'game_length_HeroLeague',
            'game_length_TeamLeague', 'game_length_UnrankedDraft',
            'game_total_QuickMatch', 'game_total_HeroLeague',
            'game_total_TeamLeague', 'game_total_UnrankedDraft',
            'game_win_QuickMatch', 'game_win_HeroLeague',
            'game_win_TeamLeague', 'game_win_UnrankedDraft',
        ], false, [
            [1, 1, 1, 1], [1, 0, 0, 0], [1, 0, 0, 0], [1, 0, 0, 0], false
        ]],
        [[
            'party_total', 'party_win',
        ], false, [
            [1, 1, 1, 1], [1, 0, 0, 0], false, [1, 0, 0, 0], false
        ]],
        [[
            'team1_count', 'Level', 'Takedowns', 'SoloKills',
            'Assists', 'Deaths', 'HighestKillStreak', 'HeroDamage',
            'SiegeDamage', 'StructureDamage', 'MinionDamage',
            'CreepDamage', 'SummonDamage', 'TimeCCdEnemyHeroes',
            'Healing', 'SelfHealing', 'DamageTaken', 'DamageSoaked',
            'ExperienceContribution', 'TownKills', 'TimeSpentDead',
            'MercCampCaptures', 'WatchTowerCaptures', 'MetaExperience',
            'ProtectionGivenToAllies', 'TimeSilencingEnemyHeroes',
            'TimeRootingEnemyHeroes', 'TimeStunningEnemyHeroes',
            'ClutchHealsPerformed', 'EscapesPerformed',
            'VengeancesPerformed', 'TeamfightEscapesPerformed',
            'OutnumberedDeaths', 'TeamfightHealingDone',
            'TeamfightDamageTaken', 'TeamfightHeroDamage',
            'EndOfMatchAwardGivenToNonwinner', 'OnFireTimeOnFire',
            'TimeOnPoint', 'TeamWinsDiablo', 'TeamWinsFemale',
            'TeamWinsMale', 'TeamWinsStarCraft', 'TeamWinsWarcraft',
            'WinsWarrior', 'WinsAssassin', 'WinsSupport', 'WinsSpecialist',
            'WinsStarCraft', 'WinsDiablo', 'WinsWarcraft', 'WinsMale',
            'WinsFemale', 'PlaysStarCraft', 'PlaysDiablo', 'PlaysOverwatch',
            'PlaysWarCraft', 'PlaysWarrior', 'PlaysAssassin', 'PlaysSupport',
            'PlaysSpecialist', 'PlaysMale', 'PlaysFemale',
            'DragonNumberOfDragonCaptures', 'DragonShrinesCaptured',
            'GardensSeedsCollected', 'GardensPlantDamage', 'AltarDamageDone',
            'DamageDoneToImmortal', 'DamageDoneToShrineMinions', 'GemsTurnedIn',
            'RavenTributesCollected', 'CurseDamageDone',
            'MinesSkullsCollected', 'BlackheartDoubloonsCollected',
            'BlackheartDoubloonsTurnedIn', 'TimeInTemple', 'DamageDoneToZerg',
            'NukeDamageDone', 'TimeOnPayload', 'party_total_2', 'party_win_2',
            'party_total_3', 'party_win_3', 'party_total_4', 'party_win_4',
            'party_total_5', 'party_win_5', 'win_by_advice'
        ], false, [
            [1, 1, 1, 1], false, false, false, false
        ]],
        [[
            'maps_length', 'maps_total', 'maps_win', 'GameMode_win_by_advice'
        ], true, [
            [1, 1, 1, 1], false, false, false, false
        ]],
        [[
            'Level_count', 'Takedowns_count', 'SoloKills_count',
            'Assists_count', 'Deaths_count', 'HighestKillStreak_count',
            'MatchAwards'
        ], true, [
            [1, 0, 0, 0], false, false, false, false
        ]],
        [['last_game_time'], false, [
            [0, 0, 1, 0], [0, 0, 1, 0], [0, 0, 1, 0], [0, 0, 1, 0], false
        ]],
        [[
            'rank_avg_game_length', 'rank_total_game_length', 'rank_total_game',
            'rank_total_win', 'rank_win_rate', 'rank_avg_solo_kills',
            'rank_avg_takedowns', 'rank_avg_deaths', 'rank_avg_experience',
            'rank_avg_hero_damage', 'rank_avg_siege_damage', 'rank_avg_damage_taken',
            'rank_avg_camp', 'rank_avg_teamfight_damage', 'rank_avg_teamfight_damage_taken',
            'rank_avg_outnumbered_deaths', 'rank_win_rate_quickMatch', 'rank_win_rate_unrankedDraft',
            'rank_win_rate_teamLeague', 'rank_win_rate_heroLeague', 'rank_kda'
        ], false, [
            false, false, false, false, [1, 0, 0, 0]
        ]],
        
    ];

    static public function __callStatic($name, $args)
    {
        $model = new self;
        return $model->$name();
    }

    private function getFieldsMapping()
    {
        $mapping = [];
        foreach ($this->presets as $key1 => $preset) {
            $fields = $preset[0];
            foreach ($fields as $key2 => $field) {
                $alias = $this->alias($key1, $key2);
                $mapping[$alias] = $field;
            }
        }
        return $mapping;
    }

    private function getFileExt()
    {
        return SAVE_EXT;
    }

    protected function alias($key1, $key2)
    {
        return "$key1-$key2";
    }

    public function save($path, $fileName = 'data')
    {
        $data = [];
        foreach ($this->items as $name) {
            if (isset($this->$name) && $this->$name) {
                $data[$name] = $this->$name;
            }
        }
        if (empty($data)) {
            return false;
        }
        // $this->_save($data, $path);
        is_dir($path) or mkdir($path, 0755, true);
        file_put_contents($path . $fileName . SAVE_EXT, json_encode($data, JSON_UNESCAPED_UNICODE));
        return true;
    }

    // protected function _save($data, $path)
    // {
    //     foreach ($data as $name => $each) {
    //         if (!$each) {
    //             continue;
    //         }
    //         if (is_array($each)) {
    //             $this->_save($each, $path . $name . DS);
    //         } else {
    //             file_put_contents($path . $name . SAVE_EXT, $each);
    //         }
    //     }
    // }
}