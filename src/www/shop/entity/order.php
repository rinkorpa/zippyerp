<?php

namespace ZippyERP\Shop\Entity;

//класс-сущность  заказа
/**
 * @keyfield=order_id
 * @table=shop_orders
 * @view=shop_orders_view
 */
class Order extends \ZCL\DB\Entity
{

    protected function init() {
        $this->order_id = 0;
    }

    protected function afterLoad() {

        $this->created = strtotime($this->created);
        $this->closed = strlen($this->closed) > 0 ? strtotime($this->closed) : null;
    }

    protected function afterSave($update) {
        if ($update == false)
            return;
        $conn = \ZCL\DB\DB::getConnect();

        $plist = OrderDetail::find("order_id =" . $this->order_id);
        foreach ($plist as $p) {
            $conn->Execute("update shop_products set topsaled =0 where  group_id=" . $p->group_id);
            $sql = "SELECT product_id ,count(quantity) as qnt FROM `shop_orderdetails_view` WHERE product_id in( select product_id from shop_products where deleted <> 1 and group_id= " . $p->group_id . ") and (orderstatus = 1 or orderstatus =2) group by product_id order  by  count(quantity)  DESC   LIMIT 1";
            $row = $conn->GetRow($sql);
            if ($p->product_id == $row["product_id"]) {
                $pr = Product::load($p->product_id);
                $pr->topsaled = 1;
                $pr->save();
            }
        }
    }

}
