<html>
    <body>
        <table   class="ctable"  border="0" cellspacing="0" cellpadding="2">
            <tr><th width="30">&nbsp;</th><th  width="100" >&nbsp;</th><th  width="130" >&nbsp;</th><th  width="50">&nbsp;</th><th width="50">&nbsp;</th><th width="60">&nbsp;</th><th width="80">&nbsp;</th></tr>


            <tr >
                <td style="font-weight: bolder;font-size: larger;" align="center" colspan="7"  valign="middle" >
                    <br><br>Начисление зарплаты № {{document_number}}  от  {{date}}    <br><br>
                </td>
            </tr>
           <tr >
                <td  style="font-weight: bolder; ;" align  colspan="7"  valign="middle" >
                   За  {{sdate}}
                <br></td>
            </tr>

            <tr style="font-weight: bolder;"><th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="30">№</th><th colspan="2" width="230"  style="border-top:1px #000 solid;border-bottom:1px #000 solid;" >ФИО</th><th  style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="100">Начислено</th><th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="100">Удержано</th><th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="100">К оплате</th></tr>
                    {{#_detail}}
                <tr ><td align="right" >{{no}}&nbsp;</td><td  colspan="2">{{emp_name}}</td><td align="right">{{incamount}}</td><td align="right">{{decamount}}</td><td align="right">{{amount}}  </td></tr>
                    {{/_detail}}



        </table>
        <br> <br>
    </body>
</html>