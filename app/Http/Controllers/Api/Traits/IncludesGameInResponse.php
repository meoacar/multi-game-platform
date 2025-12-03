<?php

namespace App\Http\Controllers\Api\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Includes Game In Response Trait
 * 
 * API response'larına game bilgisi eklemek için kullanılan trait.
 */
trait IncludesGameInResponse
{
    /**
     * Response'a game bilgisi ekle
     * 
     * @param array $response
     * @param Model|Collection|LengthAwarePaginator $data
     * @return array
     */
    protected function addGameToResponse(array $response, $data): array
    {
        // Tek bir model için
        if ($data instanceof Model && $data->relationLoaded('game')) {
            $response['game'] = [
                'id' => $data->game->id,
                'name' => $data->game->name,
                'slug' => $data->game->slug,
            ];
        }
        
        // Collection için (ilk item'dan game bilgisini al)
        if ($data instanceof Collection && $data->isNotEmpty()) {
            $firstItem = $data->first();
            if ($firstItem instanceof Model && $firstItem->relationLoaded('game')) {
                $response['game'] = [
                    'id' => $firstItem->game->id,
                    'name' => $firstItem->game->name,
                    'slug' => $firstItem->game->slug,
                ];
            }
        }
        
        // Paginated data için
        if ($data instanceof LengthAwarePaginator && $data->isNotEmpty()) {
            $firstItem = $data->first();
            if ($firstItem instanceof Model && $firstItem->relationLoaded('game')) {
                $response['game'] = [
                    'id' => $firstItem->game->id,
                    'name' => $firstItem->game->name,
                    'slug' => $firstItem->game->slug,
                ];
            }
        }
        
        return $response;
    }
    
    /**
     * Success response with game info
     * 
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successWithGame($data, string $message = 'Success', int $statusCode = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
        
        // Game bilgisini ekle
        $response = $this->addGameToResponse($response, $data);
        
        return response()->json($response, $statusCode);
    }
}
