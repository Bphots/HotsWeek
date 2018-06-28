<?php
namespace hotsweek\behavior;

class BpRecorder
{
    public function run(&$content)
    {
        $map = $content[0];
        $choice = $content[1];
        $combo = array_values($choice);
        sort($combo);
        $cacheName = 'anniversary:' . $map . ',' . json_encode($combo);
        cache($cacheName, $choice, 3600, 'anniversary');
        return true;
    }
}
