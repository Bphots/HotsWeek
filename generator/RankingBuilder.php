<?php
namespace hotsweek\generator;

use app\hotsweek\model\Player;
use app\hotsweek\model\PlayerBase;
use app\hotsweek\model\PlayerHeroes;

class RankingBuilder extends Presets
{
    protected $except;
    protected $rootPath;
    protected $playerIDs;
    protected $PlayerBase = [];
    protected $PlayerHeroes = [];

    public function setPath($rootPath, $except = [])
    {
        $this->rootPath = $rootPath;
        $this->except = $except;
        $this->getPlayerIDs();
    }

    public function rank()
    {
        foreach ($this->playerIDs as $playerID) {
            // For EXT3 file system
            $groupID = floor($playerID / 1000);
            $fileName = $this->rootPath . $groupID . DS . $playerID . DS . 'data' . SAVE_EXT;
            if (!file_exists($fileName)) {
                continue;
            }
            $data = @file_get_contents($fileName);
            if ($data) {
                $data = @json_decode($data, true) ?: [];
                if (isset($data[FILENAME_BASE])) {
                    $_data = $data[FILENAME_BASE];
                    $gameTotal = $_data['0-1'][FUNC_SUM]; //game_total
                    $this->compare($this->PlayerBase[FUNC_SUM], $_data, $playerID);
                    $this->compare($this->PlayerBase[FUNC_AVG], $_data, $playerID, $gameTotal);
                }
                if (isset($data[FILENAME_HEROES])) {
                    foreach ($data[FILENAME_HEROES] as $heroID => $hero) {
                        if (!isset($data[FILENAME_HEROES][$heroID])) {
                            $data[FILENAME_HEROES][$heroID] = [];
                        }
                        $_data = $data[FILENAME_HEROES][$heroID];
                        $gameTotal = $_data['0-1'][FUNC_SUM]; //game_total
                        $this->compare($this->PlayerHeroes[$heroID][FUNC_SUM], $_data, $playerID);
                        $this->compare($this->PlayerHeroes[$heroID][FUNC_AVG], $_data, $playerID, $gameTotal);
                    }
                }
            }
        }
    }

    private function getPlayerIDs()
    {
        $playerIDs = [];
        $path = $this->rootPath;
        $root = opendir($path);
        while (($groupID = readdir($root)) !== false) {
            $_path = $path . DS . $groupID;
            $_root = opendir($_path);
            while (($playerID = readdir($_root)) !== false) {
                if (is_numeric($playerID) && is_dir($_path . DS . $playerID) && !in_array($playerID, $this->except)) {
                    $playerIDs[] = (int)$playerID;
                }
            }
        }
        $this->playerIDs = $playerIDs;
    }

    private function compare(&$list, $data, $playerID, $divisor = 1)
    {
        foreach ($data as $key => $value) {
            if (!isset($value['sum'])) {
                continue;
            }
            if (is_array($value['sum'])) {
                foreach ($value['sum'] as $_key => $_value) {
                    $_value = $divisor ? (int)($_value / $divisor) : 0;
                    $this->_compare($list[$key], $_key, $_value, $playerID);
                }
            } else {
                $value['sum'] = $divisor ? (int)($value['sum'] / $divisor) : 0;
                $this->_compare($list, $key, $value['sum'], $playerID);
            }
        }
    }

    private function _compare(&$list, $key, $value, $playerID)
    {
        if (!$value) return;
        if (!isset($list[$key])) {
            $list[$key][0] = [
                $playerID,
                $value,
            ];
        } else {
            foreach ($list[$key] as $v) {
                $ranking[$v[0]] = $v[1];
            }
            isset($ranking[$playerID]) or $ranking[$playerID] = $value;
            arsort($ranking);
            $count = 0;
            foreach ($ranking as $id => $v) {
                if ($count >= 30) break;
                $list[$key][$count] = [
                    $id,
                    $v,
                ];
                $count++;
            }
        }
        // } elseif ($list[$key][0][1] < $value) {
        //     $list[$key][0] = [
        //         $playerID,
        //         $value,
        //     ];
        // }
    }
}
