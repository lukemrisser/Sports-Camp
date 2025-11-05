<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;

class PlayersImport implements ToCollection, WithHeadingRow
{
    use Importable;

    /** @var \Illuminate\Support\Collection */
    protected $players;

    /** @var array */
    protected $headings = [];

    /**
     * Receive the rows from the sheet.
     * We normalize and store rows in-memory so the controller can inspect them.
     * Do not persist to DB here — controller will handle creation.
     *
     * @param Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        $this->players = collect();

        // capture detected headings (if any)
        $first = $rows->first();
        if (is_array($first) || $first instanceof Collection) {
            $this->headings = array_map('strval', array_keys((array) $first));
        }

        foreach ($rows as $row) {
            $normalized = $this->normalizeRow((array) $row);
            // skip rows with no meaningful name
            if (trim($normalized['Player First Name'] . $normalized['Player Last Name']) === '') {
                continue;
            }
            $this->players->push($normalized);
        }
    }

    /**
     * Return the normalized players collection (for controller use)
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPlayers(): Collection
    {
        return $this->players ?? collect();
    }

    /**
     * Return detected headings for debugging
     *
     * @return array
     */
    public function getHeadings(): array
    {
        return $this->headings;
    }

    /**
     * Normalize a single row to expected keys: Player First Name, Player Last Name, Teammate Request
     * Falls back to positional mapping if no recognizable headings are present.
     *
     * @param array $row
     * @return array
     */
    protected function normalizeRow(array $row): array
    {
        // build a key -> value map with lowercase keys; also map underscores to spaces
        $map = [];
        foreach ($row as $k => $v) {
            $k = (string) $k;
            $nk = strtolower(trim($k));
            $map[$nk] = $v;
            $map[str_replace('_', ' ', $nk)] = $v;
        }

        $firstNameKeys = ['player first name', 'player_first_name', 'first name', 'firstname', 'camper_firstname', 'camper first name', 'first_name'];
        $lastNameKeys = ['player last name', 'player_last_name', 'last name', 'lastname', 'camper_lastname', 'camper last name', 'last_name'];
        $teammateKeys = ['teammate request', 'teammate_request', 'teammate requests', 'requests', 'teammates'];
        $birthDateKeys = ['player birth date', 'player_birth_date', 'birth date', 'birthdate', 'birth_date', 'date of birth', 'dob'];

        $get = function (array $keys) use ($map) {
            foreach ($keys as $k) {
                $lk = strtolower(trim($k));
                if (array_key_exists($lk, $map)) {
                    $val = $map[$lk];
                    return is_string($val) ? trim($val) : $val;
                }
            }
            return null;
        };

        // detect if any expected heading exists in map
        $allKeys = array_keys($map);
        $recognized = false;
        foreach (array_merge($firstNameKeys, $lastNameKeys, $teammateKeys, $birthDateKeys) as $k) {
            if (in_array(strtolower($k), $allKeys, true)) {
                $recognized = true;
                break;
            }
        }

        if (! $recognized) {
            // positional fallback: [0]=first, [1]=last, [2]=teammate requests, [3]=birth date
            $values = array_values($row);
            return [
                'Player First Name' => isset($values[0]) ? trim((string) $values[0]) : '',
                'Player Last Name' => isset($values[1]) ? trim((string) $values[1]) : '',
                'Teammate Request' => isset($values[2]) ? trim((string) $values[2]) : '',
                'Player Birth Date' => isset($values[3]) ? trim((string) $values[3]) : '',
            ];
        }

        return [
            'Player First Name' => $get($firstNameKeys) ?? '',
            'Player Last Name' => $get($lastNameKeys) ?? '',
            'Teammate Request' => $get($teammateKeys) ?? '',
            'Player Birth Date' => $get($birthDateKeys) ?? '',
        ];
    }
}