<?php

namespace App\Widgets;

use Zippy\Binding\PropertyBinding as Prop;
use Zippy\Html\DataList\ArrayDataSource;
use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\BookmarkableLink;
use Zippy\Html\Link\RedirectLink;
use App\Entity\Doc\Document;
use App\Helper as H;
use App\Application as App;
use App\System;

/**
 * Виджет для  просмотра  документов
 */
class DocView extends \Zippy\Html\PageFragment
{

    private $_doc;
    public $_reldocs = array();
    public $_statelist = array();
    public $_fileslist = array();
    public $_msglist = array();
    public $_entrylist = array();

    public function __construct($id) {
        parent::__construct($id);

        $this->add(new BookmarkableLink('print', ""));

     
        $this->add(new RedirectLink('word', ""));
        $this->add(new RedirectLink('excel', ""));
        $this->add(new RedirectLink('pdf', ""));
        $this->add(new RedirectLink('xml', ""));

        $this->add(new Label('preview'));

        $this->add(new DataView('reldocs', new ArrayDataSource(new Prop($this, '_reldocs')), $this, 'relDoclistOnRow'));

        $this->add(new DataView('dw_statelist', new ArrayDataSource(new Prop($this, '_statelist')), $this, 'stateListOnRow'));

        $this->add(new DataView('dw_entrylist', new ArrayDataSource(new Prop($this, '_entrylist')), $this, 'entryListOnRow'));

        $this->add(new Form('addrelform'))->onSubmit($this, 'OnReldocSubmit');
        $this->addrelform->add(new AutocompleteTextInput('addrel'))->onText($this, 'OnAddDoc');


        $this->add(new Form('addfileform'))->onSubmit($this, 'OnFileSubmit');
        $this->addfileform->add(new \Zippy\Html\Form\File('addfile'));
        $this->addfileform->add(new TextInput('adddescfile'));
        $this->add(new DataView('dw_files', new ArrayDataSource(new Prop($this, '_fileslist')), $this, 'fileListOnRow'));

        $this->add(new Form('addmsgform'))->onSubmit($this, 'OnMsgSubmit');
        $this->addmsgform->add(new TextArea('addmsg'));
        $this->add(new DataView('dw_msglist', new ArrayDataSource(new Prop($this, '_msglist')), $this, 'msgListOnRow'));
    }

    // Устанавливаем  документ  для  просмотра
    public function setDoc(\App\Entity\Doc\Document $doc) {
        $this->_doc = $doc;
        $doc = $this->_doc->cast();



        $html = $doc->generateReport();
        $this->preview->setText($html, true);
        // проверяем  поддержку  экспорта
        $exportlist = $doc->supportedExport();
        $this->word->setVisible(in_array(Document::EX_WORD, $exportlist));
        $this->excel->setVisible(in_array(Document::EX_EXCEL, $exportlist));
        $this->pdf->setVisible(in_array(Document::EX_EXCEL, $exportlist));
        $this->pdf->setVisible(in_array(Document::EX_PDF, $exportlist));
        $this->xml->setVisible(in_array(Document::EX_XML_GNAU, $exportlist));

        $reportpage = "App/Pages/ShowDoc";


        $this->print->pagename = $reportpage;
        $this->print->params = array('print', $doc->document_id);
        $this->word->pagename = $reportpage;
        $this->word->params = array('doc', $doc->document_id);
        $this->excel->pagename = $reportpage;
        $this->excel->params = array('xls', $doc->document_id);
        $this->pdf->pagename = $reportpage;
        $this->pdf->params = array('pdf', $doc->document_id);
        $this->xml->pagename = $reportpage;
        $this->xml->params = array('xml', $doc->document_id);

        //список связаных  документов
        $this->updateDocs();

        //статусы
        $this->_statelist = $this->_doc->getLogList();
        $this->dw_statelist->Reload();

        //проводки
        $this->_entrylist = \App\Entity\AccountEntry::find('document_id=' . $this->_doc->document_id);
        $this->dw_entrylist->Reload();

        //список приатаченных  файлов
        $this->updateFiles();
        $this->updateMessages();
    }

    // обновление  списка  связанных  документов
    private function updateDocs() {
        $this->_reldocs = $this->_doc->ConnectedDocList();
        $this->reldocs->Reload();
    }

    //вывод строки  связанного  документа
    public function relDoclistOnRow($row) {
        $item = $row->getDataItem();
        $row->add(new ClickLink('docitem'))->onClick($this, 'detailDocOnClick');
        $row->add(new ClickLink('deldoc'))->onClick($this, 'deleteDocOnClick');
        $row->docitem->setValue($item->meta_desc . ' ' . $item->document_number);
    }

    //удаление связанного  документа
    public function deleteDocOnClick($sender) {
        $doc = $sender->owner->getDataItem();
        $this->_doc->RemoveConnectedDoc($doc->document_id);
        $this->updateDocs();
    }

    //открыть связанный документ
    public function detailDocOnClick($sender) {
        //$id = $sender->owner->getDataItem()->document_id;
        //App::Redirect('\App\Pages\Register\DocList', $id);
        $this->setDoc($sender->owner->getDataItem());
    }

    //вывод строки  лога состояний
    public function stateListOnRow($row) {
        $item = $row->getDataItem();
        $row->add(new Label('statehost', $item->hostname));
        $row->add(new Label('statedate', date('Y.m.d H:i', $item->updatedon)));
        $row->add(new Label('stateuser', $item->username));
        $row->add(new Label('statename', Document::getStateName($item->state)));
    }

    //вывод строки  проводок
    public function entryListOnRow($row) {
        $item = $row->getDataItem();
        $row->add(new Label('dt', $item->acc_d));
        $row->add(new Label('ct', $item->acc_c));
        $row->add(new Label('entryamount', H::famt($item->amount)));
    }

    /**
     * добавление  связанного  документа
     *
     * @param mixed $sender
     */
    public function OnReldocSubmit($sender) {

        $id = $this->addrelform->addrel->getKey();

        if ($id > 0) {
            $this->_doc->AddConnectedDoc($id);
            $this->updateDocs();
            $this->addrelform->addrel->setText('');
        } else {
            
        }
    }

    // автолоад списка  документов
    public function OnAddDoc($sender) {
        $text = $sender->getValue();
        $answer = array();
        $conn = \ZDB\DB::getConnect();
        $sql = "select document_id,document_number from documents where document_number  like '%{$text}%' and document_id <> {$this->_doc->document_id} order  by document_id desc  limit 0,20";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $answer[$row['document_id']] = $row['document_number'];
        }
        return $answer;
    }

    /**
     * добавление прикрепленного файла
     *
     * @param mixed $sender
     */
    public function OnFileSubmit($sender) {

        $file = $this->addfileform->addfile->getFile();
        if ($file['size'] > 10000000) {
            $this->getOwnerPage()->setError("Файл больше 10М !");
            return;
        }

        H::addFile($file, $this->_doc->document_id, $this->addfileform->adddescfile->getText(), \App\Entity\Message::TYPE_DOC);
        $this->addfileform->adddescfile->setText('');
        $this->updateFiles();
    }

    // обновление  списка  прикрепленных файлов
    private function updateFiles() {
        $this->_fileslist = H::getFileList($this->_doc->document_id, \App\Entity\Message::TYPE_DOC);
        $this->dw_files->Reload();
    }

    //вывод строки  прикрепленного файла
    public function filelistOnRow($row) {
        $item = $row->getDataItem();

        $file = $row->add(new \Zippy\Html\Link\BookmarkableLink("filename", _BASEURL . 'index.php?p=App/Pages/LoadFile&arg=' . $item->file_id));
        $file->setValue($item->filename);
        $file->setAttribute('title', $item->description);

        $row->add(new ClickLink('delfile'))->onClick($this, 'deleteFileOnClick');
    }

    //удаление прикрепленного файла
    public function deleteFileOnClick($sender) {
        $file = $sender->owner->getDataItem();
        H::deleteFile($file->file_id);
        $this->updateFiles();
    }

    /**
     * добавление коментария
     *
     * @param mixed $sender
     */
    public function OnMsgSubmit($sender) {
        $msg = new \App\Entity\Message();
        $msg->message = $this->addmsgform->addmsg->getText();
        $msg->created = time();
        $msg->user_id = System::getUser()->user_id;
        $msg->item_id = $this->_doc->document_id;
        $msg->item_type = \App\Entity\Message::TYPE_DOC;
        if (strlen($msg->message) == 0)
            return;
        $msg->save();

        $this->addmsgform->addmsg->setText('');
        $this->updateMessages();
    }

    //список   комментариев
    private function updateMessages() {
        $this->_msglist = \App\Entity\Message::find('item_type =1 and item_id=' . $this->_doc->document_id);
        $this->dw_msglist->Reload();
    }

    //вывод строки  коментария
    public function msgListOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label("msgdata", $item->message));
        $row->add(new Label("msgdate", date("Y-m-d H:i", $item->created)));
        $row->add(new Label("msguser", $item->username));

        $row->add(new ClickLink('delmsg'))->onClick($this, 'deleteMsgOnClick');
    }

    //удаление коментария
    public function deleteMsgOnClick($sender) {
        $msg = $sender->owner->getDataItem();
        \App\Entity\Message::delete($msg->message_id);
        $this->updateMessages();
    }

}
