<?php

namespace Webkul\Admin\DataGrids\Sellers;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Seller\Repositories\SellerGroupRepository;

class SellerDataGrid extends DataGrid
{
    /**
     * Index.
     *
     * @var string
     */
    protected $primaryColumn = 'seller_id';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected SellerGroupRepository $sellerGroupRepository) {}

    /**
     * Prepare query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function prepareQueryBuilder()
    {
        $tablePrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('sellers')
            ->leftJoin('addresses', function ($join) {
                $join->on('sellers.id', '=', 'addresses.seller_id')
                    ->where('addresses.address_type', '=', 'customer');
            })
            ->leftJoin('orders', 'sellers.id', '=', 'orders.seller_id')
            ->leftJoin('seller_groups', 'sellers.customer_group_id', '=', 'seller_groups.id')
            ->addSelect(
                'sellers.id as seller_id',
                'sellers.email',
                'sellers.phone',
                'sellers.gender',
                'sellers.status',
                'sellers.is_suspended',
                'seller_groups.name as group',
                'sellers.channel_id',
            )
            ->addSelect(DB::raw('COUNT(DISTINCT '.$tablePrefix.'addresses.id) as address_count'))
            ->addSelect(DB::raw('COUNT(DISTINCT '.$tablePrefix.'orders.id) as order_count'))
            ->addSelect(DB::raw('CONCAT('.$tablePrefix.'sellers.first_name, " ", '.$tablePrefix.'sellers.last_name) as full_name'))
            ->groupBy('sellers.id');

        $this->addFilter('channel_id', 'sellers.channel_id');
        $this->addFilter('seller_id', 'sellers.id');
        $this->addFilter('email', 'sellers.email');
        $this->addFilter('full_name', DB::raw('CONCAT('.$tablePrefix.'sellers.first_name, " ", '.$tablePrefix.'sellers.last_name)'));
        $this->addFilter('group', 'seller_groups.name');
        $this->addFilter('phone', 'sellers.phone');
        $this->addFilter('status', 'sellers.status');

        return $queryBuilder;
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'              => 'channel_id',
            'label'              => trans('admin::app.sellers.sellers.index.datagrid.channel'),
            'type'               => 'string',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => collect(core()->getAllChannels())
                ->map(fn ($channel) => ['label' => $channel->name, 'value' => $channel->id])
                ->values()
                ->toArray(),
            'sortable'   => true,
            'visibility' => false,
        ]);

        $this->addColumn([
            'index'      => 'seller_id',
            'label'      => trans('admin::app.sellers.sellers.index.datagrid.id'),
            'type'       => 'integer',
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'full_name',
            'label'      => trans('admin::app.sellers.sellers.index.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'email',
            'label'      => trans('admin::app.sellers.sellers.index.datagrid.email'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'phone',
            'label'      => trans('admin::app.sellers.sellers.index.datagrid.phone'),
            'type'       => 'integer',
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('admin::app.sellers.sellers.index.datagrid.status'),
            'type'               => 'boolean',
            'filterable'         => true,
            'filterable_options' => [
                [
                    'label' => trans('admin::app.sellers.sellers.index.datagrid.active'),
                    'value' => 1,
                ],
                [
                    'label' => trans('admin::app.sellers.sellers.index.datagrid.inactive'),
                    'value' => 0,
                ],
            ],
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'gender',
            'label'      => trans('admin::app.sellers.sellers.index.datagrid.gender'),
            'type'       => 'string',
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'              => 'group',
            'label'              => trans('admin::app.sellers.sellers.index.datagrid.group'),
            'type'               => 'string',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => $this->sellerGroupRepository->all(['name as label', 'name as value'])->toArray(),
        ]);

        $this->addColumn([
            'index'    => 'is_suspended',
            'label'    => trans('admin::app.sellers.sellers.index.datagrid.suspended'),
            'type'     => 'boolean',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'       => 'revenue',
            'label'       => trans('admin::app.sellers.sellers.index.datagrid.revenue'),
            'type'        => 'integer',
            'closure'     => function ($row) {
                return app(OrderRepository::class)->scopeQuery(function ($q) use ($row) {
                    return $q->whereNotIn('status', [Order::STATUS_CANCELED, Order::STATUS_CLOSED])
                        ->where('seller_id', $row->seller_id);
                })->sum('base_grand_total_invoiced');
            },
        ]);

        $this->addColumn([
            'index'       => 'order_count',
            'label'       => trans('admin::app.sellers.sellers.index.datagrid.order-count'),
            'type'        => 'integer',
            'sortable'    => true,
        ]);

        $this->addColumn([
            'index'       => 'address_count',
            'label'       => trans('admin::app.sellers.sellers.index.datagrid.address-count'),
            'type'        => 'integer',
            'sortable'    => true,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'icon'   => 'icon-view',
            'title'  => trans('admin::app.sellers.sellers.index.datagrid.view'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.sellers.sellers.view', $row->seller_id);
            },
        ]);

        $this->addAction([
            'icon'   => 'icon-exit',
            'title'  => trans('admin::app.sellers.sellers.index.datagrid.login-as-customer'),
            'method' => 'GET',
            'target' => 'blank',
            'url'    => function ($row) {
                return route('admin.sellers.sellers.login_as_customer', $row->seller_id);
            },
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        if (bouncer()->hasPermission('sellers.sellers.delete')) {
            $this->addMassAction([
                'title'  => trans('admin::app.sellers.sellers.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => route('admin.sellers.sellers.mass_delete'),
            ]);
        }

        if (bouncer()->hasPermission('sellers.sellers.edit')) {
            $this->addMassAction([
                'title'   => trans('admin::app.sellers.sellers.index.datagrid.update-status'),
                'method'  => 'POST',
                'url'     => route('admin.sellers.sellers.mass_update'),
                'options' => [
                    [
                        'label' => trans('admin::app.sellers.sellers.index.datagrid.active'),
                        'value' => 1,
                    ],
                    [
                        'label' => trans('admin::app.sellers.sellers.index.datagrid.inactive'),
                        'value' => 0,
                    ],
                ],
            ]);
        }
    }
}
