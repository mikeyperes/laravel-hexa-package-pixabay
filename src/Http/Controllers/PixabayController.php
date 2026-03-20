<?php

namespace hexa_package_pixabay\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use hexa_package_pixabay\Services\PixabayService;

/**
 * PixabayController — handles the raw dev page and AJAX search endpoint.
 */
class PixabayController extends Controller
{
    /**
     * Show the raw dev page for Pixabay.
     *
     * @return \Illuminate\View\View
     */
    public function raw()
    {
        return view('pixabay::raw.index');
    }

    /**
     * AJAX: Search photos via Pixabay API.
     *
     * @param Request $request
     * @param PixabayService $service
     * @return JsonResponse
     */
    public function search(Request $request, PixabayService $service): JsonResponse
    {
        $request->validate([
            'query'    => 'required|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:200',
            'page'     => 'nullable|integer|min:1',
        ]);

        $result = $service->searchPhotos(
            $request->input('query'),
            (int) $request->input('per_page', 15),
            (int) $request->input('page', 1)
        );

        return response()->json($result);
    }
}
