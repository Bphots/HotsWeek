<?php

namespace hotsweek\parser;

use app\hotsweek\model\Period;
use app\hotsweek\model\Player;
use hotsweek\parser\builder\BaseDataBuilder;
use hotsweek\parser\builder\HeroesDataBuilder;
use hotsweek\parser\builder\EnemiesDataBuilder;
use hotsweek\parser\builder\MatesDataBuilder;

const WEEK_MIN = 0;
// const WEEK_MIN = 2533;

class ParseBattleReportCore
{
    use \hotsweek\parser\mapping\Heroes;

    protected $gameModeLimit = [3, 4, 5, 6];
    protected $timestamp;
    protected $periodID;
    protected $weekNumber;
    protected $date;
    protected $contentBase;
    protected $contentPlayers;
    protected $players;

    public function __construct($content)
    {
        $this->timestamp = strtotime($content['Timestamp']);
        $this->periodID = Period::order('ReplayBuild desc')->value('id');
        $this->weekNumber = floor(($this->timestamp + 345600) / 604800) + 1;
        $this->thisWeekNumber = floor((time() + 345600) / 604800) + 1;
        $this->date = date("Y-m-d", $this->timestamp);
        $this->contentPlayers = $content['Players'];
        unset($content['Players']);
        $this->contentBase = $content;
        $this->buildParty($this->contentPlayers);
        $this->getPlayers();
    }

    public function build()
    {
        if ($this->timestamp === false || !$this->checkWeekNumber()) {
            return false;
        } elseif (!in_array($this->contentBase['GameMode'], $this->gameModeLimit)) {
            return false;
        }
        foreach ($this->players as $key => $player) {
            $personal = $this->contentPlayers[$key];
            $this->parseBaseData($player, $personal);
            $this->parseHeroesData($player, $personal);
            $this->parseEnemiesData($player, $personal);
            $this->parseMatesData($player, $personal);
        }
        return true;
    }

    protected function getPlayers()
    {
        $players = [];
        foreach ($this->contentPlayers as $key => $each) {
            $player = Player::get([
                'BattleNetId'       =>  $each['BattleNetId'],
                'BattleNetRegionId' =>  $each['BattleNetRegionId'],
            ]);
            if (!$player) {
                $player = new Player;
                $player->save([
                    'BattleNetId'       =>  $each['BattleNetId'],
                    'Name'              =>  $each['Name'],
                    'BattleTag'         =>  $each['BattleTag'],
                    'BattleNetRegionId' =>  $each['BattleNetRegionId'],
                ]);
            }
            $players[$key] = $player;
        }
        $this->players = $players;
    }

    protected function parseBaseData($player, $personal)
    {
        $baseData = $player->baseData()->where([
            'date' => $this->date,
        ])->find();
        if (!$baseData) {
            $player->baseData()->save([
                'date'          =>  $this->date,
                'period_id'     =>  $this->periodID,
                'week_number'   =>  $this->weekNumber,
            ]);
            $baseData = $player->baseData()->where([
                'date'          =>  $this->date,
            ])->find();
        }
        $builder = new BaseDataBuilder($baseData, $this->contentBase, $personal);
        $builder->build();
        unset($builder);
    }

    protected function parseHeroesData($player, $personal)
    {
        $heroID = $this->heroesMapping[$personal['Character']];
        $heroesData = $player->heroesData()->where([
            'hero_id'           =>  $heroID,
            'week_number'       =>  $this->weekNumber,
        ])->find();
        if (!$heroesData) {
            $player->heroesData()->save([
                'hero_id'       =>  $heroID,
                'period_id'     =>  $this->periodID,
                'week_number'   =>  $this->weekNumber,
            ]);
            $heroesData = $player->heroesData()->where([
                'hero_id'       =>  $heroID,
                'week_number'   =>  $this->weekNumber,
            ])->find();
        }
        $builder = new HeroesDataBuilder($heroesData, $this->contentBase, $personal);
        $builder->build();
        unset($builder);
    }

    protected function parseEnemiesData($player, $personal)
    {
        foreach ($this->contentPlayers as $key => $enemy) {
            if ($player->id <= $this->players[$key]->id) {
                continue;
            }
            if ($personal['Team'] == $enemy['Team']) {
                continue;
            }
            $enemiesData = $player->enemiesData()->where([
                'player2_id'        =>  $this->players[$key]->id,
                'week_number'       =>  $this->weekNumber,
            ])->find();
            if (!$enemiesData) {
                $player->enemiesData()->save([
                    'player2_id'    =>  $this->players[$key]->id,
                    'period_id'     =>  $this->periodID,
                    'week_number'   =>  $this->weekNumber,
                ]);
                $enemiesData = $player->enemiesData()->where([
                    'player2_id'    =>  $this->players[$key]->id,
                    'week_number'   =>  $this->weekNumber,
                ])->find();
            }
            $builder = new EnemiesDataBuilder($enemiesData, $this->contentBase, $personal, $enemy);
            $builder->build();
            unset($builder);
        }
    }

    protected function parseMatesData($player, $personal)
    {
        foreach ($this->contentPlayers as $key => $mate) {
            if ($player->id <= $this->players[$key]->id) {
                continue;
            }
            if ($personal['Team'] != $mate['Team']) {
                continue;
            }
            $matesData = $player->matesData()->where([
                'player2_id'        =>  $this->players[$key]->id,
                'week_number'       =>  $this->weekNumber,
            ])->find();
            if (!$matesData) {
                $player->matesData()->save([
                    'player2_id'    =>  $this->players[$key]->id,
                    'period_id'     =>  $this->periodID,
                    'week_number'   =>  $this->weekNumber,
                ]);
                $matesData = $player->matesData()->where([
                    'player2_id'    =>  $this->players[$key]->id,
                    'week_number'   =>  $this->weekNumber,
                ])->find();
            }
            $builder = new MatesDataBuilder($matesData, $this->contentBase, $personal, $mate);
            $builder->build();
            unset($builder);
        }
    }

    protected function buildParty($players)
    {
        $partys = [];
        foreach ($players as $key => $player)
        {
            $this->contentPlayers[$key]['party'] = 0;
            $value = $player['PartyValue'];
            if ($value) {
                $partys[$value][] = $key;
            }
        }
        foreach ($partys as $party) {
            $count = count($party);
            foreach ($party as $key) {
                $this->contentPlayers[$key]['party'] = $count;
            }
        }
    }

    protected function checkWeekNumber()
    {
        return $this->weekNumber >= WEEK_MIN;
    }
}
