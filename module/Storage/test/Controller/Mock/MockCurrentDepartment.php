<?php

namespace StorageTest\Controller\Mock;

use Reference\Entity\Department;

trait MockCurrentDepartment
{
    private ?Department $department;
    private bool $currentDepartmentIsset = false;

    public function setCurrentDepartment(?Department $department): void
    {
        $this->department = $department;
        $this->currentDepartmentIsset = true;
    }

    protected function currentDepartment()
    {
        if (! $this->currentDepartmentIsset) {
            $department = new Department();
            $department->setId(2);
            $this->department = $department;
        }
        return new class($this->department){
            private ?Department $department;

            public function __construct(?Department $department)
            {
                $this->department = $department;
            }

            public function getDepartment()
            {
                return $this->department;
            }

            public function getId()
            {
                return $this->department->getId();
            }

            public function isHide()
            {
                return $this->department->isHide();
            }
        };
    }
}
