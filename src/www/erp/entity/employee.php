<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  сотрудник
 * 
 * @table=erp_staff_employee
 * @view=erp_staff_employee_view
 * @keyfield=employee_id
 */
class Employee extends \ZCL\DB\Entity
{

    protected function afterLoad()
    {
        $this->hiredate = strtotime($this->hiredate);
        if (strlen($this->firedate) > 0)
            $this->firedate = strtotime($this->firedate);
        else
            $this->firedate = null;
    }

    //Возвращает  фамилию  и  имя
    public function getShortName()
    {
        return $this->lastname . " " . $this->firstname;
    }

    //Возвращает  фамилию  и  инициалы
    public function getInitName()
    {
        $name = $this->lastname . " " . mb_substr($this->firstname, 0, 1, "UTF-8") . '.';
        if (strlen($this->middlename) > 0) {
            $name = $name . " " . mb_substr($this->middlename, 0, 1, "UTF-8") . '.';
        }
        return $name;
    }

    public static function AddActivity($employee_id, $amount, $document_id)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("insert into erp_staff_employee_activity (employee_id,document_id,amount) values ({$employee_id},{$document_id},{$amount})");
    }

}
