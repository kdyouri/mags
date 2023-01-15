<?php

/**
 * @property Numero $Numero
 */
class Magazine extends AppModel {

    public $displayField = 'nom';

    public $order = 'nom';

    public $hasMany = 'Numero';
}