<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->orders;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Order ID',
            'Player Name',
            'Parent Name',
            'Camp',
            'Sport',
            'Order Date',
            'Amount',
            'Paid',
            'Outstanding',
            'Status'
        ];
    }

    /**
     * @param mixed $order
     * @return array
     */
    public function map($order): array
    {
        return [
            $order->Order_ID,
            $order->player 
                ? $order->player->Camper_FirstName . ' ' . $order->player->Camper_LastName 
                : 'N/A',
            $order->parent 
                ? $order->parent->Parent_FirstName . ' ' . $order->parent->Parent_LastName 
                : 'N/A',
            $order->camp ? $order->camp->Camp_Name : 'N/A',
            ($order->camp && $order->camp->sport) ? $order->camp->sport->Sport_Name : 'N/A',
            $order->Order_Date ? $order->Order_Date->format('M d, Y') : 'N/A',
            '$' . number_format($order->Item_Amount ?? 0, 2),
            '$' . number_format($order->Item_Amount_Paid ?? 0, 2),
            '$' . number_format($order->remaining_amount ?? 0, 2),
            $this->getPaymentStatus($order)
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
            
            // Auto-fit columns
            'A:J' => ['alignment' => ['horizontal' => 'left']],
        ];
    }

    private function getPaymentStatus($order)
    {
        if ($order->payment_status === 'paid' || $order->isFullyPaid()) {
            return 'Paid';
        } elseif ($order->payment_status === 'partial' || $order->isPartiallyPaid()) {
            return 'Partial';
        } else {
            return 'Pending';
        }
    }
}