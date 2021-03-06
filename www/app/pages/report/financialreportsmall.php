<?php

namespace App\Pages\Report;

use Carbon\Carbon;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\RedirectLink;
use Zippy\Html\Panel;
use Zippy\Html\Link\ClickLink;
use App\Entity\Account;
use App\Helper as H;
use App\System;

/**
 * финансовый отчет  малого  предприятия
 */
class FinancialReportSmall extends \App\Pages\Base
{

    public function __construct() {
        parent::__construct();

        if (\App\ACL::checkShowReport('FinancialReportSmall') == false) {
            $this->setWarn('Недостаточно  прав  для просмотра');
            return;
        }

        $this->add(new Form('filter'))->onSubmit($this, 'OnSubmit');
        $this->filter->add(new DropDownChoice('yr'));
        $this->filter->add(new DropDownChoice('qw'));
        $this->add(new ClickLink('autoclick'))->onClick($this, 'OnAutoLoad', true);

        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('xml', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender) {



        $this->detail->preview->setText("Загрузка...");

        $reportpage = "App/Pages/ShowReport";
        $reportname = "finreport25";

        \App\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
        \App\Session::getSession()->printxml = $this->exportGNAU($header);

        $this->detail->preview->setText($html, true);

        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', $reportname);
        $this->detail->html->pagename = $reportpage;
        $this->detail->html->params = array('html', $reportname);
        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', $reportname);
        $this->detail->xml->pagename = $reportpage;
        $this->detail->xml->params = array('xml', $reportname);

        $this->detail->setVisible(true);
    }

    private function getHeaderData() {


        //$detail = array();
        $totstartdt = 0;
        $totstartct = 0;
        $totobdt = 0;
        $totobct = 0;
        $totenddt = 0;
        $totendct = 0;

        $year = $this->filter->yr->getValue();
        $qw = $this->filter->qw->getValue();

        $date = new Carbon();
        $date->year($year)->startOfYear();
        $from = $date->timestamp;
        $date->addMonths($qw * 3);
        $to = $date->timestamp - 1;

        $firm = System::getOptions("firmdetail");

        $a10 = Account::load('10');
        $a11 = Account::load('11');
        $a12 = Account::load('12');
        $a13 = Account::load('13');
        $a15 = Account::load('15');
        $a20 = Account::load('20');
        $a22 = Account::load('22');
        $a23 = Account::load('23');
        $a26 = Account::load('26');
        $a28 = Account::load('28');
        $a30 = Account::load('30');
        $a31 = Account::load('31');
        $a36 = Account::load('36');
        $a37 = Account::load('37');
        $a40 = Account::load('40');
        $a641 = Account::load('641');
        $a642 = Account::load('642');
        $a643 = Account::load('643');
        $a644 = Account::load('644');
        $a63 = Account::load('63');
        $a66 = Account::load('66');
        $a68 = Account::load('68');
        $a70 = Account::load('70');
        $a701 = Account::load('701');
        $a702 = Account::load('702');
        $a703 = Account::load('703');
        //  $a704 = Account::load(704);
        $a71 = Account::load('71');

        $a79 = Account::load('79');
        $a90 = Account::load('90');
        $a91 = Account::load('91');
        $a92 = Account::load('92');
        $a93 = Account::load('93');
        $a94 = Account::load('94');
        $a97 = Account::load('97');


        //актив
        $b1011 = $a10->getSaldoD($from) + $a11->getSaldoD($from) + $a12->getSaldoD($from);
        $e1011 = $a10->getSaldoD($to) + $a11->getSaldoD($to) + $a12->getSaldoD($to);
        ;
        $b1012 = $a13->getSaldoD($from);
        $e1012 = $a13->getSaldoD($to);

        $b1010 = $b1011 - $b1012;
        $e1010 = $e1011 - $e1012;

        $b1005 = $a15->getSaldoD($from);
        $e1005 = $a15->getSaldoD($to);

        $b1095 = $b1005 + $b1010;
        $e1095 = $e1005 + $e1010;

        $b1100 = $a20->getSaldoD($from) + $a22->getSaldoD($from) + $a23->getSaldoD($from);
        $e1100 = $a20->getSaldoD($to) + $a22->getSaldoD($to) + $a23->getSaldoD($to);
        $b1103 = $a26->getSaldoD($from) + $a28->getSaldoD($from);
        $e1103 = $a26->getSaldoD($to) + $a28->getSaldoD($to);


        $b1100 = $b1100 + $b1103;
        $e1100 = $e1100 + $e1103;

        $b1125 = $a36->getSaldoD($from);
        $e1125 = $a36->getSaldoD($to);
        $b1135 = $a641->getSaldoD($from) + $a642->getSaldoD($from);
        $e1135 = $a641->getSaldoD($to) + $a642->getSaldoD($to);
        // $b1136 = SubConto::getAmount($from,641,0,0,0,0,0,666);
        // $e1136 = SubConto::getAmount($to,641,0,0,0,0,0,666);
        $b1155 = $a63->getSaldoD($from) + $a37->getSaldoD($from) + $a68->getSaldoD($from);
        $e1155 = $a63->getSaldoD($to) + $a37->getSaldoD($to) + $a68->getSaldoD($to);
        $b1165 = $a30->getSaldoD($from) + $a31->getSaldoD($from);
        $e1165 = $a30->getSaldoD($to) + $a31->getSaldoD($to);
        $b1190 = $a643->getSaldoD($from) + $a644->getSaldoD($from);
        $e1190 = $a643->getSaldoD($to) + $a644->getSaldoD($to);

        $b1195 = $b1100 + $b1125 + $b1135 + $b1155 + $b1165 + $b1190;
        $e1195 = $e1100 + $e1125 + $e1135 + $e1155 + $e1165 + $e1190;

        $b1300 = $b1095 + $b1195;
        $e1300 = $e1095 + $e1195;

        //пассив

        $b1400 = $a40->getSaldoC($from);
        $e1400 = $a40->getSaldoC($to);

        $b1420 = $a79->getSaldoC($from);
        $e1420 = $a79->getSaldoC($to);

        $b1495 = $b1420;
        $e1495 = $e1420;

        $b1420 = $b1420 > 0 ? $b1420 : "({$b1420})";
        $e1420 = $e1420 > 0 ? $e1420 : "({$e1420})";

        $b1615 = $a63->getSaldoC($from);
        $e1615 = $a63->getSaldoC($to);
        $b1620 = $a641->getSaldoC($from);
        $e1620 = $a641->getSaldoC($to);
        // $b1621 = SubConto::getAmount($from,641,0,0,0,0,0,666);
        // $e1621 = SubConto::getAmount($to,641,0,0,0,0,0,666);

        $b1630 = $a66->getSaldoC($from);
        $e1630 = $a66->getSaldoC($to);
        $b1690 = $a36->getSaldoC($from) + $a37->getSaldoC($from) + $a643->getSaldoC($from) + $a644->getSaldoC($from) + $a68->getSaldoC($from);
        $e1690 = $a36->getSaldoC($to) + $a37->getSaldoC($to) + $a643->getSaldoC($to) + $a644->getSaldoC($to) + $a68->getSaldoC($to);

        $b1695 = $b1615 + $b1620 + $b1630 + $b1690;
        $e1695 = $e1615 + $e1620 + $e1630 + $e1690;

        $b1900 = $b1400 + $b1495 + $b1695;
        $e1900 = $e1400 + $e1495 + $e1695;


        //форма 2
        $_from = date('- 1 year', $from);
        $_to = date('- 1 year', $to);


        $ob701 = $a701->getSaldoAndOb($from, $to);
        $ob702 = $a702->getSaldoAndOb($from, $to);
        $ob703 = $a703->getSaldoAndOb($from, $to);
        $b2000 = $ob701['obct'] + $ob702['obct'] + $ob703['obct'];
        $b2000 -= Account::getObBetweenAccount(70, 30, $from, $to);
        $b2000 -= Account::getObBetweenAccount(70, 31, $from, $to);
        $b2000 -= Account::getObBetweenAccount(70, 36, $from, $to);
        $b2000 -= Account::getObBetweenAccount(70, 641, $from, $to);
        $b2000 -= Account::getObBetweenAccount(70, 642, $from, $to);
        $b2000 -= Account::getObBetweenAccount(70, 643, $from, $to);

        $ob71 = $a71->getSaldoAndOb($from, $to);
        $b2120 = $ob71['obct'];
        $b2120 -= Account::getObBetweenAccount(71, 641, $from, $to);
        $b2120 -= Account::getObBetweenAccount(71, 643, $from, $to);

        //$ob72 = $a72->getSaldoAndOb($from,$to);
        //$b2240   = $ob72['obct'];  73 74
        $b2240 = 0;
        $b2280 = $b2000 + $b2120 + $b2240;

        $ob90 = $a90->getSaldoAndOb($from, $to);
        $ob91 = $a91->getSaldoAndOb($from, $to);
        $ob92 = $a92->getSaldoAndOb($from, $to);
        $ob93 = $a93->getSaldoAndOb($from, $to);
        $ob94 = $a94->getSaldoAndOb($from, $to);
        $ob97 = $a97->getSaldoAndOb($from, $to);
        // $ob98 = $a98->getSaldoAndOb($from,$to);
        $b2050 = $ob90['obdt'];
        $b2180 = $ob92['obdt'] + $ob93['obdt'] + $ob94['obdt'];

        $b2270 = $ob97['obdt'];
        $b2285 = $b2050 + $b2180 + $b2270;
        $b2290 = $b2280 - $b2285;
        //$b2300 =  $ob98['obdt']   ;
        $b2350 = $b2290 - $b2300;

        $ob701 = $a701->getSaldoAndOb($_from, $_to);
        $ob702 = $a702->getSaldoAndOb($_from, $_to);
        $ob703 = $a703->getSaldoAndOb($_from, $_to);
        $e2000 = $ob701['obct'] + $ob702['obct'] + $ob703['obct'];
        $e2000 -= Account::getObBetweenAccount(70, 30, $_from, $_to);
        $e2000 -= Account::getObBetweenAccount(70, 31, $_from, $_to);
        $e2000 -= Account::getObBetweenAccount(70, 36, $_from, $_to);
        $e2000 -= Account::getObBetweenAccount(70, 641, $_from, $_to);
        $e2000 -= Account::getObBetweenAccount(70, 642, $_from, $_to);
        $e2000 -= Account::getObBetweenAccount(70, 643, $_from, $_to);

        $ob71 = $a71->getSaldoAndOb($_from, $_to);
        $e2120 = $ob71['obct'];
        $e2120 -= Account::getObBetweenAccount(71, 641, $_from, $_to);
        $e2120 -= Account::getObBetweenAccount(71, 643, $_from, $_to);

        //$ob72 = $a72->getSaldoAndOb($_from,$_to);
        //$e2240   = $ob72['obct'];
        $e2240 = 0;
        $e2280 = $e2000 + $e2120 + $e2240;

        $ob90 = $a90->getSaldoAndOb($_from, $_to);
        $ob91 = $a91->getSaldoAndOb($_from, $_to);
        $ob92 = $a92->getSaldoAndOb($_from, $_to);
        $ob93 = $a93->getSaldoAndOb($_from, $_to);
        $ob94 = $a94->getSaldoAndOb($_from, $_to);
        $ob97 = $a97->getSaldoAndOb($_from, $_to);
        // $ob98 = $a98->getSaldoAndOb($_from,$_to);
        $e2050 = $ob90['obdt'];
        $e2180 = $ob92['obdt'] + $ob93['obdt'] + $ob94['obdt'];

        $e2270 = $ob97['obdt'];
        $e2285 = $e2050 + $e2180 + $b2270;
        $e2290 = $e2280 - $e2285;
        //$e2300 =  $ob98['obdt']   ;
        $e2350 = $e2290 - $e2300;


        $header = array(
            'date1y' => date('Y', time()),
            'date1m' => date('m', time()),
            'date1d' => date('d', time()),
            'date2' => date('d.m.Y', $to + 1),
            'edrpou' => (string) sprintf("%10d", $firm['edrpou']),
            'koatuu' => (string) sprintf("%10d", $firm['koatuu']),
            'kopfg' => (string) sprintf("%10d", $firm['kopfg']),
            'kodu' => (string) sprintf("%10d", $firm['kodu']),
            'kved' => (string) sprintf("%10s", $firm['kved']),
            'address' => $firm['address'] . ' ' . $firm['city'] . ', ' . $firm['phone'],
            'firmname' => $firm['firmname'],
            'b1005' => H::fm_t1($b1005),
            'e1005' => H::fm_t1($e1005),
            'b1010' => H::fm_t1($b1010),
            'e1010' => H::fm_t1($e1010),
            'b1011' => H::fm_t1($b1011),
            'e1011' => H::fm_t1($e1011),
            'b1012' => H::fm_t1($b1012),
            'e1012' => H::fm_t1($e1012),
            'b1095' => H::fm_t1($b1095),
            'e1095' => H::fm_t1($e1095),
            'b1100' => H::fm_t1($b1100),
            'e1100' => H::fm_t1($e1100),
            'b1103' => H::fm_t1($b1103),
            'e1103' => H::fm_t1($e1103),
            'b1125' => H::fm_t1($b1125),
            'e1125' => H::fm_t1($e1125),
            'b1135' => H::fm_t1($b1135),
            'e1135' => H::fm_t1($e1135),
            'b1136' => H::fm_t1($b1136),
            'e1136' => H::fm_t1($e1136),
            'b1155' => H::fm_t1($b1155),
            'e1155' => H::fm_t1($e1155),
            'b1165' => H::fm_t1($b1165),
            'e1165' => H::fm_t1($e1165),
            'b1190' => H::fm_t1($b1190),
            'e1190' => H::fm_t1($e1190),
            'b1195' => H::fm_t1($b1195),
            'e1195' => H::fm_t1($e1195),
            'b1300' => H::fm_t1($b1300),
            'e1300' => H::fm_t1($e1300),
            'b1400' => H::fm_t1($b1400),
            'e1400' => H::fm_t1($e1400),
            'b1420' => H::fm_t1($b1420),
            'e1420' => H::fm_t1($e1420),
            'b1495' => H::fm_t1($b1495),
            'e1495' => H::fm_t1($e1495),
            'b1615' => H::fm_t1($b1615),
            'e1615' => H::fm_t1($e1615),
            'b1620' => H::fm_t1($b1620),
            'e1620' => H::fm_t1($e1620),
            'b1621' => H::fm_t1($b1621),
            'e1621' => H::fm_t1($e1621),
            'b1630' => H::fm_t1($b1630),
            'e1630' => H::fm_t1($e1630),
            'b1690' => H::fm_t1($b1690),
            'e1690' => H::fm_t1($e1690),
            'b1695' => H::fm_t1($b1695),
            'e1695' => H::fm_t1($e1695),
            'b1900' => H::fm_t1($b1900),
            'e1900' => H::fm_t1($e1900),
            'b2000' => H::fm_t1($b2000),
            'e2000' => H::fm_t1($e2000),
            'b2120' => H::fm_t1($b2120),
            'e2120' => H::fm_t1($e2120),
            'b2240' => H::fm_t1($b2240),
            'e2240' => H::fm_t1($e2240),
            'b2280' => H::fm_t1($b2280),
            'e2280' => H::fm_t1($e2280),
            'b2050' => H::fm_t1($b2050),
            'e2050' => H::fm_t1($e2050),
            'b2180' => H::fm_t1($b2180),
            'e2180' => H::fm_t1($e2180),
            'b2270' => H::fm_t1($b2270),
            'e2270' => H::fm_t1($e2270),
            'b2285' => H::fm_t1($b2285),
            'e2285' => H::fm_t1($e2285),
            'b2290' => H::fm_t1($b2290),
            'e2290' => H::fm_t1($e2290),
            'b2300' => H::fm_t1($b2300),
            'e2300' => H::fm_t1($e2300),
            'b2350' => H::fm_t1($b2350),
            'e2350' => H::fm_t1($e2350)
        );


        return $header;
    }

    public function generateReport($header) {


        $report = new \App\Report('financialreportsmall.tpl');


        $html = $report->generate($header);

        return $html;
    }

    public function exportGNAU($header) {
        $year = $this->filter->yr->getValue();
        $pm = (string) sprintf('%02d', 3 * $this->filter->qw->getValue());
        $common = System::getOptions("common");
        $firm = System::getOptions("firmdetail");
        $jf = ($common['juridical'] == true ? "J" : "F") . "0901106";

        $edrpou = (string) sprintf("%10d", $firm['edrpou']);
        //2301 0011111111 J0901106 1 00 0000045 1 03 2015 2301.xml
        //1 - місяць, 2 - квартал, 3 - півріччя, 4 - 9 місяців, 5 - рік

        $number = (string) sprintf('%07d', 1);
        $filename = $firm['gni'] . $edrpou . "J0901106" . "100{$number}2" . $pm . $year . $firm['gni'] . ".xml";

        $filename = str_replace(' ', '0', $filename);

        $xml = "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>
  <DECLAR xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"J0901106.xsd\">
  <DECLARHEAD>
  <TIN>{$firm['edrpou']}</TIN>
  <C_DOC>J09</C_DOC>
  <C_DOC_SUB>011</C_DOC_SUB>
  <C_DOC_VER>6</C_DOC_VER>
  <C_DOC_TYPE>0</C_DOC_TYPE>
  <C_DOC_CNT>1</C_DOC_CNT>
  <C_REG>" . substr($firm['gni'], 0, 2) . "</C_REG>
  <C_RAJ>" . substr($firm['gni'], 2, 2) . "</C_RAJ>
  <PERIOD_MONTH>{$pm}</PERIOD_MONTH>
  <PERIOD_TYPE>2</PERIOD_TYPE>
  <PERIOD_YEAR>{$year}</PERIOD_YEAR>
  <C_STI_ORIG>{$firm['gni']}</C_STI_ORIG>
  <C_DOC_STAN>1</C_DOC_STAN>
  <LINKED_DOCS xsi:nil=\"true\" />
  <D_FILL>" . (string) date('dmY') . "</D_FILL>
  <SOFTWARE>Zippy ERP</SOFTWARE>
  </DECLARHEAD>
  <DECLARBODY>
  <HFILL>" . (string) date('dmY') . "</HFILL>
  <HNAME>{$firm['name']}<</HNAME>
  <HTIN>{$firm['edrpou']}</HTIN>
  <HKOATUU_S xsi:nil=\"true\" />
  <HKOATUU  >{$firm['koatuu']}</HKOATUU  >
  <HKOPFG_S xsi:nil=\"true\" />
  <HKOPFG  >{$firm['kopfg']}</HKOPFG  >
  <HKVED_S xsi:nil=\"true\" />
  <HKVED  >{$firm['kved']}</HKVED  >
  <HKIL xsi:nil=\"true\" />
  <HLOC xsi:nil=\"true\" />
  <HTEL xsi:nil=\"true\" />
  <HPERIOD1 />
  <HZY>{$year}</HZY>
  <R1005G3>{$header['b1005']}</R1005G3>
  <R1005G4>{$header['e1005']}</R1005G4>
  <R1010G3>{$header['b1010']}</R1010G3>
  <R1010G4>{$header['e1010']}</R1010G4>
  <R1011G3>{$header['b1011']}</R1011G3>
  <R1011G4>{$header['e1011']}</R1011G4>
  <R1012G3>{$header['b1012']}</R1012G3>
  <R1012G4>{$header['e1012']}</R1012G4>
  <R1095G3>{$header['b1095']}</R1095G3>
  <R1095G4>{$header['e1095']}</R1095G4>
  <R1100G3>{$header['b1100']}</R1100G3>
  <R1100G4>{$header['e1100']}</R1100G4>
  <R1103G3>{$header['b1103']}</R1103G3>
  <R1103G4>{$header['e1103']}</R1103G4>
  <R1125G3>{$header['b1125']}</R1125G3>
  <R1125G4>{$header['e1125']}</R1125G4>
  <R1135G3>{$header['b1135']}</R1135G3>
  <R1135G4>{$header['e1135']}</R1135G4>
  <R1136G3>{$header['b1126']}</R1136G3>
  <R1136G4>{$header['e1126']}</R1136G4>
  <R1155G3>{$header['b1155']}</R1155G3>
  <R1155G4>{$header['e1155']}</R1155G4>
  <R1165G3>{$header['b1165']}</R1165G3>
  <R1165G4>{$header['e1165']}</R1165G4>
  <R1190G3>{$header['b1190']}</R1190G3>
  <R1190G4>{$header['e1190']}</R1190G4>
  <R1195G3>{$header['b1195']}</R1195G3>
  <R1195G4>{$header['e1195']}</R1195G4>
  <R1300G3>{$header['b1300']}</R1300G3>
  <R1300G4>{$header['e1300']}</R1300G4>
  <R1400G3>{$header['b1400']}</R1400G3>
  <R1400G4>{$header['e1400']}</R1400G4>
  <R1420G3>{$header['b1420']}</R1420G3>
  <R1420G4>{$header['e1420']}</R1420G4>
  <R1495G3>{$header['b1495']}</R1495G3>
  <R1495G4>{$header['e1495']}</R1495G4>
  <R1615G3>{$header['b1615']}</R1615G3>
  <R1615G4>{$header['e1615']}</R1615G4>
  <R1620G3>{$header['b1620']}</R1620G3>
  <R1620G4>{$header['e1620']}</R1620G4>
  <R1621G3>{$header['b1621']}</R1621G3>
  <R1621G4>{$header['e1621']}</R1621G4>
  <R1630G3>{$header['b1630']}</R1630G3>
  <R1630G4>{$header['e1630']}</R1630G4>
  <R1690G3>{$header['b1690']}</R1690G3>
  <R1690G4>{$header['e1690']}</R1690G4>
  <R1695G3>{$header['b1695']}</R1695G3>
  <R1695G4>{$header['e1695']}</R1695G4>
  <R1900G3>{$header['b1900']}</R1900G3>
  <R1900G4>{$header['e1900']}</R1900G4>
  <R2000G3>{$header['b2000']}</R2000G3>
  <R2000G4>{$header['e2000']}</R2000G4>
  <R2120G3>{$header['b2120']}</R2120G3>
  <R2120G4>{$header['e2120']}</R2120G4>
  <R2240G3>{$header['b2240']}</R2240G3>
  <R2240G4>{$header['e2240']}</R2240G4>
  <R2280G3>{$header['b2280']}</R2280G3>
  <R2280G4>{$header['e2280']}</R2280G4>
  <R2050G3>{$header['b2050']}</R2050G3>
  <R2050G4>{$header['e2050']}</R2050G4>
  <R2180G3>{$header['b2180']}</R2180G3>
  <R2180G4>{$header['e2180']}</R2180G4>
  <R2270G3>{$header['b2270']}</R2270G3>
  <R2270G4>{$header['e2270']}</R2270G4>
  <R2285G3>{$header['b2285']}</R2285G3>
  <R2285G4>{$header['e2285']}</R2285G4>
  <R2290G3>{$header['b2290']}</R2290G3>
  <R2290G4>{$header['e2290']}</R2290G4>
  <R2300G3>{$header['b2300']}</R2300G3>
  <R2300G4>{$header['e2300']}</R2300G4>
  <R2350G3>{$header['b2350']}</R2350G3>
  <R2350G4>{$header['e2350']}</R2350G4>







  <HBOS />
  <HBUH xsi:nil=\"true\" />
  </DECLARBODY>
  </DECLAR>";

        return $xml;
    }

    public function OnAutoLoad($sender) {
        $html = $this->generateReport($this->getHeaderData());
        \App\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
        $this->detail->preview->setText($html, true);
        $this->updateAjax(array('preview'));
    }

    protected function beforeRender() {
        parent::beforeRender();

        \Zippy\WebApplication::addJavaScript("\$('#autoclick').click()", true);
    }

}
