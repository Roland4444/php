<?php

namespace Core\Entity;

class ExpenseDay
{

    private $date;
    private $expenses;

    public function __construct($date)
    {
        $this->date = $date;
        $this->expenses = [];
    }

    public function addExpense($expense)
    {
        $this->expenses[] = $expense;
    }

    public function getExpenses()
    {
        return $this->expenses;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getSum()
    {
        $sum = 0;
        foreach ($this->expenses as $expense) {
            $sum += $expense->getMoney();
        }
        return $sum;
    }
}
