 
<table class="ctable" border="0" cellspacing="0" cellpadding="2">


    <tr>
        <td></td>
        <td>Поставщик</td>
        <td colspan="6">{{customer_name}}</td>
    </tr>

    <tr>
        <td style="font-weight: bolder;font-size: larger;" align="center" colspan="7" valign="middle">
            <br><br> Накладная № {{document_number}} от {{date}} <br><br><br>
        </td>
    </tr>

    <tr style="font-weight: bolder;">
        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="30">№</th>
        <th colspan="2"   style="border-top:1px #000 solid;border-bottom:1px #000 solid;">Наименование</th>
        <th colspan="2"   style="border-top:1px #000 solid;border-bottom:1px #000 solid;">Код</th>

        <th align="right" style="text-align: right;border-top:1px #000 solid;border-bottom:1px #000 solid;" width="50">Кол.</th>
        <th align="right" style="text-align: right;border-top:1px #000 solid;border-bottom:1px #000 solid;" width="60">Цена</th>
        {{#usends}}       <th align="right" style="text-align: right;border-top:1px #000 solid;border-bottom:1px #000 solid;" width="100">Цена с  НДС</th>{{/usends}}


        <th align="right" style="text-align: right;border-top:1px #000 solid;border-bottom:1px #000 solid;" width="80">Сумма</th>
    </tr>
    {{#_detail}}
    <tr>
        <td align="right">{{no}}</td>
        <td colspan="2">{{itemname}}</td>
        <td colspan="2">{{itemcode}}</td>

        <td align="right">{{quantity}}</td>
        <td align="right">{{price}}</td>
        {{#usends}}   <td align="right">{{pricends}}</td>  {{/usends}}
        <td align="right">{{amount}}</td>
    </tr>
    {{/_detail}}
    <tr style="font-weight: bolder;">
        <td style="border-top:1px #000 solid;" colspan="7" align="right">Итого:</td>
        {{#usends}}   <td style="border-top:1px #000 solid;" align="right"> </td>  {{/usends}}
        <td style="border-top:1px #000 solid;" align="right">{{total}}</td>
    </tr>
    {{#usends}}
    <tr style="font-weight: bolder;">
        <td style="border-top:1px #000 solid;" colspan="7" align="right">В т.ч. НДС:</td>
        <td align="right" style="border-top:1px #000 solid;"> </td>  
        <td style="border-top:1px #000 solid;" align="right">{{totalnds}}</td>
    </tr>  {{/usends}}


</table>

