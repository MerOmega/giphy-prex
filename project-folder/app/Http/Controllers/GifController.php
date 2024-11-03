<?php

namespace App\Http\Controllers;

use App\Models\FavoriteGif;
use App\Models\User;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GifController extends Controller
{
    private string $giphySearchUrl     = 'https://api.giphy.com/v1/gifs/search';
    private string $giphySearchByIdUrl = 'https://api.giphy.com/v1/gifs/';

    /** @var string|Repository|Application|\Illuminate\Foundation\Application|mixed
     * The Giphy API key is stores in an env file and set up in services.php
     * */
    private string $giphyApiKey;

    public function __construct()
    {
        $this->giphyApiKey = config('services.giphy.key');
    }

    /**
     * Returns a list of GIFs based on a search query.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchGifs(Request $request): JsonResponse
    {
        $request->validate([
            'query'  => 'required|string',
            'limit'  => 'sometimes|integer|min:1',
            'offset' => 'sometimes|integer|min:0',
        ]);

        $query  = $request->input('query');
        $limit  = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        $response = Http::get($this->giphySearchUrl, [
            'api_key' => $this->giphyApiKey,
            'q'       => $query,
            'limit'   => $limit,
            'offset'  => $offset,
        ]);

        return response()->json($response->json());
    }

    /**
     * Returns a GIF by its ID.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function getGifById(string $id): JsonResponse
    {
        $response = Http::get($this->giphySearchByIdUrl . "{$id}", [
            'api_key' => $this->giphyApiKey,
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'GIF not found'], 404);
        }
    }

    /**
     * Receives a request to save a GIF as a favorite.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveFavoriteGif(Request $request): JsonResponse
    {
        $request->validate([
            'gif_id'  => 'required|string',
            'alias'   => 'required|string',
            'user_id' => 'required|integer',
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        FavoriteGif::create([
            'gif_id'  => $request->gif_id,
            'alias'   => $request->alias,
            'user_id' => $request->user_id,
        ]);

        return response()->json(['message' => 'GIF saved as favorite'], 201);
    }

}
