<?php

/**
 * @property Magazine $Magazine
 */
class MagazinesController extends AppController {

    public function index(){
        $this->response->disableCache();

        $magazines = $this->Magazine->Numero->find('all', [
            'fields' => ['Magazine.id', 'Magazine.nom', 'Magazine.logo', 'Magazine.dir', 'Magazine.genre', 'COUNT(*) AS count'],
            'conditions' => [
                'Numero.cd' => false,
                'Numero.album' => true,
            ],
            'group' => ['Magazine.id', 'Magazine.nom', 'Magazine.logo', 'Magazine.dir', 'Magazine.genre'],
            'order' => [
                'Magazine.genre' => 'asc',
                'Magazine.nom' => 'asc'
            ]
        ]);
        $parGenre = Hash::combine($magazines, '{n}.Magazine.id', '{n}', '{n}.Magazine.genre');
        $this->set(compact('parGenre'));

        $this->set('favories', $this->Magazine->Numero->find('all', [
            'conditions' => ['Numero.favorie' => 1],
            'order' => ['Numero.hs' => 'asc', 'Numero.numero' => 'desc']
        ]));
    }
}