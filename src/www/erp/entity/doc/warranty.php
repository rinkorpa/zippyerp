<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ гарантийного талон
 * 
 */
class Warranty extends Document
{

    public function generateReport()
    {


        $i = 1;
        $detail = array();
        $total = 0;
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['itemname'],
                "quantity" => $value['quantity'],
                "price" => H::fm($value['price']),
                "amount" => H::fm($value['quantity'] * $value['price']),
                "sn" => $value['sn'],
                "warranty" => $value['warranty']
            );
            $total += $value['quantity'] * $value['price'] / 100;
        }

        $firm = \ZippyERP\System\System::getOptions("firmdetail");


        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['name'],
            "customer" => strlen($this->headerdata["customer"]) > 0 ? $this->headerdata["customer"] : '',
            "document_number" => $this->document_number
        );

        $report = new \ZippyERP\ERP\Report('warranty.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {

        return true;
    }

    public function nextNumber()
    {
        $doc = Document::getFirst("meta_name='Invoice'", "document_id ", 'desc');
        if ($doc == null)
            return '';
        $prevnumber = $doc->document_number;
        if (strlen($prevnumber) == 0)
            return '';
        $prevnumber = preg_replace('/[^0-9]/', '', $prevnumber);

        return "ГТ-" . sprintf("%05d", ++$prevnumber);
    }

}
