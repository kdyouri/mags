<?php
class Numero extends AppModel {

    public $belongsTo = 'Magazine';

    public function afterFind($results, $primary = false)
    {
        foreach ($results as &$numero) {
            if (isset($numero[$this->alias]['url_pages'])) {
                $numero[$this->alias]['url_pages'] = json_decode($numero[$this->alias]['url_pages'], true);
            }
        }
        return $results;
    }

}