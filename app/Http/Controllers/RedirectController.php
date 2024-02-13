<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRedirectRequest;
use App\Http\Requests\UpdateRedirectRequest;
use App\Models\Redirect;
use App\Services\HashidsService;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    protected $hashidsService;

    public function __construct(HashidsService $hashidsService)
    {
        $this->hashidsService = $hashidsService;
    }

    public function index()
    {
        $redirects = Redirect::all();
        return response()->json($redirects);
    }

    public function store(CreateRedirectRequest $request)
    {
        $redirect = new Redirect($request->validated());
        $redirect->save();

        return response()->json([
            'message' => 'Redirect created successfully',
            'code' => $this->hashidsService->encode($redirect->id)
        ], 201);
    }

    public function show($code)
    {
        $id = $this->hashidsService->decode($code);
        $redirect = Redirect::find($id);

        if (!$redirect) {
            return response()->json(['message' => 'Redirect not found'], 404);
        }

        return response()->json($redirect);
    }

    public function update(UpdateRedirectRequest $request, $code)
    {
        $id = $this->hashidsService->decode($code);
        $redirect = Redirect::find($id);

        if (!$redirect) {
            return response()->json(['message' => 'Redirect not found'], 404);
        }

        $redirect->update($request->validated());

        return response()->json(['message' => 'Redirect updated successfully']);
    }

    public function destroy($code)
    {
        $id = $this->hashidsService->decode($code);
        $redirect = Redirect::find($id);

        if (!$redirect) {
            return response()->json(['message' => 'Redirect not found'], 404);
        }

        $redirect->delete();

        return response()->json(['message' => 'Redirect deleted successfully']);
    }

    public function redirectToDestination($code, Request $request)
    {
        $id = $this->hashidsService->decode($code);
        $redirect = Redirect::with('logs')->find($id);

        if (!$redirect || !$redirect->active) {
            return response()->json(['message' => 'Redirect not found or inactive'], 404);
        }

        $accessLogData = [
            'redirect_id' => $redirect->id,
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'referer' => $request->header('Referer'),
            'query_params' => json_encode($request->query()),
            'accessed_at' => now()
        ];

        $redirect->logs()->create($accessLogData);

        $destinationUrl = $redirect->destination_url;

        $queryParams = array_merge($request->query(), $redirect->query_params ?? []);
        if (!empty($queryParams)) {
            $destinationUrl .= '?' . http_build_query($queryParams);
        }

        return redirect()->to($destinationUrl);
    }

}
