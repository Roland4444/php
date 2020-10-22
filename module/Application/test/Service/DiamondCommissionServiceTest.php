<?php

namespace ApplicationTest\Service;

use Application\Service\DiamondCommissionService;
use Finance\Entity\OtherExpense;
use Finance\Service\BankService;
use PHPUnit\Framework\TestCase;
use Reference\Entity\Settings;
use Reference\Service\CategoryService;
use Reference\Service\SettingsService;

class DiamondCommissionServiceTest extends TestCase
{
    public function testDecreaseIfExpenseIsEmpty()
    {
        $commission = 50;

        $service = $this->createMock(\Finance\Service\OtherExpenseService::class);
        $service->expects($this->once())
            ->method('getDiamondExpense')->willReturn(null);

        $diamondCommissionService = new DiamondCommissionService(null, $service, null, null);
        $diamondCommissionService->decrease('2019-11-15', $commission);
    }

    public function testDecreaseLowerExpense()
    {
        $moneyVal = 120;
        $commissionVal = 50;

        $otherExpense = new OtherExpense();
        $otherExpense->setMoney($moneyVal);
        $commission = $commissionVal;

        $service = $this->createMock(\Finance\Service\OtherExpenseService::class);
        $service->method('getDiamondExpense')->willReturn($otherExpense);
        $service->method('save')->willReturn(null);

        $service->expects($this->once())
            ->method('save');

        $service->expects($this->once())
            ->method('getDiamondExpense');

        $diamondCommissionService = new DiamondCommissionService(null, $service, null, null);
        $diamondCommissionService->decrease('2019-11-15', $commission);

        $this->assertEquals($otherExpense->getMoney(), $moneyVal - $commissionVal);
    }

    public function testDecreaseRemovesExpense()
    {
        $otherExpense = new OtherExpense();
        $otherExpense->setMoney(50);
        $commission = 100;
        $service = $this->createMock(\Finance\Service\OtherExpenseService::class);
        $service->method('getDiamondExpense')->willReturn($otherExpense);
        $service->method('remove')->willReturn(null);

        $service->expects($this->once())->method('remove');
        $service->expects($this->once())->method('getDiamondExpense');

        $diamondCommissionService = new DiamondCommissionService(null, $service, null, null);
        $diamondCommissionService->decrease('2019-11-15', $commission);

        $this->assertLessThan(0, (int)$otherExpense->getMoney());
    }

    public function testIncrease()
    {
        $moneyVal = 120;
        $commissionVal = 50;

        $mockedService = $this->getMockBuilder(\Finance\Service\OtherExpenseService::class)
            ->setMethods(['getDiamondExpense', 'save'])
            ->getMock();
        $otherExpense = new OtherExpense();
        $otherExpense->setMoney($moneyVal);
        $commission = $commissionVal;
        $mockedService->method('getDiamondExpense')->willReturn($otherExpense);
        $mockedService->method('save')->willReturn(null);

        $diamondCommissionService = new DiamondCommissionService(null, $mockedService, null, null);

        $mockedService->expects($this->once())->method('save');
        $mockedService->expects($this->once())->method('getDiamondExpense');

        $diamondCommissionService->increase('2019-11-15', $commission);

        $this->assertEquals($otherExpense->getMoney(), $moneyVal + $commissionVal);
    }

    public function testCreateDiamondOtherExpenseIfNotAvailableYet()
    {
        $mockedService = $this->createMock(\Finance\Service\OtherExpenseService::class);
        $expense = new OtherExpense();
        $expense->setDate('2019-11-15');
        $expense->setRealdate('2019-11-15');
        $expense->setComment('Комиссия рнко');
        $expense->setMoney(50);
        $mockedService->expects($this->once())->method('save')->with($expense);

        $categoryService = $this->createMock(CategoryService::class);
        $categoryService->expects($this->once())->method('getByAlias');

        $bankService = $this->createMock(BankService::class);
        $bankService->expects($this->once())->method('findByAlias');

        $commission = 50;

        $diamondCommissionService = new DiamondCommissionService(null, $mockedService, $categoryService, $bankService);
        $diamondCommissionService->increase('2019-11-15', $commission);
    }

    public function testCalculateCommissionWithEmptySettings()
    {
        $settingService = $this->createMock(SettingsService::class);
        $settingService->expects($this->once())
            ->method('findByAlias');

        $diamondCommissionService = new DiamondCommissionService($settingService, null, null, null);
        $result = $diamondCommissionService->calculateCommission(100);

        $this->assertEquals(0, $result);
    }

    public function testCalculateCommissionWithSettings()
    {
        $settings = new Settings();
        $settings->setValue("2,5");
        $settings->setAlias("diamond_commission");

        $settingService = $this->createMock(SettingsService::class);
        $settingService->expects($this->once())
            ->method('findByAlias')
            ->with(Settings::DIAMOND_COMMISSION)
            ->willReturn($settings);

        $diamondCommissionService = new DiamondCommissionService($settingService, null, null, null);
        $result = $diamondCommissionService->calculateCommission(12351);

        $this->assertEquals('308.78', $result);
    }

    public function testCalculateCommissionIfSettingValueIsEmpty()
    {
        $settings = new Settings();

        $settingService = $this->createMock(SettingsService::class);
        $settingService->method('findByAlias')
            ->with(Settings::DIAMOND_COMMISSION)
            ->willReturn($settings);

        $diamondCommissionService = new DiamondCommissionService($settingService, null, null, null);
        $result = $diamondCommissionService->calculateCommission(100);

        $this->assertEquals("0", $result);
    }

    public function testCalculateIfCommissionIsLessThanDiamondMinCommission()
    {
        $settings = new Settings();
        $settings->setValue("3.5");
        $settings->setAlias("diamond_commission");

        $settingService = $this->createMock(SettingsService::class);
        $settingService->method('findByAlias')
            ->with(Settings::DIAMOND_COMMISSION)
            ->willReturn($settings);

        $diamondCommissionService = new DiamondCommissionService($settingService, null, null, null);

        $result = $diamondCommissionService->calculateCommission(500);
        $this->assertEquals("40", $result);
    }
}
