<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ входящая налоговая  накладая 
 * 
 */
class TaxInvoiceIncome extends Document
{

    public function generateReport()
    {


        $i = 1;
        $detail = array();
        $total = 0;
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'],
                "price" => H::fm($value['price']),
                "amount" => H::fm($value['quantity'] * $value['price'])
            );
            $total += $value['quantity'] * $value['price'] / 100;
        }

        $firm = \ZippyERP\System\System::getOptions("firmdetail");
        $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['name'],
            "firmcode" => $firm['code'],
            "customername" => $customer->customer_name,
            "document_number" => $this->document_number,
            "nds" => H::fm($this->headerdata["nds"]),
            "total" => H::fm($total),
            "totalnds" => H::fm($total + $this->headerdata["nds"]),
            "summa" => Util::ucfirst(Util::money2str($total + $this->headerdata["nds"] / 100, '.', ''))
        );

        $report = new \ZippyERP\ERP\Report('taxinvoiceincome.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        
    }

    public function nextNumber()
    {
        $doc = Document::getFirst("meta_name='GoodsIssue'", "document_id desc");
        if ($doc == null)
            return '';
        $prevnumber = $doc->document_number;
        if (strlen($prevnumber) == 0)
            return '';
        $prevnumber = preg_replace('/[^0-9]/', '', $prevnumber);

        return "РН-" . sprintf("%05d", ++$prevnumber);
    }

}
