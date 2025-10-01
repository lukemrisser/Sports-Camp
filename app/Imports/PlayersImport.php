<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class PlayersImport implements ToCollection, WithHeadingRow
{
    /**
     * The imported players collection.
     */
    protected $players;

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // $rows is a collection of arrays, each representing a row from the spreadsheet
        $this->players = $rows;
    }

    /**
     * Get the imported players collection
     */
    public function getPlayers()
    {
        return $this->players ?? collect();
    }
}