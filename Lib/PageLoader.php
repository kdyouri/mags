<?php
class PageLoader {

    const BASE_URL = 'http://download.abandonware.org/magazines';

    /**
     * @param integer $numpage
     * @param array $data
     * @return string
     */
    public static function getUrl($numpage, $data) {
        $base = static::BASE_URL;
        $dir = rawurlencode($data['Magazine']['dir']) . '/' . rawurlencode($data['Numero']['dir']);

        switch (true) {
            /*case $data['Numero']['livret']:
                $p1 = $numpage;
                $p2 = $data['Numero']['nbr_pages'] - ($p1 - 1);
                $isTheFirstSheet = ($numpage == 1 || $numpage == $data['Numero']['nbr_pages']);
                $isPair = !($numpage % 2);
                if ($isTheFirstSheet) $isPair = !$isPair;
                if ($isPair) {
                    $url = str_replace('{page1}', substr("00$p1", -$data['Numero']['pages_digits']), $url_pattern);
                    $url = str_replace('{page2}', substr("00$p2", -$data['Numero']['pages_digits']), $url);
                } else {
                    $url = str_replace('{page1}', substr("00$p2", -$data['Numero']['pages_digits']), $url_pattern);
                    $url = str_replace('{page2}', substr("00$p1", -$data['Numero']['pages_digits']), $url);
                }
                break;*/

            case $numpage > 1 && $data['Numero']['diviser']:
                $i = intval($numpage / 2);
                $url = $data['Numero']['url_pages'][$i];
                break;

            default:
                $url = $data['Numero']['url_pages'][$numpage - 1];
        }
        return "{$base}/{$dir}/{$url}";
    }
}