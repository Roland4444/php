<?php

namespace ProjectTest\Service;

use Core\Repository\IRepository;
use Finance\Entity\BankAccount;
use Finance\Repositories\BankAccountRepository;
use Finance\Service\BankService;
use PHPUnit\Framework\TestCase;

class BankAccountServiceTest extends TestCase
{
    public function testGetEntity()
    {
        $mockRepository = $this->createMock(IRepository::class);
        $service = new BankService($mockRepository);
        $this->assertEquals(BankAccount::class, $service->getEntity());
    }

    public function testSave()
    {
        $mock = $this->createMock(BankAccountRepository::class);
        $service = new BankService($mock);

        $account = new BankAccount();
        $mock->expects($this->once())->method('save')->with($this->equalTo($account));
        $mock->expects($this->never())->method('clearDef');
        $service->save($account);
    }

    public function testSaveDef()
    {
        $mock = $this->createMock(BankAccountRepository::class);
        $service = new BankService($mock);

        $defAccount = new BankAccount();
        $defAccount->setDef(true);
        $mock->expects($this->once())->method('save');
        $mock->expects($this->once())->method('clearDef');
        $service->save($defAccount);
    }

    public function testFindCash()
    {
        $mock = $this->createMock(BankAccountRepository::class);
        $service = new BankService($mock);
        $mock->expects($this->once())->method('findOneBy')->with(['cash' => true])->willReturn(new BankAccount());
        $service->findCash();
    }

    public function testFindDefault()
    {
        $mock = $this->createMock(BankAccountRepository::class);
        $service = new BankService($mock);
        $mock->expects($this->once())->method('findOneBy')->with(['def' => 1]);
        $service->findDefault();
    }

    public function testFindByNumber()
    {
        $mock = $this->createMock(BankAccountRepository::class);
        $service = new BankService($mock);
        $number = '123';
        $mock->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => $number]);
        $service->findByNumber($number);
    }

    public function testFind()
    {
        $mock = $this->createMock(BankAccountRepository::class);
        $service = new BankService($mock);
        $id = '500';
        $mock->expects($this->once())
            ->method('find')
            ->with($this->equalTo($id));
        $service->find($id);
    }

    public function testFindAll()
    {
        $mock = $this->createMock(BankAccountRepository::class);
        $service = new BankService($mock);
        $withClosed = true;
        $mock->expects($this->once())
            ->method('findBy')
            ->with([], ['name' => 'asc'])
            ->willReturn([]);
        $service->findAll($withClosed);
    }
}
