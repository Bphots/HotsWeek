<?php
namespace hotsweek\behavior;

use hotsweek\parser\builder\BuilderMappings;
use app\hotsweek\model\Player;
use app\common\model\WinnerCount;
use app\common\model\Maps;

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
        $cacheName = 'anniversary:' . $this->map->code . ',' . json_encode($combo);
        $cache = cache($cacheName);
        dump($cacheName);
        dump($cache);
        if (!$cache) {
            return false;
        }
        cache($cacheName, null);
        foreach ($combo as $heroID) {
            if ($this->checkIfOnRanking($heroID, $cache)) {
                $playerIndex = $this->heroPlayers[$heroID];
                if ($this->content['Players']['IsWinner']) {
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
                'ignore' => true,
            ]
        ];
        $t1 = microtime(true);
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
        foreach (array_slice($data, 0, TOP) as $each) {
            $tops[] = $each[0];
        }
        return in_array($heroID, $tops);
    }
}
