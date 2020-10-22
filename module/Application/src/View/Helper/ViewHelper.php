<?php

namespace Application\View\Helper;

use Reference\Entity\Department;
use Reference\Entity\Warehouse;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class ViewHelper extends AbstractHelper
{
    private $requestUri;
    private $urlPlugin;
    private $config;
    private $departments;
    private $acl;
    private $viewRenderer;
    private $router;

    private $userRoleName;
    private $userDepartment;
    private $logger;

    /**
     * @var \Doctrine\ORM\PersistentCollection
     */
    private $warehouses;

    public function __construct(ServiceManager $container)
    {
        $this->requestUri = $container->get('Request')->getRequestUri();
        $this->urlPlugin =
            $container->get('ControllerPluginManager')->get('url');

        $this->config = $container->get('Config');

        $entityManager = $container->get('entityManager');
        $this->departments =
            $entityManager->getRepository(Department::class)->findBy([]);

        $this->acl = $container->get('acl');
        $this->viewRenderer = $container->get('ViewRenderer');
        $this->router = $container->get('Router');

        $authService = $container->get('authenticationService');
        $user = $authService->getIdentity();

        $this->warehouses = $user->getWarehouses();

        $this->userRoleName = $user->getRoleNames();
        $this->userDepartment = $user->getDepartment();
        $this->logger = $container->get('MyLogger');
    }

    /**
     * Рендерит меню по массиву из конфига, либо по полученному в аргументе (если отображается меню уровня 2 и глубже)
     *
     * @return mixed
     */
    public function showMenu()
    {
        try {
            $menu = $this->config['menu'];
            $ferrous = [];
            $nonFerrous = [];

            foreach ($this->departments as $dep) {
                //Если площадка невидимая и юзер не админ
                if ($dep->isHide()) {
                    continue;
                }

                //Если у отделения стоит флаг - закрыт
                if ($dep->isClosed()) {
                    continue;
                }

                if ($dep->getType() == Department::TYPE_BLACK) {
                    $submenu = $this->config['ferrousMenu']['departmentMenu'];
                } else {
                    $submenu =
                        $this->config['nonFerrousMenu']['departmentMenu'];
                }

                $sub = $this->addDepartment($submenu, $dep->getId());

                if ($dep->getType() == Department::TYPE_BLACK) {
                    $ferrous['f-' . $dep->getId()] =
                        ['title' => $dep->getName(), 'sub-menu' => $sub,];
                } else {
                    $nonFerrous['nf-' . $dep->getId()] =
                        ['title' => $dep->getName(), 'sub-menu' => $sub,];
                }
            }

            $this->putWarehousesToMenu($menu);

            if ($this->userDepartment) {
                if ($this->userDepartment->getType() ==
                    Department::TYPE_BLACK) {
                    $menu = array_merge($ferrous['f-' .
                    $this->userDepartment->getId()]['sub-menu'], $menu);
                } else {
                    $menu = array_merge($nonFerrous['nf-' .
                    $this->userDepartment->getId()]['sub-menu'], $menu);
                }
                unset($menu['legalCash']);
                unset($menu['storage']);
                unset($menu['non-ferrous']);
                unset($menu['ferrous']);
            } else {
                $menu['non-ferrous']['sub-menu'] =
                    array_merge($menu['non-ferrous']['sub-menu'], $nonFerrous);
                $menu['non-ferrous']['sub-menu'] = array_merge($menu['non-ferrous']['sub-menu'], $this->config['nonFerrousMenu']['all']);
                $menu['ferrous']['sub-menu'] =
                    array_merge($menu['ferrous']['sub-menu'], $ferrous);
                $menu['ferrous']['sub-menu'] = array_merge($menu['ferrous']['sub-menu'], $this->config['ferrousMenu']['all']);
            }

            $menu = array_merge($this->config['menu_top'], $menu);
            $menu = array_merge($menu, $this->config['menu_down']);

            $menu = $this->hideClosedMenu($menu);
            $jsonMenu = $this->menuToJson($menu);


            $viewModel = new ViewModel(['jsonMenu' => Json::encode($jsonMenu)]);
            $viewModel->setTemplate('shared/menu');
            return $this->viewRenderer->render($viewModel);
        } catch (\Exception $e) {
            $this->logger->err($e->getTraceAsString());
        }
    }

    private function menuToJson(array $menu)
    {
        $jsonMenu = [];
        foreach ($menu as $item) {
            $newItem = [];
            $newItem['name'] = $item['title'];
            if (isset($item['route'])) {
                $newItem['href'] = $this->itemUrl($item);
            }
            if (isset($item['sub-menu'])) {
                $newItem['submenu'] = $this->menuToJson($item['sub-menu']);
            }
            if (isset($item['active'])) {
                $newItem['active'] = true;
            }
            $jsonMenu[] = $newItem;
        }
        return $jsonMenu;
    }

    private function addDepartment(array $menu, $dep_id)
    {
        $res = [];
        foreach ($menu as $key => $value) {
            if ($key === 'route-params') {
                $res[$key] = $value;
                $res[$key]['department'] = $dep_id;
            } elseif ($key === 'store' || $key === 'cash' ||
                $key === 'cash-expenses') {
                $res[$key . $dep_id] = $this->addDepartment($value, $dep_id);
            } elseif (is_array($value)) {
                $res[$key] = $this->addDepartment($value, $dep_id);
            } else {
                $res[$key] = $value;
            }
        }
        return $res;
    }

    private function isActiveItem($routeUri)
    {
        $routeData = $this->parseRouteData($routeUri);
        $requestRouteData = $this->parseRouteData($this->requestUri);

        return $routeData == $requestRouteData;
    }

    private function hideClosedMenu($menu)
    {
        $data = [];
        foreach ($menu as $key => $value) {
            if (isset($value['sub-menu'])) {
                $submenu = $this->hideClosedMenu($value['sub-menu']);
                if ($submenu) {
                    $data[$key]['title'] = $value['title'];
                    $data[$key]['sub-menu'] = $submenu;
                }
            } elseif (isset($value['route'])) {
                if (isset($value['route']) && $value['route'] != '#') {
                    $routeUri = $this->itemUrl($value);
                    $routeData = $this->parseRouteData($routeUri);

                    foreach ($this->userRoleName as $userRole) {
                        if ($this->acl->isAllowed($userRole, $routeData['controller'], $routeData['action'])) {
                            if ($this->userDepartment == null || $routeData['department'] == null || $this->userDepartment->getId() == $routeData['department']) {
                                $data[$key]['title'] = $value['title'];
                                $data[$key] = $menu[$key];
                                if ($this->isActiveItem($routeUri)) {
                                    $data[$key]['active'] = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

    private function parseRouteData($routeUri)
    {
        $request = new Request();
        $request->setUri($routeUri);
        $routeMatch = $this->router->match($request);
        return [
            'controller' => $routeMatch->getParam('controller', null),
            'action' => $routeMatch->getParam('action', null),
            'department' => $routeMatch->getParam('department', null),
            'warehouse' => $routeMatch->getParam('warehouse', null)
        ];
    }

    private function itemUrl($item)
    {
        return $this->urlPlugin->fromRoute($item['route'], isset($item['route-params']) ? $item['route-params'] : []);
    }

    /**
     * Добавляет в меню данные о доступных хоз. складах
     *
     * @param array $menu
     */
    private function putWarehousesToMenu(&$menu)
    {
        if (empty($this->warehouses)) {
            return;
        }

        foreach ($this->warehouses as $warehouse) {
            if ($warehouse->isClosed()) {
                continue;
            }

            $menu['warehouse']['sub-menu'][] =
                [
                    'title' => $warehouse->getName(),
                    'sub-menu' => $this->addWarehouse($this->config['warehouseMenu']['sub-menu'], $warehouse->getId())
                ];
        }
    }

    private function addWarehouse($menu, $id)
    {
        $res = [];
        foreach ($menu as $key => $value) {
            if ($key === 'route-params') {
                $res[$key] = $value;
                $res[$key]['warehouse'] = $id;
            } elseif (is_array($value)) {
                $res[$key] = $this->addWarehouse($value, $id);
            } else {
                $res[$key] = $value;
            }
        }
        return $res;
    }
}
