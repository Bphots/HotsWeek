<?php
namespace hotsweek\generator;

use app\hotsweek\model\PlayerBase;
use app\hotsweek\model\PlayerHeroes;
use app\hotsweek\model\PlayerEnemies;
use app\hotsweek\model\PlayerMates;

const TYPE_SUM = 0;
const TYPE_AVG = 1;
const TYPE_MAX = 2;
const TYPE_MIN = 3;
const FUNC_SUM = 'sum';
const FUNC_AVG = 'avg';
const FUNC_MAX = 'max';
const FUNC_MIN = 'min';
const FILENAME_BASE = 'PlayerBase';
const FILENAME_HEROES = 'PlayerHeroes';
const FILENAME_ENEMIES = 'PlayerEnemies';
const FILENAME_MATES = 'PlayerMates';
const SAVE_EXT = '.json';

class Counter
{
    protected $weekNumber;
    protected $playerID;
    protected $PlayerBase;
    protected $PlayerHeroes;
    protected $PlayerEnemies;
    protected $PlayerMates;
    protected $items = [FILENAME_BASE, FILENAME_HEROES, FILENAME_ENEMIES, FILENAME_MATES];
    protected $presets = [
        // 0: Field name
        // 1: Is it JSON ?
        // 2: Items (base,heroes,enemies,mates) - Methods (sum,avg,max,min)
        [[
            'game_length', 'game_total', 'game_win',
            'game_length_QuickMatch', 'game_length_HeroLeague',
            'game_length_TeamLeague', 'game_length_UnrankedDraft',
            'game_total_QuickMatch', 'game_total_HeroLeague',
            'game_total_TeamLeague', 'game_total_UnrankedDraft',
            'game_win_QuickMatch', 'game_win_HeroLeague',
            'game_win_TeamLeague', 'game_win_UnrankedDraft',
        ], false, [
            [1, 1, 1, 1], [1, 0, 0, 0], [1, 0, 0, 0], [1, 0, 0, 0]
        ]],
        [[
            'party_total', 'party_win',
        ], false, [
            [1, 1, 1, 1], [1, 0, 0, 0], false, [1, 0, 0, 0]
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
            'party_total_5', 'party_win_5'
        ], false, [
            [1, 1, 1, 1], false, false, false
        ]],
        [[
            'maps_length', 'maps_total', 'maps_win'
        ], true, [
            [1, 1, 1, 1], false, false, false
        ]],
        [[
            'Level_count', 'Takedowns_count', 'SoloKills_count',
            'Assists_count', 'Deaths_count', 'HighestKillStreak_count',
            'MatchAwards'
        ], true, [
            [0, 0, 1, 0], false, false, false
        ]],
        [['last_game_time'], false, [
            [0, 0, 1, 0], [0, 0, 1, 0], [0, 0, 1, 0], [0, 0, 1, 0]
        ]],
    ];

    public function setWeek($weekNumber)
    {
        $this->weekNumber = $weekNumber;
    }

    public function setPlayer($playerID)
    {
        $this->playerID = $playerID;
    }

    public function countBaseData()
    {
        $limit = [];
        $this->weekNumber && $limit['week_number'] = $this->weekNumber;
        $this->playerID && $limit['player_id'] = $this->playerID;
        $data = PlayerBase::all($limit);
        if (!$data) {
            return;
        }
        $this->PlayerBase = json_encode($this->count($data, 0), JSON_UNESCAPED_UNICODE);
    }
    
    public function countHeroesData()
    {
        $limit = [];
        $this->weekNumber && $limit['week_number'] = $this->weekNumber;
        $this->playerID && $limit['player_id'] = $this->playerID;
        $data = PlayerHeroes::all($limit);
        if (!$data) {
            return;
        }
        $list = [];
        foreach ($data as $each) {
            $list[$each->hero_id][] = $each;
        }
        foreach ($list as $heroID => $each) {
            $this->PlayerHeroes[$heroID] = json_encode($this->count($each, 1), JSON_UNESCAPED_UNICODE);
        }
    }

    public function countEnemiesData()
    {
        if (!$this->playerID) {
            return;
        }
        $limit = [];
        $this->weekNumber && $limit['week_number'] = $this->weekNumber;
        $this->playerID && $limit['player_id|player2_id'] = $this->playerID;
        $data = PlayerEnemies::all($limit);
        if (!$data) {
            return;
        }
        $list = [];
        foreach ($data as $each) {
            $enemyID = $each->player_id == $this->playerID ? $each->player2_id : $each->player_id;
            $list[$enemyID][] = $each;
        }
        foreach ($list as $enemyID => $each) {
            $this->PlayerEnemies[$enemyID] = json_encode($this->count($each, 2), JSON_UNESCAPED_UNICODE);
        }
    }

    public function countMatesData()
    {
        if (!$this->playerID) {
            return;
        }
        $limit = [];
        $this->weekNumber && $limit['week_number'] = $this->weekNumber;
        $this->playerID && $limit['player_id|player2_id'] = $this->playerID;
        $data = PlayerMates::all($limit);
        if (!$data) {
            return;
        }
        $list = [];
        foreach ($data as $each) {
            $mateID = $each->player_id == $this->playerID ? $each->player2_id : $each->player_id;
            $list[$mateID][] = $each;
        }
        foreach ($list as $mateID => $each) {
            $this->PlayerMates[$mateID] = json_encode($this->count($each, 3), JSON_UNESCAPED_UNICODE);
        }
    }


    public function save($path)
    {
        $isEmpty = true;
        foreach ($this->items as $name) {
            if ($this->$name) {
                $isEmpty = false;
            }
            $data[$name] = $this->$name;
        }
        if ($isEmpty) {
            return;
        }
        $this->_save($data, $path);
    }

    protected function _save($data, $path)
    {
        is_dir($path) or mkdir($path, 0755, true);
        foreach ($data as $name => $each) {
            if (!$each) {
                continue;
            }
            if (is_array($each)) {
                $this->_save($each, $path . $name . DS);
            } else {
                file_put_contents($path . $name . SAVE_EXT, $each);
            }
        }
    }

    protected function count($data, $itemKey)
    {
        $return = [];
        foreach ($this->presets as $preset) {
            if (!$preset[2][$itemKey]) {
                continue;
            }
            $fields = $preset[0];
            $isJson = $preset[1];
            $type = $preset[2][$itemKey];
            foreach ($fields as $field) {
                if ($isJson) {
                    $this->countJson($return, $data, $field, $type);
                } else {
                    $this->countNumber($return, $data, $field, $type);
                }
            }
        }
        return $return;
    }

    protected function countNumber(&$return, $data, $field, $type)
    {
        $count = 0;
        $total = count($data);
        $max = $min = isset($data[0]) ? $data[0][$field] : 0;
        foreach ($data as $each) {
            $count += $each[$field];
            $max >= $each[$field] or $max = $each[$field];
            $min <= $each[$field] or $min = $each[$field];
        }
        // sum
        if ($type[TYPE_SUM]) {
            $return[$field][FUNC_SUM] = $count;
        }
        // avg
        if ($type[TYPE_AVG] && $total) {
            $return[$field][FUNC_AVG] = round($count / $total, 4);
        }
        // max
        if ($type[TYPE_MAX]) {
            $return[$field][FUNC_MAX] = $max;
        }
        // min
        if ($type[TYPE_MIN]) {
            $return[$field][FUNC_MIN] = $min;
        }
    }

    protected function countJson(&$return, $data, $field, $type)
    {
        $count = $max = $min = [];
        $total = count($data);
        foreach ($data as $each) {
            $array = json_decode($each[$field], true);
            if (!$array) {
                continue;
            }
            foreach ($array as $key => $value) {
                if (!isset($count[$key])) {
                    $count[$key] = 0;
                }
                if (!isset($max[$key])) {
                    $max[$key] = $value;
                }
                if (!isset($min[$key])) {
                    $min[$key] = $value;
                }
                $count[$key] += $value;
                $max[$key] >= $value or $max[$key] = $value;
                $min[$key] <= $value or $min[$key] = $value;
            }
        }
        // sum
        if ($type[TYPE_SUM]) {
            $return[$field][TYPE_SUM] = $count;
        }
        // avg
        if ($type[TYPE_AVG] && $total) {
            $avg = [];
            foreach ($count as $key => $value) {
                $avg[$key] = round($value / $total, 4);
            }
            $return[$field][TYPE_AVG] = $avg;
        }
        // max
        if ($type[TYPE_MAX]) {
            $return[$field][TYPE_MAX] = $max;
        }
        // min
        if ($type[TYPE_MIN]) {
            $return[$field][TYPE_MIN] = $min;
        }
    }
}
