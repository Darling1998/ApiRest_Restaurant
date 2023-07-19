<?php
class M_DetallePedidos extends \DB\SQL\Mapper{
    public function __construct()
    {
        parent::__construct(\Base::instance()->get('DB'),'detallepedido');
    }
}