<?php
namespace hotsweek\behavior;

class HeroLeagueWinByAdivce 
{
    protected $content;

    public function __construct()
    {
        
    }

    public function run(&$content)
    {
        $this->content = $content;
        $this->checkGameMode();
        foreach ($content['Players'] as $player) {
            if (!$this->checkRegion($player)) {
                
            }
            // $this->get;
        }
        $this->content['Character'];
    }

    protected function checkGameMode()
    {
        return $this->content['GameMode'] === 4;
    }

    protected function checkRegion()
    {
        return $this->content['GameMode'] === 4;
    }
}