<?php
namespace hotsweek\behavior;

use hotsweek\parser\builder\BuilderMappings;
use app\hotsweek\model\Player;
use app\hotsweek\model\WinnerCount;
use app\common\model\Maps;

include_once __DIR__ . '/../parser/Constants.php';

class HeroLeagueWinByAdivce extends BuilderMappings
{
    protected $content;
    protected $map;
    protected $mapID;
    protected $heroPlayers = [];
    protected $heroIDs = [];

    public function run(&$content)
    {
        $this->content = $content;
        if (!$this->checkGameMode() || !$this->getMap() || !$this->getHeroIDs()) {
            return false;
        }
        $combo = array_values($this->heroIDs);
        sort($combo);
        $bans = $this->getBans();
        $combo = array_merge($combo, $bans);
        $cacheName = 'anniversary:' . $this->map->code . ',' . json_encode($combo);
        $cache = cache($cacheName);
        if (!$cache) {
            return false;
        }
        cache($cacheName, null);
        $banIndex = [0, 8, 1, 7];
        foreach ($cache as $key => $heroID) {
            if (in_array($key, $banIndex)) continue;
            if ($this->checkIfOnRanking($heroID, $cache)) {
                $playerIndex = $this->heroPlayers[$heroID];
                if ($this->content['Players'][$playerIndex]['IsWinner']) {
                    $player = $this->getPlayer($playerIndex);
                    // Record
                    $this->record($player);
                }
            }
        }
    }

    protected function checkGameMode()
    {
        return $this->content['GameMode'] === 4;
    }

    protected function checkRegion($player)
    {
        return $player['BattleNetRegionId'] === 5;
    }

    protected function record($player)
    {
        if (!$player) return false;
        $record = WinnerCount::get(['player_id', $player->id]);
        if (!$record) {
            $record = new WinnerCount;
            $record->save(['player_id' => $player->id]);
        }
        $record->win++;
        $record->save();
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

    protected function getHeroIDs()
    {
        foreach ($this->content['Players'] as $key => $player) {
            if (!$this->checkRegion($player)) {
                return false;
            }
            $name = $player['Character'];
            $heroID = $this->heroesMapping[$name] ?? null;
            if (!$heroID) {
                return false;
            }
            $this->heroPlayers[$heroID] = $key;
            $this->heroIDs[$key] = $heroID;
        }
        return true;
    }

    protected function checkIfOnRanking($heroID, $choices)
    {
        $length = array_search($heroID, $choices);
        $msg = [
            'action' => 'advice',
            'params' => [
                'choices' => array_slice($choices, 0, $length),
                'map' => $this->map->code,
                'debug' => false,
                'ignore' => true,
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

    protected function getBans()
    {
        $banData = $this->content['TeamHeroBans'];
        $codes = array_merge($banData[0], $banData[1]);
        $bans = [];
        foreach ($codes as $code) {
            $heroID = @constant(strtoupper("HERO_$code"));
            if ($heroID) {
                $bans[] = $heroID;
            }
        }
        return $bans;
    }
}
