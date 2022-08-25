<?php

namespace App\Http\Controllers\Reports;

use App\Exceptions\InvalidSelectException;
use App\Http\Controllers\Controller;
use App\Modules\Reports\src\Models\RestockingReport;
use App\Traits\CsvFileResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class RestockingReportController extends Controller
{
    use CsvFileResponse;

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     * @throws InvalidSelectException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request)
    {
        if (($request->query->count() === 0) && (Auth::user()->warehouse)) {
            return redirect(route('reports.restocking', ['filter[warehouse_code]' => Auth::user()->warehouse->code]));
        }

        $report = new RestockingReport();
        $report->view = 'reports.restocking-report';

        return $report->response($request);
    }
}
