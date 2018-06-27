<?php
namespace hotsweek\generator;

use hotsweek\generator\Counter;
use app\hotsweek\model\Period;

class Generator
{
    protected $date;
    protected $weekNumber;
    protected $periodID;
    protected $rootPath;
    protected $savePath;

    public function __construct($weekNumber)
    {
        $this->weekNumber = $weekNumber;
        $this->rootPath = ROOT_PATH . 'weeklyreport' . DS . $weekNumber . DS;
        $this->savePath = $this->rootPath . '%u' . DS;
    }
    
    public function countGlobal()
    {
        $path = sprintf($this->savePath, 0);
        $counter = new Counter;
        $counter->setWeek($this->weekNumber);
        $counter->countBaseData();
        $counter->countHeroesData();
        $counter->save($path);
    }

    public function buildRanking()
    {
        $except = [0];
        $path = sprintf($this->savePath, 0);
        $ranking = new RankingBuilder;
        $ranking->setPath($this->rootPath, $except);
        $ranking->rank();
        $ranking->save($path, 'ranking');
    }

    public function countPersonal($playerID)
    {
        $path = sprintf($this->savePath, $playerID);
        $counter = new Counter;
        $counter->setWeek($this->weekNumber);
        $counter->setPlayer($playerID);
        $counter->pushPlayerInfo();
        $counter->countBaseData();
        $counter->countHeroesData();
        $counter->countEnemiesData();
        $counter->countMatesData();
        $counter->save($path);
    }
}
