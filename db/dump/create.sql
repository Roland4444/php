-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Апр 16 2020 г., 14:53
-- Версия сервера: 8.0.19
-- Версия PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `avs`
--

-- --------------------------------------------------------

--
-- Структура таблицы `authlog`
--

CREATE TABLE `authlog` (
                           `id` bigint NOT NULL,
                           `login` varchar(45) NOT NULL,
                           `ip` varchar(45) NOT NULL,
                           `date` datetime NOT NULL,
                           `allow` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `bank_account`
--

CREATE TABLE `bank_account` (
                                `id` bigint NOT NULL,
                                `name` varchar(45) DEFAULT NULL,
                                `bank` varchar(45) DEFAULT NULL,
                                `cash` tinyint(1) NOT NULL,
                                `def` tinyint(1) NOT NULL,
                                `closed` tinyint(1) NOT NULL DEFAULT '0',
                                `diamond` tinyint(1) NOT NULL DEFAULT '0',
                                `alias` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `blacklist`
--

CREATE TABLE `blacklist` (
                             `id` bigint UNSIGNED NOT NULL,
                             `ip` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cash_transfer`
--

CREATE TABLE `cash_transfer` (
                                 `id` bigint NOT NULL,
                                 `source_id` bigint NOT NULL,
                                 `dest_id` bigint NOT NULL,
                                 `money` decimal(10,2) NOT NULL,
                                 `payment_number` varchar(10) DEFAULT NULL,
                                 `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `category_role_ref`
--

CREATE TABLE `category_role_ref` (
                                     `id` int NOT NULL,
                                     `category_id` bigint NOT NULL,
                                     `role_id` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `containers`
--

CREATE TABLE `containers` (
                              `id` bigint NOT NULL,
                              `name` varchar(45) NOT NULL,
                              `tariff_cost` decimal(10,2) DEFAULT NULL,
                              `shipment_id` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `container_extra_owner`
--

CREATE TABLE `container_extra_owner` (
                                         `id` int NOT NULL,
                                         `container_id` bigint NOT NULL,
                                         `owner_id` bigint NOT NULL,
                                         `owner_cost` decimal(10,2) NOT NULL,
                                         `date_formal` date NOT NULL,
                                         `is_paid` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `container_items`
--

CREATE TABLE `container_items` (
                                   `id` bigint NOT NULL,
                                   `container_id` bigint NOT NULL,
                                   `metal_id` bigint NOT NULL,
                                   `weight` decimal(15,2) NOT NULL,
                                   `real_weight` decimal(15,2) NOT NULL,
                                   `cost` decimal(15,2) DEFAULT NULL,
                                   `cost_dol` decimal(11,3) DEFAULT NULL,
                                   `comment` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cost_category`
--

CREATE TABLE `cost_category` (
                                 `id` bigint NOT NULL,
                                 `name` varchar(45) NOT NULL,
                                 `group_id` bigint NOT NULL,
                                 `alias` varchar(250) DEFAULT NULL,
                                 `options` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cost_category_group`
--

CREATE TABLE `cost_category_group` (
                                       `id` bigint NOT NULL,
                                       `name` varchar(45) NOT NULL,
                                       `sortOrder` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `customer`
--

CREATE TABLE `customer` (
                            `id` bigint NOT NULL,
                            `name` varchar(200) NOT NULL,
                            `def` tinyint(1) NOT NULL,
                            `legal` tinyint(1) NOT NULL DEFAULT '0',
                            `inspection_date` date DEFAULT NULL,
                            `options` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `customer_debt`
--

CREATE TABLE `customer_debt` (
                                 `id` int NOT NULL,
                                 `customer_id` bigint NOT NULL,
                                 `amount` float NOT NULL,
                                 `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `department`
--

CREATE TABLE `department` (
                              `id` bigint NOT NULL,
                              `type` enum('black','color') NOT NULL,
                              `name` varchar(45) NOT NULL,
                              `source_department` bigint DEFAULT NULL,
                              `alias` varchar(255) DEFAULT NULL,
                              `options` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `employees`
--

CREATE TABLE `employees` (
                             `id` int NOT NULL,
                             `name` varchar(50) NOT NULL,
                             `license` varchar(14) NOT NULL,
                             `options` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `expense_shablons`
--

CREATE TABLE `expense_shablons` (
                                    `id` bigint NOT NULL,
                                    `inn` bigint NOT NULL,
                                    `text` varchar(200) NOT NULL,
                                    `category_id` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `factoring_assignment_debt`
--

CREATE TABLE `factoring_assignment_debt` (
                                             `id` int NOT NULL,
                                             `date` date NOT NULL,
                                             `provider_id` int NOT NULL,
                                             `trader_id` bigint NOT NULL,
                                             `money` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `factoring_payments`
--

CREATE TABLE `factoring_payments` (
                                      `id` int NOT NULL,
                                      `date` date NOT NULL,
                                      `provider_id` int NOT NULL,
                                      `money` decimal(10,2) NOT NULL,
                                      `bank_id` bigint NOT NULL,
                                      `payment_number` int DEFAULT NULL,
                                      `trader_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `factoring_provider`
--

CREATE TABLE `factoring_provider` (
                                      `id` int NOT NULL,
                                      `title` varchar(250) NOT NULL,
                                      `inn` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `images`
--

CREATE TABLE `images` (
                          `id` int NOT NULL,
                          `entity` varchar(255) NOT NULL,
                          `entity_id` int NOT NULL,
                          `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `main_metal_expenses`
--

CREATE TABLE `main_metal_expenses` (
                                       `id` int NOT NULL,
                                       `customer_id` bigint NOT NULL,
                                       `bank_id` bigint NOT NULL,
                                       `date` date NOT NULL,
                                       `payment_number` int DEFAULT NULL,
                                       `money` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `main_other_expenses`
--

CREATE TABLE `main_other_expenses` (
                                       `id` bigint NOT NULL,
                                       `date` date NOT NULL,
                                       `realdate` date DEFAULT '2001-01-01',
                                       `recipient` varchar(255) DEFAULT NULL,
                                       `comment` varchar(255) NOT NULL,
                                       `money` decimal(10,2) NOT NULL,
                                       `bank_id` bigint NOT NULL,
                                       `category_id` bigint NOT NULL,
                                       `payment_number` int DEFAULT NULL,
                                       `inn` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `main_receipts`
--

CREATE TABLE `main_receipts` (
                                 `id` bigint NOT NULL,
                                 `date` date NOT NULL,
                                 `money` decimal(10,2) NOT NULL,
                                 `comment` varchar(255) DEFAULT NULL,
                                 `payment_number` int DEFAULT NULL,
                                 `bank_account_id` bigint NOT NULL,
                                 `inn` bigint NOT NULL,
                                 `sender` varchar(150) DEFAULT NULL,
                                 `options` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `main_receipts_trader`
--

CREATE TABLE `main_receipts_trader` (
                                        `id` bigint NOT NULL,
                                        `date` date NOT NULL,
                                        `money` decimal(10,2) NOT NULL,
                                        `payment_number` int DEFAULT NULL,
                                        `bank_account_id` bigint NOT NULL,
                                        `trader_id` bigint NOT NULL,
                                        `type` enum('black','color') NOT NULL DEFAULT 'black'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `metal`
--

CREATE TABLE `metal` (
                         `id` bigint NOT NULL,
                         `name` varchar(45) NOT NULL,
                         `def` tinyint(1) NOT NULL,
                         `group_id` bigint NOT NULL,
                         `code` varchar(255) NOT NULL,
                         `alias` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `metal_group`
--

CREATE TABLE `metal_group` (
                               `id` bigint NOT NULL,
                               `name` varchar(45) NOT NULL,
                               `ferrous` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `money_to_department`
--

CREATE TABLE `money_to_department` (
                                       `id` bigint NOT NULL,
                                       `date` date NOT NULL,
                                       `money` decimal(10,2) DEFAULT NULL,
                                       `department_id` bigint NOT NULL,
                                       `bank_id` bigint NOT NULL,
                                       `verified` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `move_of_vehicles`
--

CREATE TABLE `move_of_vehicles` (
                                    `id` bigint NOT NULL,
                                    `date` date NOT NULL,
                                    `customer` varchar(45) NOT NULL,
                                    `vehicle_id` bigint NOT NULL,
                                    `waybill` varchar(45) DEFAULT NULL,
                                    `payment` decimal(10,2) NOT NULL,
                                    `department_id` bigint NOT NULL,
                                    `comment` varchar(255) DEFAULT NULL,
                                    `completed` tinyint(1) DEFAULT NULL,
                                    `money_department_id` bigint NOT NULL,
                                    `departure_time` time DEFAULT NULL COMMENT 'Время выезда с базы',
                                    `arrival_time` time DEFAULT NULL COMMENT 'Время возвращения на базу',
                                    `driver_id` int NOT NULL DEFAULT '1',
                                    `departure` varchar(255) DEFAULT NULL COMMENT 'Адрес отправления',
                                    `arrival` varchar(255) DEFAULT NULL COMMENT 'Адрес назначения',
                                    `departure_from_point_time` time DEFAULT NULL COMMENT 'Время убытия из точку назначения',
                                    `arrival_at_point_time` time DEFAULT NULL COMMENT 'Время прибытия на точку назначения',
                                    `distance` int DEFAULT NULL COMMENT 'Количество километров, пройденное автомобилем',
                                    `remote_sklad_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `office_cash_transport_income`
--

CREATE TABLE `office_cash_transport_income` (
                                                `id` int NOT NULL,
                                                `date` date NOT NULL,
                                                `money` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `owner`
--

CREATE TABLE `owner` (
                         `id` bigint NOT NULL,
                         `name` varchar(45) NOT NULL,
                         `inn` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `phinxlog`
--

CREATE TABLE `phinxlog` (
                            `version` bigint NOT NULL,
                            `migration_name` varchar(100) DEFAULT NULL,
                            `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                            `breakpoint` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `purchase`
--

CREATE TABLE `purchase` (
                            `id` bigint NOT NULL,
                            `date` date NOT NULL,
                            `weight` decimal(10,2) NOT NULL,
                            `cost` decimal(10,3) NOT NULL,
                            `formal_cost` decimal(10,2) DEFAULT NULL,
                            `metal_id` bigint NOT NULL,
                            `department_id` bigint NOT NULL,
                            `customer_id` bigint NOT NULL,
                            `deal_id` int DEFAULT NULL,
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `purchase_deal`
--

CREATE TABLE `purchase_deal` (
                                 `id` int NOT NULL,
                                 `code` varchar(255) NOT NULL,
                                 `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `remote_sklad`
--

CREATE TABLE `remote_sklad` (
                                `id` bigint UNSIGNED NOT NULL,
                                `report_id` bigint NOT NULL,
                                `transport` varchar(50) NOT NULL,
                                `transnumb` varchar(50) NOT NULL,
                                `naklnumb` int DEFAULT NULL,
                                `date` date DEFAULT NULL,
                                `massa` varchar(50) NOT NULL,
                                `gruz` varchar(50) NOT NULL,
                                `sklad` varchar(50) NOT NULL,
                                `sor` varchar(10) NOT NULL,
                                `brute` int NOT NULL DEFAULT '0',
                                `primesi` varchar(10) NOT NULL,
                                `time` varchar(5) NOT NULL,
                                `cena` varchar(10) DEFAULT NULL,
                                `img` mediumblob,
                                `img2` mediumblob,
                                `massaFact` int DEFAULT NULL,
                                `destination` varchar(50) DEFAULT NULL,
                                `comment` varchar(255) DEFAULT NULL,
                                `finished` tinyint(1) DEFAULT NULL,
                                `transfer` int DEFAULT NULL,
                                `recplate` varchar(10) NOT NULL DEFAULT 'xxx',
                                `path` varchar(255) NOT NULL DEFAULT 'foto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `report_expense_limits`
--

CREATE TABLE `report_expense_limits` (
                                         `id` int NOT NULL,
                                         `data` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

CREATE TABLE `role` (
                        `id` bigint NOT NULL,
                        `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
                            `id` int NOT NULL,
                            `label` varchar(250) NOT NULL COMMENT 'Описание поля',
                            `alias` varchar(250) NOT NULL COMMENT 'Алиас',
                            `value` varchar(250) NOT NULL COMMENT 'Значение.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `shipment`
--

CREATE TABLE `shipment` (
                            `id` bigint NOT NULL,
                            `date` date NOT NULL,
                            `department_id` bigint NOT NULL,
                            `trader_id` bigint NOT NULL,
                            `tariff_id` bigint NOT NULL,
                            `dollar_rate` decimal(10,4) DEFAULT NULL,
                            `options` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spares`
--

CREATE TABLE `spares` (
                          `id` bigint UNSIGNED NOT NULL,
                          `name` varchar(100) NOT NULL,
                          `is_composite` int DEFAULT '0' COMMENT 'Определяет, что может делиться на штуки, литры итп',
                          `comment` varchar(20) DEFAULT NULL,
                          `units` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_consumption`
--

CREATE TABLE `spare_consumption` (
                                     `id` int NOT NULL,
                                     `date` date NOT NULL,
                                     `employee_id` int NOT NULL,
                                     `warehouse_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_consumption_items`
--

CREATE TABLE `spare_consumption_items` (
                                           `id` int NOT NULL,
                                           `spare_id` bigint UNSIGNED NOT NULL COMMENT 'id запчасти',
                                           `quantity` decimal(10,2) NOT NULL,
                                           `comment` varchar(250) NOT NULL COMMENT 'Комментарий',
                                           `vehicle_id` bigint DEFAULT NULL COMMENT 'id техники',
                                           `consumption_id` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_inventory`
--

CREATE TABLE `spare_inventory` (
                                   `id` int NOT NULL,
                                   `date` date NOT NULL COMMENT 'Дата проведения инвентаризации',
                                   `warehouse_id` int NOT NULL COMMENT 'id склада'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_inventory_items`
--

CREATE TABLE `spare_inventory_items` (
                                         `id` int NOT NULL,
                                         `inventory_id` int NOT NULL COMMENT 'id инвентаризации',
                                         `spare_id` bigint UNSIGNED NOT NULL COMMENT 'id запчасти',
                                         `quantity_formal` decimal(10,2) NOT NULL,
                                         `quantity_fact` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_order`
--

CREATE TABLE `spare_order` (
                               `id` int NOT NULL,
                               `document` varchar(200) NOT NULL COMMENT 'Индефикатор документа заказа',
                               `seller_id` int NOT NULL COMMENT 'Поставщик',
                               `date` date NOT NULL COMMENT 'Дата заказа',
                               `status_id` int NOT NULL,
                               `expected_date` date NOT NULL COMMENT 'Ожидаемая дата поступления',
                               `payment_status_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_order_cash_expense_ref`
--

CREATE TABLE `spare_order_cash_expense_ref` (
                                                `id` int NOT NULL,
                                                `expense_id` bigint NOT NULL,
                                                `order_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_order_expense_ref`
--

CREATE TABLE `spare_order_expense_ref` (
                                           `id` int NOT NULL,
                                           `expense_id` bigint NOT NULL,
                                           `order_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_order_items`
--

CREATE TABLE `spare_order_items` (
                                     `id` int NOT NULL,
                                     `order_id` int NOT NULL,
                                     `planning_items_id` int NOT NULL,
                                     `quantity` decimal(10,2) DEFAULT NULL,
                                     `sub_quantity` decimal(10,2) DEFAULT NULL,
                                     `price` decimal(10,4) DEFAULT NULL,
                                     `spare_id` bigint UNSIGNED NOT NULL COMMENT 'id запчасти'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_order_status`
--

CREATE TABLE `spare_order_status` (
                                      `id` int NOT NULL,
                                      `title` varchar(25) NOT NULL COMMENT 'Название статуса',
                                      `alias` varchar(25) NOT NULL COMMENT 'Алиас'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_order_status_payment`
--

CREATE TABLE `spare_order_status_payment` (
                                              `id` int NOT NULL,
                                              `title` varchar(25) NOT NULL COMMENT 'Название статуса',
                                              `alias` varchar(25) NOT NULL COMMENT 'Алиас'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_planning`
--

CREATE TABLE `spare_planning` (
                                  `id` int NOT NULL,
                                  `comment` varchar(250) DEFAULT NULL COMMENT 'Комментарий',
                                  `date` date NOT NULL,
                                  `status_id` int NOT NULL,
                                  `employee_id` int DEFAULT NULL,
                                  `vehicle_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_planning_items`
--

CREATE TABLE `spare_planning_items` (
                                        `id` int NOT NULL,
                                        `planning_id` int NOT NULL,
                                        `spare_id` bigint UNSIGNED NOT NULL COMMENT 'id запчасти',
                                        `quantity` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_planning_status`
--

CREATE TABLE `spare_planning_status` (
                                         `id` int NOT NULL,
                                         `title` varchar(25) NOT NULL COMMENT 'Название статуса',
                                         `alias` varchar(25) NOT NULL COMMENT 'Алиас'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_receipt`
--

CREATE TABLE `spare_receipt` (
                                 `id` int NOT NULL,
                                 `document` varchar(200) NOT NULL COMMENT 'Индефикатор документа прихода',
                                 `seller` varchar(255) NOT NULL,
                                 `date` date NOT NULL COMMENT 'Дата поступления',
                                 `warehouse_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_receipt_items`
--

CREATE TABLE `spare_receipt_items` (
                                       `id` int NOT NULL,
                                       `receipt_id` int NOT NULL,
                                       `order_item_id` int UNSIGNED NOT NULL,
                                       `quantity` decimal(10,2) NOT NULL,
                                       `sub_quantity` decimal(10,2) DEFAULT NULL,
                                       `spare_id` bigint UNSIGNED NOT NULL COMMENT 'id запчасти'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_seller`
--

CREATE TABLE `spare_seller` (
                                `id` int NOT NULL,
                                `name` varchar(200) NOT NULL,
                                `inn` varchar(20) NOT NULL,
                                `contacts` varchar(255) NOT NULL,
                                `comment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_seller_returns`
--

CREATE TABLE `spare_seller_returns` (
                                        `id` int NOT NULL,
                                        `date` date NOT NULL,
                                        `seller_id` int NOT NULL,
                                        `money` decimal(10,2) NOT NULL,
                                        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `spare_transfer`
--

CREATE TABLE `spare_transfer` (
                                  `id` int NOT NULL,
                                  `source_id` int NOT NULL,
                                  `dest_id` int NOT NULL,
                                  `spare_id` bigint UNSIGNED NOT NULL COMMENT 'id запчасти',
                                  `quantity` int NOT NULL COMMENT 'Количество товара',
                                  `sub_quantity` int DEFAULT NULL COMMENT 'Количество единиц (штуки, литры итп) в одном пришедшем товаре',
                                  `date` date NOT NULL COMMENT 'Дата перевода'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `storage_cash_transfer`
--

CREATE TABLE `storage_cash_transfer` (
                                         `id` bigint UNSIGNED NOT NULL,
                                         `date` date NOT NULL,
                                         `source_department_id` bigint NOT NULL,
                                         `dest_department_id` bigint NOT NULL,
                                         `money` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `storage_metal_expense`
--

CREATE TABLE `storage_metal_expense` (
                                         `id` bigint NOT NULL,
                                         `date` date NOT NULL,
                                         `customer_id` bigint NOT NULL,
                                         `money` decimal(10,2) NOT NULL,
                                         `department_id` bigint NOT NULL,
                                         `formal` tinyint(1) NOT NULL DEFAULT '0',
                                         `deal_id` int DEFAULT NULL,
                                         `diamond` tinyint(1) NOT NULL DEFAULT '0',
                                         `weighing_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `storage_other_expense`
--

CREATE TABLE `storage_other_expense` (
                                         `id` bigint NOT NULL,
                                         `date` date NOT NULL,
                                         `realdate` date DEFAULT NULL,
                                         `money` decimal(10,2) NOT NULL,
                                         `department_id` bigint NOT NULL,
                                         `category_id` bigint NOT NULL,
                                         `comment` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tariff_shipment`
--

CREATE TABLE `tariff_shipment` (
                                   `id` bigint NOT NULL,
                                   `name` varchar(45) NOT NULL,
                                   `destination` varchar(45) NOT NULL,
                                   `distance` int NOT NULL,
                                   `def` tinyint(1) DEFAULT NULL,
                                   `type` enum('black','color') NOT NULL,
                                   `money` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `trader`
--

CREATE TABLE `trader` (
                          `id` bigint NOT NULL,
                          `name` varchar(45) NOT NULL,
                          `def` tinyint(1) NOT NULL,
                          `inn` bigint NOT NULL,
                          `parent_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `trader_parent`
--

CREATE TABLE `trader_parent` (
                                 `id` bigint UNSIGNED NOT NULL,
                                 `name` varchar(100) NOT NULL,
                                 `ord` int NOT NULL DEFAULT '0',
                                 `alias` varchar(255) DEFAULT NULL,
                                 `options` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `transfer`
--

CREATE TABLE `transfer` (
                            `id` bigint NOT NULL,
                            `date` date NOT NULL,
                            `source_department_id` bigint NOT NULL,
                            `dest_department_id` bigint NOT NULL,
                            `metal_id` bigint NOT NULL,
                            `weight` decimal(10,2) NOT NULL,
                            `actual_weight` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `transport_incas`
--

CREATE TABLE `transport_incas` (
                                   `id` int NOT NULL,
                                   `date` date NOT NULL,
                                   `money` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
                        `id` bigint NOT NULL,
                        `name` varchar(45) NOT NULL,
                        `login` varchar(45) NOT NULL,
                        `password` varchar(45) NOT NULL,
                        `change_password` tinyint(1) DEFAULT NULL,
                        `is_blocked` tinyint(1) DEFAULT NULL,
                        `login_from_internet` tinyint(1) DEFAULT NULL,
                        `department_id` bigint DEFAULT NULL,
                        `attempts` smallint(1) UNSIGNED ZEROFILL DEFAULT '0' COMMENT 'Кол-во попыток логина',
                        `pass` varchar(60) DEFAULT NULL,
                        `token` varchar(255) DEFAULT NULL,
                        `token_expired` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `user_role_ref`
--

CREATE TABLE `user_role_ref` (
                                 `id` int NOT NULL,
                                 `user_id` bigint NOT NULL,
                                 `role_id` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vehicle`
--

CREATE TABLE `vehicle` (
                           `id` bigint NOT NULL,
                           `name` varchar(45) NOT NULL,
                           `department_id` bigint NOT NULL,
                           `number` varchar(10) DEFAULT NULL,
                           `special_equipment_consumption` decimal(10,2) NOT NULL,
                           `engine_consumption` decimal(10,2) DEFAULT NULL,
                           `model` varchar(50) DEFAULT NULL,
                           `fuel_consumption` varchar(10) DEFAULT NULL,
                           `options` json DEFAULT NULL COMMENT 'Опции транспорта'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `warehouse`
--

CREATE TABLE `warehouse` (
                             `id` int NOT NULL,
                             `name` varchar(200) NOT NULL COMMENT 'Название склада',
                             `options` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `warehouse_user_ref`
--

CREATE TABLE `warehouse_user_ref` (
                                      `id` int NOT NULL,
                                      `user_id` bigint NOT NULL,
                                      `warehouse_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `waybill`
--

CREATE TABLE `waybill` (
                           `id` int NOT NULL,
                           `vehicle_id` bigint NOT NULL,
                           `driver_id` int NOT NULL,
                           `date_start` date NOT NULL COMMENT 'Время выезда',
                           `date_end` date NOT NULL COMMENT 'Время прибытия',
                           `change_factor` decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT 'Коэф изменения нормы',
                           `speedometer_start` int NOT NULL COMMENT 'Показание спидометра при выезде',
                           `speedometer_end` int NOT NULL COMMENT 'Показание спидометра при возвращении',
                           `fuel_start` int NOT NULL COMMENT 'Остаток топлива при выезде',
                           `fuel_end` int NOT NULL COMMENT 'Остаток топлива при возвращении',
                           `refueled` int NOT NULL DEFAULT '0' COMMENT 'Дозаправлено/выдано топлива',
                           `special_equipment_time` time NOT NULL COMMENT 'Время работы спецоборудования',
                           `engine_time` time NOT NULL COMMENT 'Время работы двигателя',
                           `license` varchar(14) DEFAULT NULL COMMENT 'Номер водительского удостоверения',
                           `car_number` varchar(10) DEFAULT NULL COMMENT 'Номер автомобиля',
                           `waybill_number` int NOT NULL COMMENT 'Номер накладной',
                           `mechanic` varchar(20) NOT NULL COMMENT 'ФИО механика',
                           `dispatcher` varchar(20) NOT NULL COMMENT 'ФИО диспетчера',
                           `fuel_consumption` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `waybill_settings`
--

CREATE TABLE `waybill_settings` (
                                    `id` int NOT NULL,
                                    `name` varchar(20) NOT NULL,
                                    `value` varchar(20) NOT NULL,
                                    `label` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `weighings`
--

CREATE TABLE `weighings` (
                             `id` int NOT NULL,
                             `waybill` int NOT NULL,
                             `date` date NOT NULL,
                             `time` time NOT NULL,
                             `comment` text NOT NULL,
                             `department_id` bigint NOT NULL,
                             `export_id` int NOT NULL,
                             `customer_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `weighing_items`
--

CREATE TABLE `weighing_items` (
                                  `id` int NOT NULL,
                                  `trash` decimal(10,2) NOT NULL,
                                  `clogging` decimal(10,2) NOT NULL,
                                  `tare` decimal(10,2) NOT NULL,
                                  `brutto` decimal(10,2) NOT NULL,
                                  `metal_id` bigint NOT NULL,
                                  `weighing_id` int NOT NULL,
                                  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `authlog`
--
ALTER TABLE `authlog`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bank_account`
--
ALTER TABLE `bank_account`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `blacklist`
--
ALTER TABLE `blacklist`
    ADD UNIQUE KEY `id` (`id`);

--
-- Индексы таблицы `cash_transfer`
--
ALTER TABLE `cash_transfer`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_cash_transfer_bank_account1_idx` (`source_id`),
    ADD KEY `fk_cash_transfer_bank_account2_idx` (`dest_id`);

--
-- Индексы таблицы `category_role_ref`
--
ALTER TABLE `category_role_ref`
    ADD PRIMARY KEY (`id`),
    ADD KEY `category_id` (`category_id`),
    ADD KEY `role_id` (`role_id`);

--
-- Индексы таблицы `containers`
--
ALTER TABLE `containers`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_containers_shipment1_idx` (`shipment_id`);

--
-- Индексы таблицы `container_extra_owner`
--
ALTER TABLE `container_extra_owner`
    ADD PRIMARY KEY (`id`),
    ADD KEY `owner_id` (`owner_id`),
    ADD KEY `container_extra_owner_ibfk_1` (`container_id`);

--
-- Индексы таблицы `container_items`
--
ALTER TABLE `container_items`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_container_items_container1_idx` (`container_id`),
    ADD KEY `fk_container_items_metal1_idx` (`metal_id`);

--
-- Индексы таблицы `cost_category`
--
ALTER TABLE `cost_category`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_cost_categories_cost_categories_group1_idx` (`group_id`);

--
-- Индексы таблицы `cost_category_group`
--
ALTER TABLE `cost_category_group`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `customer`
--
ALTER TABLE `customer`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `customer_debt`
--
ALTER TABLE `customer_debt`
    ADD PRIMARY KEY (`id`),
    ADD KEY `customer_id` (`customer_id`);

--
-- Индексы таблицы `department`
--
ALTER TABLE `department`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `employees`
--
ALTER TABLE `employees`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `expense_shablons`
--
ALTER TABLE `expense_shablons`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_expense_shablons_cost_category1_idx` (`category_id`);

--
-- Индексы таблицы `factoring_assignment_debt`
--
ALTER TABLE `factoring_assignment_debt`
    ADD PRIMARY KEY (`id`),
    ADD KEY `provider_id` (`provider_id`),
    ADD KEY `trader_id` (`trader_id`);

--
-- Индексы таблицы `factoring_payments`
--
ALTER TABLE `factoring_payments`
    ADD PRIMARY KEY (`id`),
    ADD KEY `provider_id` (`provider_id`),
    ADD KEY `bank_id` (`bank_id`),
    ADD KEY `trader_id` (`trader_id`);

--
-- Индексы таблицы `factoring_provider`
--
ALTER TABLE `factoring_provider`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `images`
--
ALTER TABLE `images`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `main_metal_expenses`
--
ALTER TABLE `main_metal_expenses`
    ADD PRIMARY KEY (`id`),
    ADD KEY `customer_id` (`customer_id`),
    ADD KEY `bank_id` (`bank_id`);

--
-- Индексы таблицы `main_other_expenses`
--
ALTER TABLE `main_other_expenses`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_other_expenses_bank_account1_idx` (`bank_id`),
    ADD KEY `fk_other_expenses_cost_category1_idx` (`category_id`);

--
-- Индексы таблицы `main_receipts`
--
ALTER TABLE `main_receipts`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_main_receipts_bank_account1_idx` (`bank_account_id`);

--
-- Индексы таблицы `main_receipts_trader`
--
ALTER TABLE `main_receipts_trader`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_cash_receipts_bank_account_idx` (`bank_account_id`),
    ADD KEY `fk_cash_receipts_trader1_idx` (`trader_id`);

--
-- Индексы таблицы `metal`
--
ALTER TABLE `metal`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_metal_metal_group1_idx` (`group_id`);

--
-- Индексы таблицы `metal_group`
--
ALTER TABLE `metal_group`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Индексы таблицы `money_to_department`
--
ALTER TABLE `money_to_department`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_money_to_department_department1_idx` (`department_id`),
    ADD KEY `fk_money_to_department_bank_account1_idx` (`bank_id`);

--
-- Индексы таблицы `move_of_vehicles`
--
ALTER TABLE `move_of_vehicles`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_move_of_vehicles_vehicle1_idx` (`vehicle_id`),
    ADD KEY `fk_move_of_vehicles_department1_idx` (`department_id`),
    ADD KEY `money_department_id` (`money_department_id`),
    ADD KEY `driver_id` (`driver_id`);

--
-- Индексы таблицы `office_cash_transport_income`
--
ALTER TABLE `office_cash_transport_income`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `owner`
--
ALTER TABLE `owner`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `inn_idx` (`inn`);

--
-- Индексы таблицы `phinxlog`
--
ALTER TABLE `phinxlog`
    ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `purchase`
--
ALTER TABLE `purchase`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_purchase_metal1_idx` (`metal_id`),
    ADD KEY `fk_purchase_department1_idx` (`department_id`),
    ADD KEY `fk_purchase_customer1_idx` (`customer_id`),
    ADD KEY `deal_id` (`deal_id`),
    ADD KEY `department_metal_idx` (`department_id`,`metal_id`) USING BTREE,
    ADD KEY `date_department_idx` (`date`,`department_id`);

--
-- Индексы таблицы `purchase_deal`
--
ALTER TABLE `purchase_deal`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `remote_sklad`
--
ALTER TABLE `remote_sklad`
    ADD UNIQUE KEY `id` (`id`),
    ADD KEY `naklnumb` (`naklnumb`,`date`,`sklad`);

--
-- Индексы таблицы `report_expense_limits`
--
ALTER TABLE `report_expense_limits`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `role`
--
ALTER TABLE `role`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shipment`
--
ALTER TABLE `shipment`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_shipment_department1_idx` (`department_id`),
    ADD KEY `fk_shipment_trader1_idx` (`trader_id`),
    ADD KEY `fk_shipment_tariff_shipment1_idx` (`tariff_id`);

--
-- Индексы таблицы `spares`
--
ALTER TABLE `spares`
    ADD UNIQUE KEY `id` (`id`),
    ADD KEY `article` (`comment`);

--
-- Индексы таблицы `spare_consumption`
--
ALTER TABLE `spare_consumption`
    ADD PRIMARY KEY (`id`),
    ADD KEY `employee_id` (`employee_id`),
    ADD KEY `warehouse_id` (`warehouse_id`);

--
-- Индексы таблицы `spare_consumption_items`
--
ALTER TABLE `spare_consumption_items`
    ADD PRIMARY KEY (`id`),
    ADD KEY `spare_id` (`spare_id`),
    ADD KEY `quantity` (`quantity`),
    ADD KEY `vehicle_id` (`vehicle_id`),
    ADD KEY `consumption_id` (`consumption_id`);

--
-- Индексы таблицы `spare_inventory`
--
ALTER TABLE `spare_inventory`
    ADD PRIMARY KEY (`id`),
    ADD KEY `warehouse_id` (`warehouse_id`);

--
-- Индексы таблицы `spare_inventory_items`
--
ALTER TABLE `spare_inventory_items`
    ADD PRIMARY KEY (`id`),
    ADD KEY `spare_id` (`spare_id`),
    ADD KEY `inventory_id` (`inventory_id`);

--
-- Индексы таблицы `spare_order`
--
ALTER TABLE `spare_order`
    ADD PRIMARY KEY (`id`),
    ADD KEY `status_id` (`status_id`),
    ADD KEY `payment_status_id` (`payment_status_id`),
    ADD KEY `seller_id` (`seller_id`);

--
-- Индексы таблицы `spare_order_cash_expense_ref`
--
ALTER TABLE `spare_order_cash_expense_ref`
    ADD PRIMARY KEY (`id`),
    ADD KEY `expense_id` (`expense_id`),
    ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `spare_order_expense_ref`
--
ALTER TABLE `spare_order_expense_ref`
    ADD PRIMARY KEY (`id`),
    ADD KEY `expense_id` (`expense_id`),
    ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `spare_order_items`
--
ALTER TABLE `spare_order_items`
    ADD PRIMARY KEY (`id`),
    ADD KEY `order_item_id` (`planning_items_id`),
    ADD KEY `spare_id` (`spare_id`),
    ADD KEY `order_id` (`order_id`) USING BTREE;

--
-- Индексы таблицы `spare_order_status`
--
ALTER TABLE `spare_order_status`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `spare_order_status_payment`
--
ALTER TABLE `spare_order_status_payment`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `spare_planning`
--
ALTER TABLE `spare_planning`
    ADD PRIMARY KEY (`id`),
    ADD KEY `status_id` (`status_id`),
    ADD KEY `employee_id` (`employee_id`),
    ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Индексы таблицы `spare_planning_items`
--
ALTER TABLE `spare_planning_items`
    ADD PRIMARY KEY (`id`),
    ADD KEY `spare_id` (`spare_id`),
    ADD KEY `order_id` (`planning_id`);

--
-- Индексы таблицы `spare_planning_status`
--
ALTER TABLE `spare_planning_status`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `spare_receipt`
--
ALTER TABLE `spare_receipt`
    ADD PRIMARY KEY (`id`),
    ADD KEY `warehouse_id` (`warehouse_id`);

--
-- Индексы таблицы `spare_receipt_items`
--
ALTER TABLE `spare_receipt_items`
    ADD PRIMARY KEY (`id`),
    ADD KEY `booking_item_id` (`order_item_id`),
    ADD KEY `invoice_id` (`receipt_id`),
    ADD KEY `spare_id` (`spare_id`);

--
-- Индексы таблицы `spare_seller`
--
ALTER TABLE `spare_seller`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `spare_seller_returns`
--
ALTER TABLE `spare_seller_returns`
    ADD PRIMARY KEY (`id`),
    ADD KEY `seller_id` (`seller_id`);

--
-- Индексы таблицы `spare_transfer`
--
ALTER TABLE `spare_transfer`
    ADD PRIMARY KEY (`id`),
    ADD KEY `spare_id` (`spare_id`),
    ADD KEY `warehouse_exp_id` (`source_id`),
    ADD KEY `warehouse_imp_id` (`dest_id`);

--
-- Индексы таблицы `storage_cash_transfer`
--
ALTER TABLE `storage_cash_transfer`
    ADD UNIQUE KEY `id` (`id`),
    ADD KEY `source_department_id` (`source_department_id`),
    ADD KEY `dest_department_id` (`dest_department_id`);

--
-- Индексы таблицы `storage_metal_expense`
--
ALTER TABLE `storage_metal_expense`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_storage_metal_expense_customer1_idx` (`customer_id`),
    ADD KEY `fk_storage_metal_expense_department1_idx` (`department_id`),
    ADD KEY `deal_id` (`deal_id`),
    ADD KEY `weighing_id` (`weighing_id`);

--
-- Индексы таблицы `storage_other_expense`
--
ALTER TABLE `storage_other_expense`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_storage_other_expense_department1_idx` (`department_id`),
    ADD KEY `fk_storage_other_expense_cost_category1_idx` (`category_id`);

--
-- Индексы таблицы `tariff_shipment`
--
ALTER TABLE `tariff_shipment`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `trader`
--
ALTER TABLE `trader`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `inn_UNIQUE` (`inn`),
    ADD KEY `inn` (`inn`),
    ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `trader_parent`
--
ALTER TABLE `trader_parent`
    ADD UNIQUE KEY `id` (`id`);

--
-- Индексы таблицы `transfer`
--
ALTER TABLE `transfer`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_transfer_department1_idx` (`source_department_id`),
    ADD KEY `fk_transfer_metal1_idx` (`metal_id`),
    ADD KEY `fk_transfer_department2_idx` (`dest_department_id`);

--
-- Индексы таблицы `transport_incas`
--
ALTER TABLE `transport_incas`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_user_department1_idx` (`department_id`);

--
-- Индексы таблицы `user_role_ref`
--
ALTER TABLE `user_role_ref`
    ADD PRIMARY KEY (`id`),
    ADD KEY `user_id` (`user_id`),
    ADD KEY `role_id` (`role_id`);

--
-- Индексы таблицы `vehicle`
--
ALTER TABLE `vehicle`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fk_vehicle_department1_idx` (`department_id`);

--
-- Индексы таблицы `warehouse`
--
ALTER TABLE `warehouse`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `warehouse_user_ref`
--
ALTER TABLE `warehouse_user_ref`
    ADD PRIMARY KEY (`id`),
    ADD KEY `user_id` (`user_id`),
    ADD KEY `warehouse_id` (`warehouse_id`);

--
-- Индексы таблицы `waybill`
--
ALTER TABLE `waybill`
    ADD PRIMARY KEY (`id`),
    ADD KEY `vehicle_id` (`vehicle_id`),
    ADD KEY `driver_id` (`driver_id`);

--
-- Индексы таблицы `waybill_settings`
--
ALTER TABLE `waybill_settings`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `weighings`
--
ALTER TABLE `weighings`
    ADD PRIMARY KEY (`id`),
    ADD KEY `department_id` (`department_id`),
    ADD KEY `customer_id` (`customer_id`);

--
-- Индексы таблицы `weighing_items`
--
ALTER TABLE `weighing_items`
    ADD PRIMARY KEY (`id`),
    ADD KEY `metal_id` (`metal_id`),
    ADD KEY `weighing_id` (`weighing_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `authlog`
--
ALTER TABLE `authlog`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `bank_account`
--
ALTER TABLE `bank_account`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `blacklist`
--
ALTER TABLE `blacklist`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cash_transfer`
--
ALTER TABLE `cash_transfer`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `category_role_ref`
--
ALTER TABLE `category_role_ref`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `containers`
--
ALTER TABLE `containers`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `container_extra_owner`
--
ALTER TABLE `container_extra_owner`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `container_items`
--
ALTER TABLE `container_items`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cost_category`
--
ALTER TABLE `cost_category`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cost_category_group`
--
ALTER TABLE `cost_category_group`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `customer`
--
ALTER TABLE `customer`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `customer_debt`
--
ALTER TABLE `customer_debt`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `department`
--
ALTER TABLE `department`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `employees`
--
ALTER TABLE `employees`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `expense_shablons`
--
ALTER TABLE `expense_shablons`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `factoring_assignment_debt`
--
ALTER TABLE `factoring_assignment_debt`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `factoring_payments`
--
ALTER TABLE `factoring_payments`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `factoring_provider`
--
ALTER TABLE `factoring_provider`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `images`
--
ALTER TABLE `images`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `main_metal_expenses`
--
ALTER TABLE `main_metal_expenses`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `main_other_expenses`
--
ALTER TABLE `main_other_expenses`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `main_receipts`
--
ALTER TABLE `main_receipts`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `main_receipts_trader`
--
ALTER TABLE `main_receipts_trader`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `metal`
--
ALTER TABLE `metal`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `metal_group`
--
ALTER TABLE `metal_group`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `money_to_department`
--
ALTER TABLE `money_to_department`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `move_of_vehicles`
--
ALTER TABLE `move_of_vehicles`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `office_cash_transport_income`
--
ALTER TABLE `office_cash_transport_income`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `owner`
--
ALTER TABLE `owner`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `purchase`
--
ALTER TABLE `purchase`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `purchase_deal`
--
ALTER TABLE `purchase_deal`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `remote_sklad`
--
ALTER TABLE `remote_sklad`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `report_expense_limits`
--
ALTER TABLE `report_expense_limits`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE `role`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `shipment`
--
ALTER TABLE `shipment`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spares`
--
ALTER TABLE `spares`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_consumption`
--
ALTER TABLE `spare_consumption`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_consumption_items`
--
ALTER TABLE `spare_consumption_items`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_inventory`
--
ALTER TABLE `spare_inventory`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_inventory_items`
--
ALTER TABLE `spare_inventory_items`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_order`
--
ALTER TABLE `spare_order`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_order_cash_expense_ref`
--
ALTER TABLE `spare_order_cash_expense_ref`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_order_expense_ref`
--
ALTER TABLE `spare_order_expense_ref`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_order_items`
--
ALTER TABLE `spare_order_items`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_order_status`
--
ALTER TABLE `spare_order_status`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_order_status_payment`
--
ALTER TABLE `spare_order_status_payment`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_planning`
--
ALTER TABLE `spare_planning`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_planning_items`
--
ALTER TABLE `spare_planning_items`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_planning_status`
--
ALTER TABLE `spare_planning_status`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_receipt`
--
ALTER TABLE `spare_receipt`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_receipt_items`
--
ALTER TABLE `spare_receipt_items`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_seller`
--
ALTER TABLE `spare_seller`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_seller_returns`
--
ALTER TABLE `spare_seller_returns`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `spare_transfer`
--
ALTER TABLE `spare_transfer`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `storage_cash_transfer`
--
ALTER TABLE `storage_cash_transfer`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `storage_metal_expense`
--
ALTER TABLE `storage_metal_expense`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `storage_other_expense`
--
ALTER TABLE `storage_other_expense`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tariff_shipment`
--
ALTER TABLE `tariff_shipment`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `trader`
--
ALTER TABLE `trader`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `trader_parent`
--
ALTER TABLE `trader_parent`
    MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `transfer`
--
ALTER TABLE `transfer`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `transport_incas`
--
ALTER TABLE `transport_incas`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user_role_ref`
--
ALTER TABLE `user_role_ref`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vehicle`
--
ALTER TABLE `vehicle`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `warehouse`
--
ALTER TABLE `warehouse`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `warehouse_user_ref`
--
ALTER TABLE `warehouse_user_ref`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `waybill`
--
ALTER TABLE `waybill`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `waybill_settings`
--
ALTER TABLE `waybill_settings`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `weighings`
--
ALTER TABLE `weighings`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `weighing_items`
--
ALTER TABLE `weighing_items`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `cash_transfer`
--
ALTER TABLE `cash_transfer`
    ADD CONSTRAINT `cash_transfer_ibfk_1` FOREIGN KEY (`source_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `cash_transfer_ibfk_2` FOREIGN KEY (`dest_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `category_role_ref`
--
ALTER TABLE `category_role_ref`
    ADD CONSTRAINT `category_role_ref_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `cost_category` (`id`),
    ADD CONSTRAINT `category_role_ref_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

--
-- Ограничения внешнего ключа таблицы `containers`
--
ALTER TABLE `containers`
    ADD CONSTRAINT `containers_ibfk_2` FOREIGN KEY (`shipment_id`) REFERENCES `shipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `container_extra_owner`
--
ALTER TABLE `container_extra_owner`
    ADD CONSTRAINT `container_extra_owner_ibfk_1` FOREIGN KEY (`container_id`) REFERENCES `containers` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `container_extra_owner_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `owner` (`id`);

--
-- Ограничения внешнего ключа таблицы `container_items`
--
ALTER TABLE `container_items`
    ADD CONSTRAINT `container_items_ibfk_1` FOREIGN KEY (`container_id`) REFERENCES `containers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `container_items_ibfk_2` FOREIGN KEY (`metal_id`) REFERENCES `metal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `cost_category`
--
ALTER TABLE `cost_category`
    ADD CONSTRAINT `cost_category_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `cost_category_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `customer_debt`
--
ALTER TABLE `customer_debt`
    ADD CONSTRAINT `customer_debt_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

--
-- Ограничения внешнего ключа таблицы `expense_shablons`
--
ALTER TABLE `expense_shablons`
    ADD CONSTRAINT `expense_shablons_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `cost_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `factoring_assignment_debt`
--
ALTER TABLE `factoring_assignment_debt`
    ADD CONSTRAINT `factoring_assignment_debt_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `factoring_provider` (`id`),
    ADD CONSTRAINT `factoring_assignment_debt_ibfk_2` FOREIGN KEY (`trader_id`) REFERENCES `trader` (`id`);

--
-- Ограничения внешнего ключа таблицы `factoring_payments`
--
ALTER TABLE `factoring_payments`
    ADD CONSTRAINT `factoring_payments_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `factoring_provider` (`id`),
    ADD CONSTRAINT `factoring_payments_ibfk_2` FOREIGN KEY (`bank_id`) REFERENCES `bank_account` (`id`),
    ADD CONSTRAINT `factoring_payments_ibfk_3` FOREIGN KEY (`trader_id`) REFERENCES `trader` (`id`);

--
-- Ограничения внешнего ключа таблицы `main_metal_expenses`
--
ALTER TABLE `main_metal_expenses`
    ADD CONSTRAINT `main_metal_expenses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
    ADD CONSTRAINT `main_metal_expenses_ibfk_2` FOREIGN KEY (`bank_id`) REFERENCES `bank_account` (`id`);

--
-- Ограничения внешнего ключа таблицы `main_other_expenses`
--
ALTER TABLE `main_other_expenses`
    ADD CONSTRAINT `main_other_expenses_ibfk_1` FOREIGN KEY (`bank_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `main_other_expenses_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `cost_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `main_receipts`
--
ALTER TABLE `main_receipts`
    ADD CONSTRAINT `main_receipts_ibfk_1` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `main_receipts_trader`
--
ALTER TABLE `main_receipts_trader`
    ADD CONSTRAINT `main_receipts_trader_ibfk_1` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `main_receipts_trader_ibfk_2` FOREIGN KEY (`trader_id`) REFERENCES `trader` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `money_to_department`
--
ALTER TABLE `money_to_department`
    ADD CONSTRAINT `money_to_department_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `money_to_department_ibfk_2` FOREIGN KEY (`bank_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `move_of_vehicles`
--
ALTER TABLE `move_of_vehicles`
    ADD CONSTRAINT `move_of_vehicles_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `move_of_vehicles_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `move_of_vehicles_ibfk_3` FOREIGN KEY (`money_department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `move_of_vehicles_ibfk_4` FOREIGN KEY (`driver_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `purchase`
--
ALTER TABLE `purchase`
    ADD CONSTRAINT `purchase_ibfk_1` FOREIGN KEY (`metal_id`) REFERENCES `metal` (`id`) ON UPDATE CASCADE,
    ADD CONSTRAINT `purchase_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON UPDATE CASCADE,
    ADD CONSTRAINT `purchase_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON UPDATE CASCADE,
    ADD CONSTRAINT `purchase_ibfk_4` FOREIGN KEY (`deal_id`) REFERENCES `purchase_deal` (`id`);

--
-- Ограничения внешнего ключа таблицы `shipment`
--
ALTER TABLE `shipment`
    ADD CONSTRAINT `shipment_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `shipment_ibfk_2` FOREIGN KEY (`tariff_id`) REFERENCES `tariff_shipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `spare_consumption`
--
ALTER TABLE `spare_consumption`
    ADD CONSTRAINT `spare_consumption_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
    ADD CONSTRAINT `spare_consumption_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`);

--
-- Ограничения внешнего ключа таблицы `spare_consumption_items`
--
ALTER TABLE `spare_consumption_items`
    ADD CONSTRAINT `spare_consumption_items_ibfk_1` FOREIGN KEY (`spare_id`) REFERENCES `spares` (`id`),
    ADD CONSTRAINT `spare_consumption_items_ibfk_4` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`id`),
    ADD CONSTRAINT `spare_consumption_items_ibfk_5` FOREIGN KEY (`consumption_id`) REFERENCES `spare_consumption` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `spare_inventory`
--
ALTER TABLE `spare_inventory`
    ADD CONSTRAINT `spare_inventory_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`);

--
-- Ограничения внешнего ключа таблицы `spare_inventory_items`
--
ALTER TABLE `spare_inventory_items`
    ADD CONSTRAINT `spare_inventory_items_ibfk_1` FOREIGN KEY (`spare_id`) REFERENCES `spares` (`id`),
    ADD CONSTRAINT `spare_inventory_items_ibfk_2` FOREIGN KEY (`inventory_id`) REFERENCES `spare_inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `spare_order`
--
ALTER TABLE `spare_order`
    ADD CONSTRAINT `spare_order_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `spare_order_status` (`id`),
    ADD CONSTRAINT `spare_order_ibfk_2` FOREIGN KEY (`payment_status_id`) REFERENCES `spare_order_status_payment` (`id`),
    ADD CONSTRAINT `spare_order_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `spare_seller` (`id`);

--
-- Ограничения внешнего ключа таблицы `spare_order_cash_expense_ref`
--
ALTER TABLE `spare_order_cash_expense_ref`
    ADD CONSTRAINT `spare_order_cash_expense_ref_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `storage_other_expense` (`id`),
    ADD CONSTRAINT `spare_order_cash_expense_ref_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `spare_order` (`id`);

--
-- Ограничения внешнего ключа таблицы `spare_order_expense_ref`
--
ALTER TABLE `spare_order_expense_ref`
    ADD CONSTRAINT `spare_order_expense_ref_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `main_other_expenses` (`id`),
    ADD CONSTRAINT `spare_order_expense_ref_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `spare_order` (`id`);

--
-- Ограничения внешнего ключа таблицы `spare_order_items`
--
ALTER TABLE `spare_order_items`
    ADD CONSTRAINT `spare_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `spare_order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `spare_order_items_ibfk_2` FOREIGN KEY (`spare_id`) REFERENCES `spares` (`id`),
    ADD CONSTRAINT `spare_order_items_ibfk_3` FOREIGN KEY (`planning_items_id`) REFERENCES `spare_planning_items` (`id`);

--
-- Ограничения внешнего ключа таблицы `spare_planning`
--
ALTER TABLE `spare_planning`
    ADD CONSTRAINT `spare_planning_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `spare_planning_status` (`id`),
    ADD CONSTRAINT `spare_planning_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
    ADD CONSTRAINT `spare_planning_ibfk_3` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`id`);

--
-- Ограничения внешнего ключа таблицы `spare_planning_items`
--
ALTER TABLE `spare_planning_items`
    ADD CONSTRAINT `spare_planning_items_ibfk_1` FOREIGN KEY (`spare_id`) REFERENCES `spares` (`id`),
    ADD CONSTRAINT `spare_planning_items_ibfk_2` FOREIGN KEY (`planning_id`) REFERENCES `spare_planning` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `spare_receipt`
--
ALTER TABLE `spare_receipt`
    ADD CONSTRAINT `spare_receipt_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`);

--
-- Ограничения внешнего ключа таблицы `spare_receipt_items`
--
ALTER TABLE `spare_receipt_items`
    ADD CONSTRAINT `spare_receipt_items_ibfk_1` FOREIGN KEY (`spare_id`) REFERENCES `spares` (`id`),
    ADD CONSTRAINT `spare_receipt_items_ibfk_2` FOREIGN KEY (`receipt_id`) REFERENCES `spare_receipt` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `spare_seller_returns`
--
ALTER TABLE `spare_seller_returns`
    ADD CONSTRAINT `spare_seller_returns_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `spare_seller` (`id`);

--
-- Ограничения внешнего ключа таблицы `spare_transfer`
--
ALTER TABLE `spare_transfer`
    ADD CONSTRAINT `spare_transfer_ibfk_1` FOREIGN KEY (`spare_id`) REFERENCES `spares` (`id`),
    ADD CONSTRAINT `spare_transfer_ibfk_2` FOREIGN KEY (`source_id`) REFERENCES `warehouse` (`id`),
    ADD CONSTRAINT `spare_transfer_ibfk_3` FOREIGN KEY (`dest_id`) REFERENCES `warehouse` (`id`);

--
-- Ограничения внешнего ключа таблицы `storage_cash_transfer`
--
ALTER TABLE `storage_cash_transfer`
    ADD CONSTRAINT `storage_cash_transfer_ibfk_1` FOREIGN KEY (`source_department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `storage_cash_transfer_ibfk_2` FOREIGN KEY (`dest_department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `storage_metal_expense`
--
ALTER TABLE `storage_metal_expense`
    ADD CONSTRAINT `storage_metal_expense_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON UPDATE CASCADE,
    ADD CONSTRAINT `storage_metal_expense_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON UPDATE CASCADE,
    ADD CONSTRAINT `storage_metal_expense_ibfk_3` FOREIGN KEY (`deal_id`) REFERENCES `purchase_deal` (`id`),
    ADD CONSTRAINT `storage_metal_expense_ibfk_4` FOREIGN KEY (`weighing_id`) REFERENCES `weighings` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `storage_other_expense`
--
ALTER TABLE `storage_other_expense`
    ADD CONSTRAINT `storage_other_expense_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `storage_other_expense_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `cost_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `trader`
--
ALTER TABLE `trader`
    ADD CONSTRAINT `trader_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `trader_parent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `transfer`
--
ALTER TABLE `transfer`
    ADD CONSTRAINT `transfer_ibfk_1` FOREIGN KEY (`source_department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `transfer_ibfk_2` FOREIGN KEY (`dest_department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `transfer_ibfk_3` FOREIGN KEY (`metal_id`) REFERENCES `metal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user`
--
ALTER TABLE `user`
    ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_role_ref`
--
ALTER TABLE `user_role_ref`
    ADD CONSTRAINT `user_role_ref_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
    ADD CONSTRAINT `user_role_ref_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

--
-- Ограничения внешнего ключа таблицы `vehicle`
--
ALTER TABLE `vehicle`
    ADD CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `warehouse_user_ref`
--
ALTER TABLE `warehouse_user_ref`
    ADD CONSTRAINT `warehouse_user_ref_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
    ADD CONSTRAINT `warehouse_user_ref_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `waybill`
--
ALTER TABLE `waybill`
    ADD CONSTRAINT `waybill_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `waybill_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `weighings`
--
ALTER TABLE `weighings`
    ADD CONSTRAINT `weighings_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`),
    ADD CONSTRAINT `weighings_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `weighing_items`
--
ALTER TABLE `weighing_items`
    ADD CONSTRAINT `weighing_items_ibfk_1` FOREIGN KEY (`metal_id`) REFERENCES `metal` (`id`),
    ADD CONSTRAINT `weighing_items_ibfk_2` FOREIGN KEY (`weighing_id`) REFERENCES `weighings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
