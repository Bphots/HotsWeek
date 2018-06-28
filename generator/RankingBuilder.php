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
            $fileName = $this->rootPath . $playerID . DS . 'data' . SAVE_EXT;
            $data = @file_get_contents($fileName);
            if ($data) {
                $data = json_decode($data, true);
                if (isset($data[FILENAME_BASE])) {
                    $this->compare($this->PlayerBase, $data[FILENAME_BASE], $playerID);
                }
                if (isset($data[FILENAME_HEROES])) {
                    foreach ($data[FILENAME_HEROES] as $heroID => $hero) {
                        if (!isset($data[FILENAME_HEROES][$heroID])) {
                            $data[FILENAME_HEROES][$heroID] = [];
                        }
                        $this->compare($this->PlayerHeroes, $data[FILENAME_HEROES][$heroID], $playerID);
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
        while (($name = readdir($root)) !== false) {
            $dir = $path . '/' . $name;
            $playerID = (int)$name;
            if (!is_numeric($name) || in_array($playerID, $this->except)) {
                continue;
            } elseif (is_dir($dir)) {
                $playerIDs[] = $playerID;
            }
        }
        $this->playerIDs = $playerIDs;
    }

    private function compare(&$list, $data, $playerID)
    {
        foreach ($data as $key => $value) {
            if (!isset($value['sum'])) {
                continue;
            }
            if (is_array($value['sum'])) {
                foreach ($value['sum'] as $_key => $_value) {
                    $this->_compare($list[$key], $_key, $_value, $playerID);
                }
            } else {
                $this->_compare($list, $key, $value['sum'], $playerID);
            }
        }
    }

    private function _compare(&$list, $key, $value, $playerID)
    {
        if (!isset($list[$key])) {
            $list[$key] = [
                $playerID,
                $value,
            ];
        } elseif ($list[$key][1] < $value) {
            $list[$key] = [
                $playerID,
                $value,
            ];
        }
    }
}
