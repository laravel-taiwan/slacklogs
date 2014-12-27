<?php 
class Helpers {

    public static function parseText($text)
    {
        preg_match_all('/<(.*?)>/', $text, $matches, PREG_SET_ORDER);

        if (! count($matches)) return $text;

        $fromText = [];
        $toText = [];

        foreach($matches as $match) {
            // User link case
            if (preg_match('/^@U/', $match[1])) {
                list($uid, ) = explode("|", $match[1]);

                $fromText[] = $match[0];
                $toText[] = '@' . User::where('sid', substr($uid, 1))->first()->name;

                continue;
            }

            if (preg_match('/^#C/', $match[1])) {
                list($cid, ) = explode("|", $match[1]);

                $fromText[] = $match[0];
                $toText[] = '#' . Channel::where('sid', substr($cid, 1))->first()->name;

                continue;
            }

            if (preg_match('/^http/i', $match[1])) {
                list($url, ) = explode("|", $match[1]);

                $fromText[] = $match[0];
                $toText[] = $url;

                continue;
            }
        }

        if (count($fromText)) {
            $text = str_replace($fromText, $toText, $text);
        }

        return $text;
    }
}