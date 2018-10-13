<?php
namespace hotsweek\generator;

use app\hotsweek\model\Player;
use app\hotsweek\model\TempPlayerBase as PlayerBase;
use app\hotsweek\model\TempPlayerHeroes as PlayerHeroes;
use app\hotsweek\model\TempPlayerEnemies as PlayerEnemies;
use app\hotsweek\model\TempPlayerMates as PlayerMates;
use app\hotsweek\model\TempPlayerRankings as PlayerRankings;

class Counter extends Presets
{
    protected $playerID;
    protected $PlayerBase;
    protected $PlayerHeroes;
    protected $PlayerEnemies;
    protected $PlayerMates;
    protected $PlayerInfo;
    protected $PlayerRankings;

    public function setPlayer($playerID)
    {
        $this->playerID = $playerID;
    }

    public function pushPlayerInfo()
    {
        $player = Player::get($this->playerID);
        $playerName = $player ? $player->Name . '#' . $player->BattleTag : UNKNOWN;
        $playerRegion = $player ? $player->BattleNetRegionId : UNKNOWN;
        $this->PlayerInfo = [
            'name' => $playerName,
            'region' => $playerRegion,
        ];
    }

    public function countBaseData()
    {
        $limit = [];
        $this->playerID && $limit['player_id'] = $this->playerID;
        $data = [];
        PlayerBase::where($limit)->chunk(1000, function ($rows) use (&$data) {
            foreach ($rows as $row) {
                $this->mergePlayers($data, $row, 0);
            }
        });
        if (!$data) {
            return;
        }
        $this->PlayerBase = $this->count($data, 0);
        unset($data);
    }

    protected function mergePlayers(&$list, $data, $itemKey)
    {
        isset($list[$data->date]) or $list[$data->date] = [];
        $tmp = $list[$data->date];
        foreach ($this->presets as $preset) {
            if (!$preset[2][$itemKey]) {
                continue;
            }
            $fields = $preset[0];
            $isJson = $preset[1];
            foreach ($fields as $field) {
                if ($isJson) {
                    isset($tmp[$field]) or $tmp[$field] = [];
                    $jsonData = json_decode($data[$field], true) ?: [];
                    if (!$jsonData) {
                        continue;
                    }
                    foreach ($jsonData as $key => $value) {
                        isset($tmp[$field][$key]) or $tmp[$field][$key] = 0;
                        $tmp[$field][$key] += $value;
                    }
                } else {
                    isset($tmp[$field]) or $tmp[$field] = 0;
                    $tmp[$field] += $data[$field];
                }
            }
        }
        $list[$data->date] = $tmp;
    }

    public function countHeroesData()
    {
        $limit = [];
        $this->playerID && $limit['player_id'] = $this->playerID;
        $data = [];
        PlayerHeroes::where($limit)->chunk(1000, function ($rows) use (&$data) {
            foreach ($rows as $row) {
                $data[$row->hero_id][] = $row;
            }
        });
        if (!$data) {
            return;
        }
        foreach ($data as $heroID => $each) {
            $this->PlayerHeroes[$heroID] = $this->count($each, 1);
        }
        unset($data);
    }

    public function countEnemiesData()
    {
        if (!$this->playerID) {
            return;
        }
        $limit = [];
        $this->playerID && $limit['player_id|player2_id'] = $this->playerID;
        $data = [];
        PlayerEnemies::where($limit)->chunk(1000, function ($rows) use (&$data) {
            foreach ($rows as $row) {
                $enemyID = $row->player_id == $this->playerID ? $row->player2_id : $row->player_id;
                $data[$enemyID][] = $row;
            }
        });
        if (!$data) {
            return;
        }
        foreach ($data as $enemyID => $each) {
            $this->PlayerEnemies[$enemyID] = $this->count($each, 2);
        }
        unset($data);
    }

    public function countMatesData()
    {
        if (!$this->playerID) {
            return;
        }
        $limit = [];
        $this->playerID && $limit['player_id|player2_id'] = $this->playerID;
        // $data = PlayerMates::all($limit);
        $data = [];
        PlayerMates::where($limit)->chunk(1000, function ($rows) use (&$data) {
            foreach ($rows as $row) {
                $mateID = $row->player_id == $this->playerID ? $row->player2_id : $row->player_id;
                $data[$mateID][] = $row;
            }
        });
        if (!$data) {
            return;
        }
        foreach ($data as $mateID => $each) {
            $this->PlayerMates[$mateID] = $this->count($each, 3);
        }
        unset($data);
    }

    public function countRankingsData()
    {
        $result = [];
        $limit = [];
        $itemKey = 4;
        $this->playerID && $limit['player_id'] = $this->playerID;
        $data = PlayerRankings::where($limit)->find();
        if (!$data) {
            return;
        }
        foreach ($this->presets as $key1 => $preset) {
            if (!$preset[2][$itemKey]) {
                continue;
            }
            $fields = $preset[0];
            foreach ($fields as $key2 => $field) {
                $alias = $this->alias($key1, $key2);
                $result[$alias] = $data->$field;
            }
        }
        $this->PlayerRankings = $result;
    }

    public function countGlobalRankingsPlayerNumbers()
    {
        $result = [];
        $limit = [];
        $itemKey = 4;
        $datas = PlayerRankings::where($limit)->select();
        if (!$datas) {
            return;
        }
        foreach ($this->presets as $key1 => $preset) {
            if (!$preset[2][$itemKey]) {
                continue;
            }
            $fields = $preset[0];
            foreach ($fields as $key2 => $field) {
                $alias = $this->alias($key1, $key2);
                $result[$alias] = [
                    PlayerRankings::where(array_merge($limit, [$field => ['>', 0]]))->count(),
                    PlayerRankings::where($limit)->limit(100)->order("$field asc")->column('player_id'),
                ];
                // foreach ($datas as $data) {
                //     if ($data->$field) {
                //         isset($result[$alias]) or $result[$alias] = 0;
                //         $result[$alias]++;
                //     }
                // }
            }
        }
        $this->PlayerRankings = $result;
        unset($result);
        unset($data);
    }

    protected function count($data, $itemKey)
    {
        $return = [];
        foreach ($this->presets as $key1 => $preset) {
            if (!$preset[2][$itemKey]) {
                continue;
            }
            $fields = $preset[0];
            $isJson = $preset[1];
            $type = $preset[2][$itemKey];
            foreach ($fields as $key2 => $field) {
                $alias = $this->alias($key1, $key2);
                if ($isJson) {
                    $this->countJson($return, $data, $field, $type, $alias);
                } else {
                    $this->countNumber($return, $data, $field, $type, $alias);
                }
            }
        }
        return $return;
    }

    protected function countNumber(&$return, $data, $field, $type, $alias = null)
    {
        $count = 0;
        $total = count($data);
        $max = $min = isset($data[0]) ? $data[0][$field] : 0;
        foreach ($data as $each) {
            $count += $each[$field];
            $max >= $each[$field] or $max = $each[$field];
            $min <= $each[$field] or $min = $each[$field];
        }
        $name = $alias ?: $field;
        // sum
        if ($type[TYPE_SUM]) {
            $return[$name][FUNC_SUM] = $count;
        }
        // avg
        if ($type[TYPE_AVG] && $total) {
            $return[$name][FUNC_AVG] = round($count / $total, 4);
        }
        // max
        if ($type[TYPE_MAX]) {
            $return[$name][FUNC_MAX] = $max;
        }
        // min
        if ($type[TYPE_MIN]) {
            $return[$name][FUNC_MIN] = $min;
        }
    }

    protected function countJson(&$return, $data, $field, $type, $alias = null)
    {
        $count = $max = $min = [];
        $total = count($data);
        foreach ($data as $each) {
            $array = is_array($each[$field]) ? $each[$field] : json_decode($each[$field], true);
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
        $name = $alias ?: $field;
        // sum
        if ($type[TYPE_SUM]) {
            $return[$name][FUNC_SUM] = $count;
        }
        // avg
        if ($type[TYPE_AVG] && $total) {
            $avg = [];
            foreach ($count as $key => $value) {
                $avg[$key] = round($value / $total, 4);
            }
            $return[$name][FUNC_AVG] = $avg;
        }
        // max
        if ($type[TYPE_MAX]) {
            $return[$name][FUNC_MAX] = $max;
        }
        // min
        if ($type[TYPE_MIN]) {
            $return[$name][FUNC_MIN] = $min;
        }
    }
}
