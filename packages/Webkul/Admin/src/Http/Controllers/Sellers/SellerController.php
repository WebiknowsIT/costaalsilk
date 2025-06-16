<?php

namespace Webkul\Admin\Http\Controllers\Sellers;

use Webkul\Admin\DataGrids\Sellers\SellerDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Seller\Repositories\SellerGroupRepository;

class SellerController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected SellerGroupRepository $sellerGroupRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(SellerDataGrid::class)->process();
        }
        $groups = $this->sellerGroupRepository->findWhere([['code', '<>', 'guest']]);

        return view('admin::sellers.sellers.index', compact('groups'));
    }
}