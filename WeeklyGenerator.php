<?php
namespace hotsweek;

use hotsweek\generator\Counter;
use app\hotsweek\model\Period;

class WeeklyGenerator
{
    protected $date;
    protected $weekNumber;
    protected $periodID;
    protected $savePath;

    public function __construct($weekNumber)
    {
        $this->weekNumber = $weekNumber;
        $this->savePath = ROOT_PATH . 'weeklyreport' . DS . $weekNumber . DS . '%u' . DS;
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

    public function countPersonal($playerID)
    {
        $path = sprintf($this->savePath, $playerID);
        $counter = new Counter;
        $counter->setWeek($this->weekNumber);
        $counter->setPlayer($playerID);
        $counter->countBaseData();
        $counter->countHeroesData();
        $counter->countEnemiesData();
        $counter->countMatesData();
        $counter->save($path);
    }
}
