<?php
namespace hotsweek\generator;

use app\hotsweek\model\PlayerBase;
use app\hotsweek\model\PlayerHeroes;
use app\hotsweek\model\PlayerEnemies;
use app\hotsweek\model\PlayerMates;

class Counter extends Presets
{
    protected $weekNumber;
    protected $playerID;
    protected $PlayerBase;
    protected $PlayerHeroes;
    protected $PlayerEnemies;
    protected $PlayerMates;

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
        $this->PlayerBase = $this->count($data, 0);
        // $this->PlayerBase = json_encode($this->count($data, 0), JSON_UNESCAPED_UNICODE);
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
            $this->PlayerHeroes[$heroID] = $this->count($each, 1);
            // $this->PlayerHeroes[$heroID] = json_encode($this->count($each, 1), JSON_UNESCAPED_UNICODE);
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
            $this->PlayerEnemies[$enemyID] = $this->count($each, 2);
            // $this->PlayerEnemies[$enemyID] = json_encode($this->count($each, 2), JSON_UNESCAPED_UNICODE);
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
            $this->PlayerMates[$mateID] = $this->count($each, 3);
            // $this->PlayerMates[$mateID] = json_encode($this->count($each, 3), JSON_UNESCAPED_UNICODE);
        }
    }


    public function save($path)
    {
        $data = [];
        foreach ($this->items as $name) {
            if ($this->$name) {
                $data[$name] = $this->$name;
            }
        }
        if (empty($data)) {
            return false;
        }
        $this->_save($data, $path);
        return true;
    }

    protected function _save($data, $path)
    {
        is_dir($path) or mkdir($path, 0755, true);
        file_put_contents($path . 'data' . SAVE_EXT, json_encode($data, JSON_UNESCAPED_UNICODE));
        // foreach ($data as $name => $each) {
        //     if (!$each) {
        //         continue;
        //     }
        //     if (is_array($each)) {
        //         $this->_save($each, $path . $name . DS);
        //     } else {
        //         file_put_contents($path . $name . SAVE_EXT, $each);
        //     }
        // }
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
