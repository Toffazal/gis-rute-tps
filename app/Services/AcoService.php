<?php

namespace App\Services;

use App\Models\MatriksJarak;
use App\Models\Website;

class AcoService
{
    // ACO Parameters
    private float $alpha = 1.0;    // Pengaruh pheromon
    private float $beta = 3.0;     // Pengaruh jarak (heuristik)
    private float $rho = 0.5;      // Tingkat evaporasi pheromon
    private float $Q = 100.0;      // Konstanta deposit pheromon
    private int $antCount = 20;    // Jumlah semut
    private int $iterations = 50;  // Jumlah iterasi

    /**
     * Optimasi urutan kunjungan TPS menggunakan Ant Colony Optimization.
     *
     * @param array $kantorCoord  [latitude, longitude] kantor (titik awal default)
     * @param array $tpaCoord     [latitude, longitude] TPA (titik akhir)
     * @param array $tpsList      Array of TPS objects (harus punya id, latitude, longitude, nama)
     * @param array|null $startNodeInfo Info node awal jika bukan kantor, e.g. ['tipe' => 'tps', 'id' => X, 'nama' => 'TPS X', 'coords' => [lat, lng]]
     * @return array ['urutan' => [...ordered TPS...], 'total_jarak' => int, 'detail_jarak' => [...]]
     */
    public function optimize(array $kantorCoord, array $tpaCoord, array $tpsList, array $startNodeInfo = null): array
    {
        if ($startNodeInfo === null) {
            $startNodeInfo = [
                'tipe' => 'kantor',
                'id' => null,
                'nama' => 'Kantor',
                'coords' => $kantorCoord
            ];
        }

        $n = count($tpsList);

        // Jika tidak ada TPS, kembalikan kosong
        if ($n === 0) {
            return ['urutan' => [], 'total_jarak' => 0, 'detail_jarak' => []];
        }

        // Jika hanya 1 TPS, tidak perlu optimasi
        if ($n === 1) {
            $tps = $tpsList[0];
            $jarakKantorTps = $this->getJarak($startNodeInfo['tipe'], $startNodeInfo['id'], 'tps', $tps->id, $startNodeInfo['coords'], [$tps->latitude, $tps->longitude]);
            $jarakTpsTpa = $this->getJarak('tps', $tps->id, 'tpa', null, [$tps->latitude, $tps->longitude], $tpaCoord);

            return [
                'urutan' => [$tps],
                'total_jarak' => $jarakKantorTps + $jarakTpsTpa,
                'detail_jarak' => [
                    ['dari' => $startNodeInfo['nama'], 'ke' => $tps->nama, 'jarak' => $jarakKantorTps],
                    ['dari' => $tps->nama, 'ke' => 'TPA', 'jarak' => $jarakTpsTpa],
                ],
            ];
        }

        // Build distance matrix
        // Index 0 = Start, 1..n = TPS, n+1 = TPA
        $totalNodes = $n + 2;
        $distMatrix = $this->buildDistanceMatrix($startNodeInfo, $tpaCoord, $tpsList);

        // Initialize pheromone matrix
        $pheromone = array_fill(0, $totalNodes, array_fill(0, $totalNodes, 1.0));

        $bestTour = null;
        $bestDistance = PHP_FLOAT_MAX;

        // ACO Iterations
        for ($iter = 0; $iter < $this->iterations; $iter++) {
            $allTours = [];
            $allDistances = [];

            // Each ant constructs a tour
            for ($ant = 0; $ant < $this->antCount; $ant++) {
                $tour = $this->constructTour($distMatrix, $pheromone, $n, $totalNodes);
                $distance = $this->calculateTourDistance($tour, $distMatrix);

                $allTours[] = $tour;
                $allDistances[] = $distance;

                if ($distance < $bestDistance) {
                    $bestDistance = $distance;
                    $bestTour = $tour;
                }
            }

            // Evaporate pheromone
            for ($i = 0; $i < $totalNodes; $i++) {
                for ($j = 0; $j < $totalNodes; $j++) {
                    $pheromone[$i][$j] *= (1 - $this->rho);
                    if ($pheromone[$i][$j] < 0.01) {
                        $pheromone[$i][$j] = 0.01;
                    }
                }
            }

            // Deposit pheromone
            for ($ant = 0; $ant < $this->antCount; $ant++) {
                $deposit = $this->Q / $allDistances[$ant];
                $tour = $allTours[$ant];
                for ($i = 0; $i < count($tour) - 1; $i++) {
                    $from = $tour[$i];
                    $to = $tour[$i + 1];
                    $pheromone[$from][$to] += $deposit;
                    $pheromone[$to][$from] += $deposit;
                }
            }
        }

        // Convert best tour indices back to TPS objects
        return $this->buildResult($bestTour, $bestDistance, $tpsList, $distMatrix, $startNodeInfo);
    }

    /**
     * Construct a single ant's tour.
     * Tour starts at node 0 (Kantor), visits all TPS (nodes 1..n), ends at node n+1 (TPA).
     */
    private function constructTour(array $distMatrix, array $pheromone, int $n, int $totalNodes): array
    {
        $tour = [0]; // Start at Kantor
        $visited = array_fill(0, $totalNodes, false);
        $visited[0] = true;
        $visited[$totalNodes - 1] = true; // TPA dikunjungi terakhir

        $current = 0;

        for ($step = 0; $step < $n; $step++) {
            $probabilities = [];
            $sum = 0.0;

            // Calculate probabilities for unvisited TPS nodes
            for ($j = 1; $j <= $n; $j++) {
                if (!$visited[$j]) {
                    $dist = $distMatrix[$current][$j];
                    if ($dist <= 0) $dist = 1; // Avoid division by zero

                    $tau = pow($pheromone[$current][$j], $this->alpha);
                    $eta = pow(1.0 / $dist, $this->beta);
                    $prob = $tau * $eta;

                    $probabilities[$j] = $prob;
                    $sum += $prob;
                }
            }

            if ($sum <= 0 || empty($probabilities)) {
                // Fallback: pick first unvisited
                for ($j = 1; $j <= $n; $j++) {
                    if (!$visited[$j]) {
                        $next = $j;
                        break;
                    }
                }
            } else {
                // Roulette wheel selection
                $rand = mt_rand() / mt_getrandmax() * $sum;
                $cumulative = 0.0;
                $next = array_key_first($probabilities);

                foreach ($probabilities as $j => $prob) {
                    $cumulative += $prob;
                    if ($cumulative >= $rand) {
                        $next = $j;
                        break;
                    }
                }
            }

            $tour[] = $next;
            $visited[$next] = true;
            $current = $next;
        }

        $tour[] = $totalNodes - 1; // End at TPA
        return $tour;
    }

    /**
     * Calculate total distance of a tour.
     */
    private function calculateTourDistance(array $tour, array $distMatrix): float
    {
        $distance = 0;
        for ($i = 0; $i < count($tour) - 1; $i++) {
            $distance += $distMatrix[$tour[$i]][$tour[$i + 1]];
        }
        return $distance;
    }

    private function buildDistanceMatrix(array $startNodeInfo, array $tpaCoord, array $tpsList): array
    {
        $n = count($tpsList);
        $totalNodes = $n + 2;
        $matrix = array_fill(0, $totalNodes, array_fill(0, $totalNodes, 0));

        // Build coordinate array for easy access
        $coords = [];
        $types = [];
        $ids = [];

        // Node 0: Start Node
        $coords[0] = $startNodeInfo['coords'];
        $types[0] = $startNodeInfo['tipe'];
        $ids[0] = $startNodeInfo['id'];

        // Nodes 1..n: TPS
        for ($i = 0; $i < $n; $i++) {
            $coords[$i + 1] = [$tpsList[$i]->latitude, $tpsList[$i]->longitude];
            $types[$i + 1] = 'tps';
            $ids[$i + 1] = $tpsList[$i]->id;
        }

        // Node n+1: TPA
        $coords[$n + 1] = $tpaCoord;
        $types[$n + 1] = 'tpa';
        $ids[$n + 1] = null;

        // Fill distance matrix
        for ($i = 0; $i < $totalNodes; $i++) {
            for ($j = $i + 1; $j < $totalNodes; $j++) {
                $dist = $this->getJarak($types[$i], $ids[$i], $types[$j], $ids[$j], $coords[$i], $coords[$j]);
                $matrix[$i][$j] = $dist;
                $matrix[$j][$i] = $dist;
            }
        }

        return $matrix;
    }

    /**
     * Get distance between two nodes.
     * Prioritize data from matriks_jarak table, fallback to Haversine.
     */
    private function getJarak(string $asalTipe, ?int $asalId, string $tujuanTipe, ?int $tujuanId, array $coordAsal, array $coordTujuan): int
    {
        // Try lookup from matriks_jarak table
        $record = MatriksJarak::where(function ($q) use ($asalTipe, $asalId, $tujuanTipe, $tujuanId) {
            $q->where('asal_tipe', $asalTipe)
              ->where('asal_id', $asalId)
              ->where('tujuan_tipe', $tujuanTipe)
              ->where('tujuan_id', $tujuanId);
        })->orWhere(function ($q) use ($asalTipe, $asalId, $tujuanTipe, $tujuanId) {
            $q->where('asal_tipe', $tujuanTipe)
              ->where('asal_id', $tujuanId)
              ->where('tujuan_tipe', $asalTipe)
              ->where('tujuan_id', $asalId);
        })->first();

        if ($record) {
            return $record->jarak_meter;
        }

        // Fallback: Haversine formula
        return $this->haversine($coordAsal[0], $coordAsal[1], $coordTujuan[0], $coordTujuan[1]);
    }

    /**
     * Calculate distance in meters using Haversine formula.
     */
    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }

    /**
     * Build the result array from best tour.
     */
    private function buildResult(array $bestTour, float $bestDistance, array $tpsList, array $distMatrix, array $startNodeInfo): array
    {
        $n = count($tpsList);
        $urutan = [];
        $detailJarak = [];

        for ($i = 0; $i < count($bestTour) - 1; $i++) {
            $fromIdx = $bestTour[$i];
            $toIdx = $bestTour[$i + 1];
            $jarak = $distMatrix[$fromIdx][$toIdx];

            if ($fromIdx === 0) {
                $fromLabel = $startNodeInfo['nama'];
            } elseif ($fromIdx === $n + 1) {
                $fromLabel = 'TPA';
            } else {
                $fromLabel = $tpsList[$fromIdx - 1]->nama;
            }

            if ($toIdx === 0) {
                $toLabel = $startNodeInfo['nama'];
            } elseif ($toIdx === $n + 1) {
                $toLabel = 'TPA';
            } else {
                $toLabel = $tpsList[$toIdx - 1]->nama;
                $urutan[] = $tpsList[$toIdx - 1];
            }

            $detailJarak[] = [
                'dari' => $fromLabel,
                'ke' => $toLabel,
                'jarak' => $jarak,
            ];
        }

        return [
            'urutan' => $urutan,
            'total_jarak' => (int) round($bestDistance),
            'detail_jarak' => $detailJarak,
        ];
    }

    /**
     * Get distance directly between two nodes.
     */
    public function getJarakDirect(array $nodeFrom, array $nodeTo): int
    {
        return $this->getJarak(
            $nodeFrom['tipe'],
            $nodeFrom['id'],
            $nodeTo['tipe'],
            $nodeTo['id'],
            $nodeFrom['coords'],
            $nodeTo['coords']
        );
    }
}
