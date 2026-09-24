<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Property::query()
            ->where('status', 'active')
            ->with(['propertyType', 'category', 'address.city', 'address.subCity', 'primaryImage', 'owner']);

        $this->applyFilters($query, $filters);
        $this->applySort($query, $filters['sort'] ?? 'latest');

        return $query->paginate($perPage);
    }

    public function mapSearch(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $query = Property::query()
            ->where('status', 'active')
            ->with(['address', 'primaryImage'])
            ->select(['id', 'title', 'price', 'listing_type', 'slug']);

        if (isset($filters['lat'], $filters['lng'], $filters['radius'])) {
            $lat    = $filters['lat'];
            $lng    = $filters['lng'];
            $radius = $filters['radius'] ?? 10;

            $query->whereHas('address', function ($q) use ($lat, $lng, $radius) {
                $q->whereRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) < ?',
                    [$lat, $lng, $lat, $radius]
                );
            });
        }

        $this->applyFilters($query, $filters);

        return $query->limit(200)->get();
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['q'])) {
            $rawQ = trim($filters['q']);
            $terms = [$rawQ];

            // Split into individual words
            $words = array_filter(array_map('trim', preg_split('/[,\s\-\/]+/', strtolower($rawQ))));
            $stopWords = ['in', 'the', 'for', 'at', 'city', 'area', 'region', 'town', 'subcity', 'district'];
            $filteredWords = array_filter($words, fn($w) => strlen($w) >= 3 && !in_array($w, $stopWords));

            // Ethiopian town & regional spelling aliases (e.g. Assosa/Asosa, Gambella/Gambela, Hawassa/Awassa)
            $aliasLookup = [
                'gambella' => ['gambela', 'gambela city', 'baro'],
                'gambela' => ['gambella', 'gambela city', 'baro'],
                'gambellacity' => ['gambela', 'gambella', 'gambela city'],
                'gambelacity' => ['gambela', 'gambella', 'gambela city'],
                'assosa' => ['asosa', 'benishangul'],
                'assossa' => ['asosa', 'benishangul'],
                'asosa' => ['assosa', 'benishangul'],
                'benishangul' => ['asosa', 'benishangul-gumuz'],
                'hawassa' => ['awassa', 'hawasa', 'sidama'],
                'awassa' => ['hawassa', 'sidama'],
                'hawasa' => ['hawassa', 'sidama'],
                'adama' => ['nazret', 'nazreth'],
                'nazret' => ['adama'],
                'nazreth' => ['adama'],
                'bishoftu' => ['debre zeyit', 'debrezeit'],
                'debrezeit' => ['bishoftu'],
                'debre zeyit' => ['bishoftu'],
                'bahir dar' => ['bahirdar'],
                'bahirdar' => ['bahir dar'],
                'gondar' => ['gonder'],
                'gonder' => ['gondar'],
                'mekelle' => ['mekele'],
                'mekele' => ['mekelle'],
                'jigjiga' => ['jijiga'],
                'jijiga' => ['jigjiga'],
                'dire dawa' => ['diredawa'],
                'diredawa' => ['dire dawa'],
                'semera' => ['samara', 'afar'],
                'harar' => ['harrar'],
                'harrar' => ['harar'],
                'arbaminch' => ['arba minch'],
                'arba minch' => ['arbaminch'],
                'butajira' => ['gurage'],
                'bonga' => ['kaffa'],
                'kazanchis' => ['kirkos'],
                'sarbet' => ['old airport'],
                'summit' => ['cmc'],
            ];

            foreach (array_merge([$rawQ], $filteredWords) as $token) {
                $clean = strtolower(preg_replace('/[^a-z0-9]/', '', $token));
                $collapsed = preg_replace('/(.)\\1+/', '$1', $clean);

                if (strlen($token) >= 3) {
                    $terms[] = $token;
                }
                if (isset($aliasLookup[$clean])) {
                    $terms = array_merge($terms, $aliasLookup[$clean]);
                }
                if (isset($aliasLookup[$collapsed])) {
                    $terms = array_merge($terms, $aliasLookup[$collapsed]);
                }
                if (strlen($collapsed) >= 3 && $clean !== $collapsed) {
                    $terms[] = $collapsed;
                }
            }

            $terms = array_values(array_unique(array_filter($terms, fn($t) => strlen($t) >= 2)));

            $query->where(function (Builder $builder) use ($terms, $rawQ) {
                $builder->where(function ($sub) use ($rawQ) {
                    $sub->where('title', 'like', "%{$rawQ}%")
                        ->orWhere('description', 'like', "%{$rawQ}%")
                        ->orWhereHas('owner', fn($oq) => $oq->where('name', 'like', "%{$rawQ}%"))
                        ->orWhereHas('address', function ($aq) use ($rawQ) {
                            $aq->where('street', 'like', "%{$rawQ}%")
                               ->orWhereHas('city', fn($cq) => $cq->where('name', 'like', "%{$rawQ}%"))
                               ->orWhereHas('subCity', fn($scq) => $scq->where('name', 'like', "%{$rawQ}%"));
                        });
                });

                foreach ($terms as $term) {
                    if (strlen($term) >= 2) {
                        $builder->orWhere('title', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%")
                                ->orWhereHas('owner', fn($oq) => $oq->where('name', 'like', "%{$term}%"))
                                ->orWhereHas('address', function ($aq) use ($term) {
                                    $aq->where('street', 'like', "%{$term}%")
                                       ->orWhereHas('city', fn($cq) => $cq->where('name', 'like', "%{$term}%"))
                                       ->orWhereHas('subCity', fn($scq) => $scq->where('name', 'like', "%{$term}%"));
                                });
                    }
                }
            });
        }

        if (!empty($filters['type'])) {
            $type = strtolower(trim($filters['type']));
            if ($type === 'villa') {
                $query->where(function (Builder $q) {
                    $q->whereHas('propertyType', fn($pt) => $pt->where('slug', 'house'))
                      ->where(function ($sub) {
                          $sub->where('title', 'like', '%villa%')
                              ->orWhere('description', 'like', '%villa%')
                              ->orWhereHas('category', fn($c) => $c->where('name', 'like', '%villa%'));
                      });
                });
            } elseif ($type === 'condo' || $type === 'condominium') {
                $query->where(function (Builder $q) {
                    $q->whereHas('propertyType', fn($pt) => $pt->where('slug', 'apartment'))
                      ->where(function ($sub) {
                          $sub->where('title', 'like', '%condo%')
                              ->orWhere('description', 'like', '%condo%')
                              ->orWhereHas('category', fn($c) => $c->where('name', 'like', '%condo%'));
                      });
                });
            } elseif ($type === 'studio') {
                $query->where(function (Builder $q) {
                    $q->whereHas('propertyType', fn($pt) => $pt->where('slug', 'apartment'))
                      ->where(function ($sub) {
                          $sub->where('title', 'like', '%studio%')
                              ->orWhere('description', 'like', '%studio%')
                              ->orWhereHas('category', fn($c) => $c->where('name', 'like', '%studio%'));
                      });
                });
            } else {
                $query->whereHas('propertyType', fn($q) => $q->where('slug', $type));
            }
        }

        if (!empty($filters['listing_type'])) {
            $query->where('listing_type', $filters['listing_type']);
        }

        if (!empty($filters['city_id'])) {
            $query->whereHas('address', fn($q) => $q->where('city_id', $filters['city_id']));
        }

        // Dynamic Location Filter (Sub-City, Town, Neighborhood, Street, or Locality)
        $town = !empty($filters['town']) ? trim($filters['town']) : (!empty($filters['sub_city']) ? trim($filters['sub_city']) : null);
        $neighborhood = !empty($filters['neighborhood']) ? trim($filters['neighborhood']) : null;
        $subCityId = !empty($filters['sub_city_id']) ? $filters['sub_city_id'] : (!empty($filters['neighborhood_id']) ? $filters['neighborhood_id'] : null);

        // Fallback mapping for legacy static dropdown IDs (e.g. CMC => 102, Ayat => 103, Saris => 101)
        $legacyTownMap = [
            101 => 'Saris',
            102 => 'CMC',
            103 => 'Ayat',
            104 => 'Lebu',
            105 => 'Jemo',
            106 => 'Sabian',
            107 => 'Kezira',
            108 => 'Melka Jebdu',
            109 => 'Magala',
            110 => 'Legehare',
            111 => 'Dechatu',
            112 => 'Ganda Kore',
            113 => 'Shinile',
        ];

        if ($subCityId && isset($legacyTownMap[(int)$subCityId]) && empty($town)) {
            $town = $legacyTownMap[(int)$subCityId];
        }

        $subCity = ($subCityId && is_numeric($subCityId)) ? \App\Models\SubCity::find($subCityId) : null;
        $nbModel = ($subCityId && is_numeric($subCityId) && !$subCity) ? \App\Models\Neighborhood::find($subCityId) : null;

        $locName = $town ?: ($neighborhood ?: ($subCity?->name ?: ($nbModel?->name ?: null)));

        if ($subCity || ($subCityId && is_numeric($subCityId) && $subCityId < 100) || $locName) {
            $query->where(function (Builder $locationQuery) use ($subCity, $subCityId, $locName) {
                $locationQuery->whereHas('address', function (Builder $aq) use ($subCity, $subCityId, $locName) {
                    $aq->where(function (Builder $sub) use ($subCity, $subCityId, $locName) {
                        $hasClause = false;

                        if ($subCity) {
                            $sameNameIds = \App\Models\SubCity::where('name', $subCity->name)->pluck('id')->toArray();
                            $sub->whereIn('sub_city_id', $sameNameIds);
                            $hasClause = true;
                        } elseif ($subCityId && is_numeric($subCityId) && $subCityId < 100) {
                            $sub->where('sub_city_id', $subCityId)
                                ->orWhere('neighborhood_id', $subCityId);
                            $hasClause = true;
                        }

                        if ($locName) {
                            $clean = trim($locName);
                            $method = $hasClause ? 'orWhere' : 'where';
                            $sub->$method(function (Builder $locSub) use ($clean) {
                                $locSub->where('street', 'like', "%{$clean}%")
                                       ->orWhere('full_address', 'like', "%{$clean}%")
                                       ->orWhereHas('subCity', fn($sc) => $sc->where('name', 'like', "%{$clean}%"))
                                       ->orWhereHas('neighborhood', fn($nb) => $nb->where('name', 'like', "%{$clean}%"));
                            });
                        }
                    });
                });

                if ($locName) {
                    $clean = trim($locName);
                    $locationQuery->orWhere('title', 'like', "%{$clean}%")
                                  ->orWhere('description', 'like', "%{$clean}%");
                }
            });
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['bedrooms'])) {
            $bedCount = (int)$filters['bedrooms'];
            if ($bedCount >= 4) {
                $query->where('bedrooms', '>=', 4);
            } else {
                $query->where('bedrooms', $bedCount);
            }
        }

        if (!empty($filters['bathrooms'])) {
            $query->where('bathrooms', '>=', $filters['bathrooms']);
        }

        if (!empty($filters['min_area'])) {
            $query->where('area', '>=', $filters['min_area']);
        }

        if (!empty($filters['max_area'])) {
            $query->where('area', '<=', $filters['max_area']);
        }

        if (!empty($filters['furnished'])) {
            $query->where('furnished', $filters['furnished']);
        }

        if (isset($filters['parking']) && $filters['parking']) {
            $query->where('parking_spaces', '>', 0);
        }

        if (!empty($filters['amenities'])) {
            foreach ((array) $filters['amenities'] as $amenityId) {
                $query->whereHas('amenities', fn($q) => $q->where('amenities.id', $amenityId));
            }
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['agent_id'])) {
            $query->where('user_id', $filters['agent_id']);
        }

        if (!empty($filters['owner_id'])) {
            $query->where('user_id', $filters['owner_id']);
        }
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc'   => $query->orderBy('price'),
            'price_desc'  => $query->orderByDesc('price'),
            'oldest'      => $query->orderBy('published_at'),
            'popular'     => $query->orderByDesc('views_count'),
            default       => $query->orderByDesc('published_at'),
        };
    }
}
