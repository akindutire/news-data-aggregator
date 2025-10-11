<?php

namespace App\Http\Controllers;

use App\Services\Contracts\AggregatorInterface;
use App\Services\Contracts\FetchOpts;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{

    public function __construct(private AggregatorInterface $newsAggregator) {

    }

    public function allCategories(Request $request) {
        $fetchOps = new FetchOpts;
        $fetchOps->fields = ['category'];
        $fetchOps->distinct = true;
        $fetchOps->shouldPaginate = false;

        $result = ($this->newsAggregator->fetch($fetchOps))->map(fn($item) => $item->category);

        return response(['message' => 'Categories fetched', 'data' => ['categories' => $result ] ], 200);
    }

    public function get(Request $request) {

        // Strip quotes and spaces from all input parameters
        $request->merge(array_map(function($value) {
            if (is_string($value)) {
                return trim(str_replace(["'", '"'], '', $value));
            }
            return $value;
        }, $request->all()));

        $fetchOps = new FetchOpts;
        if( $request->has('title') ) {
            $fetchOps->title = $request->title;
        }

        if( $request->has('category') ) {
            $fetchOps->category = explode(',', $request->category);
        }

        if( $request->has('author') ) {
            $fetchOps->author = explode(',', $request->author);
        }

        if( $request->has('from') && !empty($request->from)) {

            $fetchOps->fromDate = new \DateTime($request->from);
        }

        if( $request->has('to') && !empty($request->to)) {
            $fetchOps->toDate = new \DateTime($request->to);
        }

        if( $request->has('source') ) {
            $fetchOps->source = explode(',', $request->source);
        }

        if( $request->has('page') ) {
            $fetchOps->page = $request->page;
        }

        $fetchOps->orderBy = 'published_at';
        $fetchOps->orderByDir = 'DESC';
        $fetchOps->perPage = 50;

        $results = $this->newsAggregator->fetch($fetchOps);
        return response()->json(['message' => 'News fetched', 'data' => NewsResource::collection($results)], 200);
    }
}
