<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Helper as H;
use Zippy\WebApplication as App;

/**
 * Страница  ввода  товарного чека
 */
class RegisterReceipt extends \ZippyERP\ERP\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_basedocid = 0;
    private $_rowid = 0;

    public function __construct($docid = 0, $basedocid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());

        $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "store_type = " . Store::STORE_TYPE_RET . " or store_type = " . Store::STORE_TYPE_OPT, "store_type  ")))->onChange($this, 'OnChangeStore');
        $this->docform->store->selectFirst();
        $this->docform->add(new TextInput('kassa'));

        $this->docform->add(new CheckBox('return'));
        $this->docform->add(new CheckBox('paycard'));

        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->docform->add(new Label('totalnds'));
        $this->docform->add(new Label('total'));
        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));
        $this->editdetail->add(new DropDownChoice('edittovar'))->onChange($this, 'OnChangeItem');

        $this->editdetail->add(new Label('qtystock'));

        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимое  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->totalnds->setText(H::fm($this->_doc->headerdata['totalnds']));
            $this->docform->document_date->setDate($this->_doc->document_date);


            $this->docform->kassa->setText($this->_doc->headerdata['kassa']);
            $this->docform->return->setChecked($this->_doc->headerdata['return']);
            $this->docform->return->setChecked($this->_doc->headerdata['paycard']);

            $this->docform->store->setValue($this->_doc->headerdata['store']);

            foreach ($this->_doc->detaildata as $item) {
                $item = new Item($item);
                $this->_tovarlist[$item->item_id] = $item;
            }
        } else {
            $this->_doc = Document::create('RegisterReceipt');
            $this->docform->document_number->setText($this->_doc->nextNumber());
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_tovarlist')), $this, 'detailOnRow'))->Reload();
        $this->OnChangeStore($this->docform->store);
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('tovar', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', $item->quantity / 1000));
        $row->add(new Label('price', H::fm($item->price)));
        $row->add(new Label('amount', H::fm(($item->quantity / 1000) * $item->price)));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        $tovar = $sender->owner->getDataItem();
        // unset($this->_tovarlist[$tovar->tovar_id]);

        $this->_tovarlist = array_diff_key($this->_tovarlist, array($tovar->item_id => $this->_tovarlist[$tovar->item_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender) {
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->_rowid = 0;
    }

    public function editOnClick($sender) {
        $stock = $sender->getOwner()->getDataItem();
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editquantity->setText($stock->quantity / 1000);
        $this->editdetail->editprice->setText(H::fm($stock->price));


        $this->editdetail->edittovar->setValue($stock->stock_id);


        $this->editdetail->qtystock->setText(Stock::getQuantity($stock->stock_id, $this->docform->document_date->getDate()) / 1000 . ' ' . $stock->measure_name);

        $this->_rowid = $stock->stock_id;
    }

    public function saverowOnClick($sender) {
        $id = $this->editdetail->edittovar->getValue();
        if ($id == 0) {
            $this->setError("Не вибраний товар");
            return;
        }
        $stock = Stock::load($id);
        $stock->quantity = 1000 * $this->editdetail->editquantity->getText();
        $stock->partion = $stock->price;
        $stock->price = $this->editdetail->editprice->getText() * 100;

        unset($this->_tovarlist[$this->_rowid]);
        $this->_tovarlist[$stock->stock_id] = $stock;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edittovar->setValue(0);

        $this->editdetail->editquantity->setText("1");

        $this->editdetail->editprice->setText("");
        $this->editdetail->qtystock->setText("");
    }

    public function cancelrowOnClick($sender) {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
    }

    public function savedocOnClick($sender) {
        if ($this->checkForm() == false) {
            return;
        }

        $this->calcTotal();

        $this->_doc->headerdata = array(
            'store' => $this->docform->store->getValue(),
            'kassa' => $this->docform->kassa->getText(),
            'total' => $this->docform->total->getText() * 100,
            'return' => $this->docform->return->isChecked(),
            'paycard' => $this->docform->paycard->isChecked(),
            'totalnds' => $this->docform->totalnds->getText() * 100
        );
        $this->_doc->detaildata = array();
        foreach ($this->_tovarlist as $tovar) {
            $this->_doc->detaildata[] = $tovar->getData();
        }

        $this->_doc->amount = 100 * $this->docform->total->getText();
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = strtotime($this->docform->document_date->getText());
        $isEdited = $this->_doc->document_id > 0;


        $conn = \ZDB\DB::getConnect();
        $conn->BeginTrans();
        try {
            $this->_doc->save();
            if ($sender->id == 'execdoc') {
                $this->_doc->updateStatus(Document::STATE_EXECUTED);
            } else {
                $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
            }
            if ($this->_basedocid > 0) {
                $this->_doc->AddConnectedDoc($this->_basedocid);
                $this->_basedocid = 0;
            }
            $conn->CommitTrans();
            App::RedirectBack();
        } catch (\Exception $ee) {
            global $logger;
            $conn->RollbackTrans();
            $this->setError("Помилка запису документу. Деталізація в лог файлі  ");
    
            $logger->error($ee);
            return;
        }
    }

    /**
     * Расчет  итого
     *
     */
    private function calcTotal() {
        $total = 0;
        foreach ($this->_tovarlist as $tovar) {
            $total = $total + $tovar->price * ($tovar->quantity / 1000);
        }

        $nds = $total * H::nds(true);
        $this->docform->totalnds->setText(H::fm($nds));
        $this->docform->total->setText(H::fm($total));
    }

    /**
     * Валидация   формы
     *
     */
    private function checkForm() {

        if (count($this->_tovarlist) == 0) {
            $this->setError("Не введений ні один  товар");
        }
        return !$this->isError();
    }

    public function beforeRender() {
        parent::beforeRender();

        $this->calcTotal();
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

    public function OnChangeStore($sender) {
        //очистка  списка  товаров
        $this->_tovarlist = array();
        $this->docform->detail->Reload();
        $store_id = $this->docform->store->getValue();
        $this->editdetail->edittovar->setOptionList(Stock::findArrayEx("store_id={$store_id} and closed <> 1 ", 'itemname'));
    }

    public function OnChangeItem($sender) {
        $id = $sender->getValue();
        $stock = Stock::load($id);
        //$item = Item::load($stock->item_id);
        $this->editdetail->editprice->setText(H::fm($stock->price));

        $this->editdetail->qtystock->setText(Stock::getQuantity($id, $this->docform->document_date->getDate()) / 1000 . ' ' . $stock->measure_name);

        $this->updateAjax(array('editprice', 'qtystock'));
    }

}
