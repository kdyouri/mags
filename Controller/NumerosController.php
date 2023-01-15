<?php

/**
 * @property Numero $Numero
 */
class NumerosController extends AppController {

    public function index($magazineId = null) {
        $this->disableCache();

        $query = [
            'conditions' => [
                'Numero.cd' => false,
                'Numero.album' => true
            ],
            'order' => [
                'Numero.hs' => 'asc',
                'Numero.numero' => 'desc'
            ]
        ];
        if (isset($magazineId)) {
            $query['conditions']['Numero.magazine_id'] = $magazineId;
            $numeros = $this->Numero->find('all', $query);
        } else {
            $this->Paginator->settings = $query;
            $numeros = $this->Paginator->paginate();
        }
        $this->set(compact('numeros'));
    }

    /**
     * @param integer $id
     * @throws Exception
     */
    public function view($id = null) {
        $this->Numero->id = $id;
        if (!$this->Numero->exists()) throw new NotFoundException();

        $this->set('numero', $this->Numero->read());

        $this->Numero->clear();
        $this->Numero->save([
            'id' => $id,
            'visite' => date('Y-m-d H:i:s')
        ]);
        return $this->render('magazine');
    }

    public function page($id = null, $num = 1) {
        $this->Numero->id = $id;
        if (!$this->Numero->exists()) throw new NotFoundException();

        $numero = $this->Numero->read();
        $this->set(compact('numero', 'num'));
        $this->layout = null;
    }

    /**
     * @throws Exception
     */
    public function check($id = null) {
        $this->Numero->id = $id;
        if (!$this->Numero->exists()) throw new NotFoundException();

        $numero = $this->Numero->read();

        App::uses('PageLoader', 'Lib');
        $url = PageLoader::getUrl(1, $numero);

        App::uses('HttpSocket', 'Network/Http');
        $http = new HttpSocket();
        $result = $http->get($url);

        $status = $result->code == '200';
        $this->Numero->clear();
        $this->Numero->save([
            'id' => $id,
            'statut' => (int)$status
        ]);

        $this->response->type('application/json');
        $this->response->body(json_encode(compact('status')));
        return $this->response;
    }

    /**
     * @param integer $id
     * @param integer $reached
     * @throws Exception
     */
    public function update_read_data($id = null, $reached = null) {
        $this->Numero->id = $id;
        if (!$this->Numero->exists()) throw new NotFoundException();

        if ($pageCount = $this->Numero->field('nbr_pages')) {
            $read = intval(100 * $reached / $pageCount);
            if ($read == 100) $reached = 1; // Ré-initialiser

            $this->Numero->save([
                'id' => $id,
                'derniere_page_atteinte' => $reached,
                'lu' => $read // %
            ]);
        }
        $this->autoRender = false;
    }
}