<html>
    <body>
        <table  class="ctable" width="500"  cellspacing="0" cellpadding="1"  >
            <tr >

                <td colspan="3" width="160">
                    Дата: <b>{{date}} </b>
                </td>
                <td width="340" align="right">
                    Счет:<b> {{bankaccount}}</b>
                </td>
            </tr>

            <tr >
                <td style="font-weight: bolder;font-size: larger;"  align="center" colspan="4" >
                    Банковская выписка  № {{document_number}}
                    <br><br> </td>
            </tr>


            <tr style="font-weight: bolder;">
                <td width="200" style="border-bottom: 1px solid #000000" >Тип</td>
                <td width="150" style="border-bottom: 1px solid #000000">Контрагент</td>
                <td width="50" style="border-bottom: 1px solid #000000">Сумма</td>
                <td style="border-bottom: 1px solid #000000">Примечание </td>
            </tr>
            {{#_detail}}
                <tr ><td>{{type}}</td><td>{{cust}}</td><td  align="right">{{amount}}</td><td>{{comment}} </td></tr>
                    {{/_detail}}
        </table>

        <br>
    </body>
</html>
