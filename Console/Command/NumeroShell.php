<?php
App::uses('AppShell', 'Console/Command');

/**
 * @property Numero $Numero
 */
class NumeroShell extends AppShell {

    public function update() {
        $uri = "https://www.abandonware-magazines.org/affiche_mag.php";

        $this->loadModel('Numero');

        $query = [
            'conditions' => [
                'Numero.cd' => false,
//                'Numero.nbr_pages IS NULL',
                'url_pages' => '[]', 'nbr_pages !=' => 0
            ]
        ];
        if (isset($this->args[0]))
            $query['conditions']['Numero.magazine_id'] = $this->args[0];
        $numeros = $this->Numero->find('all', $query);

        foreach ($numeros as $numero) {
            $this->out("{$numero['Magazine']['nom']} {$numero['Numero']['numero']} ({$numero['Numero']['date']}) [mag={$numero['Magazine']['id']}&num={$numero['Numero']['id']}]:");

            $dir = rawurlencode($numero['Magazine']['dir']) . '/' . rawurlencode($numero['Numero']['dir']) . '/';
            $urls = [];

            $params = [
                'mag' => $numero['Numero']['magazine_id'],
                'num' => $numero['Numero']['id'],
                'album' => 'oui'
            ];
            $config = ['ssl_verify_peer' => false];

            App::uses('HttpSocket', 'Network/Http');

            $http = new HttpSocket($config);
            $response = $http->get($uri, $params);

            if ($response->code == '200') {
                $dom = new DOMDocument();
                @$dom->loadHtml($response->body);

                $xpath = new DomXpath($dom);
                /** @var DOMElement $node */
                foreach ($xpath->query('//a[@title="Voir la page en grand"]') as $node) {
                    $href = urldecode($node->getAttribute('href'));

                    // Remove root :
                    $url = str_replace('http://download.abandonware.org/magazines/', '', $href);
                    $url = str_replace('https://download.abandonware.org/magazines/', '', $url);

                    // URL encode :
                    $splits = [];
                    foreach (explode('/', $url) as $split) $splits[] = rawurlencode($split);
                    $url = implode('/', $splits);

                    // Remove second root :
                    $url = str_replace($dir, '', $url);

                    $urls[] = $url;
                }
            }

            $data = [
                'id' => $numero['Numero']['id'],
                'url_pages' => json_encode($urls, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'nbr_pages' => count($urls),
                'album' => count($urls) > 0,
                'statut' => null
            ];
            if ($numero['Numero']['diviser'] == true)
                $data['nbr_pages'] = ($data['nbr_pages'] * 2) - 2;

            $this->Numero->clear();
            $this->Numero->save($data);

            $this->out("- {$data['nbr_pages']} page(s)");
        }
    }
}
