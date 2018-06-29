<?php
namespace hotsweek\behavior;

class BpRecorder
{
    public function run(&$content)
    {
        $map = $content[0];
        $choice = $content[1];
        $banIndex = [0, 8, 1, 7];
        $bans = [];
        $combo = $choice;
        foreach ($banIndex as $index) {
            $bans[] = $combo[$index];
            unset($combo[$index]);
        }
        $combo = array_values($combo);
        sort($combo);
        $combo = array_merge($combo, $bans);
        $cacheName = 'anniversary:' . $map . ',' . json_encode($combo);
        cache($cacheName, $choice, 3600, 'anniversary');
        return true;
    }
}
