<?php
namespace hotsweek\behavior;

use hotsweek\parser\builder\BuilderMappings;
use app\hotsweek\model\Player;
use app\common\model\Maps;

include_once __DIR__ . '/../parser/Constants.php';

class IsWinByAdivce extends BuilderMappings
{
    protected $weekNumber;
    protected $content;
    protected $map;
    protected $mapID;
    protected $gameMode;
    protected $heroList;
    protected $heroPlayers;
    protected $recordField      =   'win_by_adivce';
    protected $recordFieldMap   =   'map_win_by_adivce';
    protected $banIndex         =   [0, 8, 1, 7];
    protected $gameModeLimit    =   [GAMEMODE_HEROLEAGUE, GAMEMODE_TEAMLEAGUE, GAMEMODE_UNRANKEDDRAFT];

    public function run(&$data)
    {
        $this->weekNumber = $data['weekNumber'];
        $this->content = $data['content'];
        if (!$this->getGameMode() || !$this->getMap() || !$this->getHeroList() || !$this->getHeroPlayers()) {
            return false;
        }
        for ($i = 0; $i < 14; $i++) {
            if (in_array($i, $this->banIndex)) {
                continue;
            }
            if ($this->IsOnAdviceRanking($i)) {
                $this->record($i);
            }
        }
    }

    protected function getGameMode()
    {
        $gameMode = $this->content['GameMode'];
        if (!in_array($gameMode, $this->gameModeLimit)) {
            return false;
        }
        $this->gameMode = $gameMode;
        return true;
    }

    protected function record($index)
    {
        $heroID = $this->heroList[$index];
        $playerInfo = $this->heroPlayers[$heroID];
        $playerBase = $playerInfo->baseData()->where('week_number', $this->weekNumber)->find();
        $playerHeroes = $playerInfo->heroesData()->where('week_number', $this->weekNumber)->find();
        $this->setInc($playerBase);
        $this->setInc($playerHeroes);
    }

    protected function setInc($data)
    {
        if (!$data) {
            return false;
        }
        $field = $this->recordField;
        $fieldMap = $this->recordFieldMap;
        $data->$field++;
        $tmp = @json_decode($data->$fieldMap, true) ? : [];
        isset($tmp[$this->gameMode]) or $tmp[$this->gameMode] = 0;
        $tmp[$this->gameMode]++;
        $data->$fieldMap = json_encode($tmp, JSON_UNESCAPED_UNICODE);
        $data->save();
        return true;
    }

    protected function getPlayer($index)
    {
        $player = Player::get(['BattleNetId' => $this->content['Players'][$index]['BattleNetId']]);
    }

    protected function getMap()
    {
        $mapID = $this->mapsMapping[$this->content['Map']] ?? null;
        if (!$mapID) {
            return false;
        }
        $this->map = Maps::get(['id' => $mapID]);
        return true;
    }

    protected function getHeroList()
    {
        if (!isset($this->content['OrderedBans']) || !isset($this->content['OrderedPicks'])) {
            return false;
        }
        $heroList = [];
        $banHeroNames = $this->content['OrderedBans'];
        if (count($banHeroNames) !== 4) {
            return false;
        }
        $heroNames = $this->content['OrderedPicks'];
        array_splice($heroNames, 0, 0, [$banHeroNames[0], $banHeroNames[1]]);
        array_splice($heroNames, 7, 0, [$banHeroNames[2], $banHeroNames[3]]);
        foreach ($heroNames as $heroName) {
            $heroID = $this->heroesMapping[$heroName] ?? 0;
            $heroList[] = $heroID;
        }
        $this->heroList = $heroList;
        return true;
    }

    protected function getHeroPlayers()
    {
        foreach ($this->content['Players'] as $player) {
            $heroName = $player['Character'];
            $heroID = $this->heroesMapping[$heroName] ?? null;
            if (!$heroID) {
                return false;
            }
            $playerInfo = Player::get(['BattleNetId' => $player['BattleNetId']]);
            if (!$playerInfo) {
                return false;
            }
            $this->heroPlayers[$heroID] = $playerInfo;
        }
        return true;
    }

    protected function IsOnAdviceRanking($index)
    {
        $heroID = $this->heroList[$index];
        $choices = array_slice($this->heroList, 0, $index);
        $msg = [
            'action' => 'advice',
            'params' => [
                'choices'   =>  $choices,
                'map'       =>  $this->map->code,
                'debug'     =>  false,
                'ignore'    =>  true,
            ]
        ];
        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        if (!$client->connect('127.0.0.1', 9504, -1)) {
            return false;
        }
        $client->send(json_encode($msg));
        $data = $client->recv();
        $client->close();
        $data = @json_decode($data, true);
        if (!$data) {
            return false;
        }
        $tops = [];
        foreach (array_slice($data, 0, 9) as $each) {
            $tops[] = $each[0];
        }
        return in_array($heroID, $tops);
    }
}
