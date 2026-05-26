<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WikidataService
{
    private const WIKIDATA_SPARQL_URL = 'https://query.wikidata.org/sparql';

    public function getGermanUniversities(): array
    {
        $sparqlQuery = <<<SPARQL
SELECT DISTINCT
    ?university
    ?universityLabel
    ?inception
    ?website
    ?city
    ?cityLabel
    ?coordinate
    ?students
    ?type
    ?typeLabel
    ?logo
WHERE {
    VALUES ?type {
        wd:Q3918 wd:Q875538 wd:Q1664720
        wd:Q1156895 wd:Q1364732
        wd:Q4187951 wd:Q38723
    }
    ?university wdt:P31 ?type ;
                wdt:P17 wd:Q183 .
    FILTER NOT EXISTS { ?university wdt:P576 ?dissolution . }
    OPTIONAL { ?university wdt:P571 ?inception . }
    OPTIONAL { ?university wdt:P856 ?website . }
    OPTIONAL { ?university wdt:P131 ?city . }
    OPTIONAL { ?university wdt:P625 ?coordinate . }
    OPTIONAL { ?university wdt:P2196 ?students . }
    OPTIONAL { ?university wdt:P154 ?logo . }
    SERVICE wikibase:label { bd:serviceParam wikibase:language "de,en". }
}
ORDER BY ?universityLabel
SPARQL;

        return $this->runSparql($sparqlQuery, 'universities', 120);
    }

    /**
     * For a list of city Wikidata Q-ids, find which German federal state (Q1221156)
     * each city belongs to via P131* (located in administrative territorial entity, transitive).
     *
     * Returns a map: ['Q64' => 'Q64', 'Q1731' => 'Q1731', ...] (city Q-id => state Q-id).
     */
    public function getCityStateMapping(array $cityWikidataIds): array
    {
        $cityWikidataIds = array_filter(array_unique($cityWikidataIds));
        if (empty($cityWikidataIds)) {
            return [];
        }

        $chunks = array_chunk($cityWikidataIds, 100);
        $mapping = [];

        foreach ($chunks as $chunk) {
            $values = implode(' ', array_map(fn ($qid) => "wd:{$qid}", $chunk));

            $query = <<<SPARQL
SELECT DISTINCT ?city ?state WHERE {
    VALUES ?city { {$values} }
    ?city wdt:P131* ?state .
    ?state wdt:P31 wd:Q1221156 .
}
SPARQL;

            $rows = $this->runSparql($query, 'city-state-mapping', 60);
            foreach ($rows as $row) {
                $cityQid = $this->extractWikidataId($this->extractValue($row, 'city'));
                $stateQid = $this->extractWikidataId($this->extractValue($row, 'state'));
                if ($cityQid && $stateQid) {
                    $mapping[$cityQid] = $stateQid;
                }
            }
        }

        return $mapping;
    }

    /**
     * Fetch description + Wikipedia URLs + types for a batch of universities by Q-id.
     * Returns array keyed by Q-id with: description_de, description_en, wiki_de, wiki_en, types[]
     */
    public function getUniversityEnrichment(array $qids): array
    {
        $qids = array_filter(array_unique($qids));
        if (empty($qids)) {
            return [];
        }

        $result = [];

        foreach (array_chunk($qids, 80) as $chunk) {
            $values = implode(' ', array_map(fn ($q) => "wd:{$q}", $chunk));

            // Wikipedia sitelinks (Wikidata SPARQL endpoint supports this via schema:about)
            $query = <<<SPARQL
SELECT ?university ?descDe ?descEn ?wikiDe ?wikiEn ?type WHERE {
    VALUES ?university { {$values} }
    OPTIONAL { ?university schema:description ?descDe FILTER(LANG(?descDe) = "de") }
    OPTIONAL { ?university schema:description ?descEn FILTER(LANG(?descEn) = "en") }
    OPTIONAL {
        ?wikiDe schema:about ?university ;
                schema:isPartOf <https://de.wikipedia.org/> .
    }
    OPTIONAL {
        ?wikiEn schema:about ?university ;
                schema:isPartOf <https://en.wikipedia.org/> .
    }
    OPTIONAL { ?university wdt:P31 ?type . }
}
SPARQL;

            $rows = $this->runSparql($query, 'enrichment', 120);
            foreach ($rows as $row) {
                $qid = $this->extractWikidataId($this->extractValue($row, 'university'));
                if (!$qid) {
                    continue;
                }

                if (!isset($result[$qid])) {
                    $result[$qid] = [
                        'description_de' => null,
                        'description_en' => null,
                        'wiki_de' => null,
                        'wiki_en' => null,
                        'types' => [],
                    ];
                }

                $descDe = $this->extractValue($row, 'descDe');
                $descEn = $this->extractValue($row, 'descEn');
                $wikiDe = $this->extractValue($row, 'wikiDe');
                $wikiEn = $this->extractValue($row, 'wikiEn');
                $type = $this->extractValue($row, 'type');

                if ($descDe && !$result[$qid]['description_de']) {
                    $result[$qid]['description_de'] = $descDe;
                }
                if ($descEn && !$result[$qid]['description_en']) {
                    $result[$qid]['description_en'] = $descEn;
                }
                if ($wikiDe && !$result[$qid]['wiki_de']) {
                    $result[$qid]['wiki_de'] = $wikiDe;
                }
                if ($wikiEn && !$result[$qid]['wiki_en']) {
                    $result[$qid]['wiki_en'] = $wikiEn;
                }
                if ($type) {
                    $typeQid = $this->extractWikidataId($type);
                    if ($typeQid && !in_array($typeQid, $result[$qid]['types'], true)) {
                        $result[$qid]['types'][] = $typeQid;
                    }
                }
            }
        }

        return $result;
    }

    public function getGermanStates(): array
    {
        $sparqlQuery = <<<SPARQL
SELECT DISTINCT
    ?state
    ?stateLabel
    ?capital
    ?capitalLabel
    ?population
    ?area
    ?coordinate
WHERE {
    ?state wdt:P31 wd:Q1221156 .
    OPTIONAL { ?state wdt:P36 ?capital . }
    OPTIONAL { ?state wdt:P1082 ?population . }
    OPTIONAL { ?state wdt:P2046 ?area . }
    OPTIONAL { ?state wdt:P625 ?coordinate . }
    SERVICE wikibase:label { bd:serviceParam wikibase:language "de,en". }
}
ORDER BY ?stateLabel
SPARQL;

        return $this->runSparql($sparqlQuery, 'states', 60);
    }

    private function runSparql(string $query, string $label, int $timeout): array
    {
        try {
            Log::info("Wikidata SPARQL [{$label}] starting...");

            $response = Http::withHeaders([
                'Accept' => 'application/sparql-results+json',
                'User-Agent' => 'AlmanyaUni/1.0 (https://almanyauni.com)',
            ])
                ->timeout($timeout)
                ->get(self::WIKIDATA_SPARQL_URL, [
                    'query' => $query,
                    'format' => 'json',
                ]);

            if (!$response->successful()) {
                Log::error("Wikidata SPARQL [{$label}] error", [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
                return [];
            }

            $results = $response->json()['results']['bindings'] ?? [];
            Log::info("Wikidata SPARQL [{$label}] done", ['count' => count($results)]);

            return $results;
        } catch (\Exception $e) {
            Log::error("Wikidata SPARQL [{$label}] exception: " . $e->getMessage());
            return [];
        }
    }

    public function extractValue(array $result, string $key, $default = null)
    {
        return $result[$key]['value'] ?? $default;
    }

    public function extractWikidataId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }
        if (preg_match('#/entity/(Q\d+)$#', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function parseCoordinate(?string $coordStr): array
    {
        if (!$coordStr) {
            return ['latitude' => null, 'longitude' => null];
        }
        if (preg_match('/Point\(([^\s]+)\s+([^\)]+)\)/', $coordStr, $matches)) {
            return [
                'longitude' => (float) $matches[1],
                'latitude' => (float) $matches[2],
            ];
        }
        return ['latitude' => null, 'longitude' => null];
    }

    /**
     * Wikibase label service may fall back to Q-id when no de/en label exists.
     * Treat those as "no label" so we can skip the row.
     */
    public function isUsableLabel(?string $label): bool
    {
        if ($label === null || $label === '') {
            return false;
        }
        return !preg_match('/^Q\d+$/', $label);
    }
}
