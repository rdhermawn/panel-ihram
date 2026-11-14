<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Carbon\Carbon;


class TopSpenderExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    ShouldAutoSize,
    WithEvents
{
    protected $users;
    protected $data;
    protected $totalMoney;

    public function __construct()
    {
        $this->users = User::pluck('name', 'id')->toArray();

        $this->data = DB::table('pwp_ipaymu')
            ->select('user_id', DB::raw('SUM(amount) as total_amount'), DB::raw('SUM(money) as total_money'))
            ->where('status', 'berhasil')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('user_id')
            ->orderByDesc('total_amount')
            ->get()
            ->values();

        $this->totalMoney = $this->data->sum('total_money');
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Ranking', 'Name', 'Amount', 'Spender'];
    }

    public function map($row): array
    {
        static $rank = 1;
        $name = $this->users[$row->user_id] ?? 'Unknown';
        return [
            $rank++,
            Str::limit($name, 3, '***'),
            $row->total_amount,
            'Rp. ' . number_format($row->total_money, 0, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            3 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 15,
            'D' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $serverName = config('pw-config.server_name', 'Unknown Server');
                $month = Carbon::now()->translatedFormat('F Y');

                $event->sheet->setCellValue('A1', "Top Spender - {$serverName}");
                $event->sheet->setCellValue('A2', "Periode: {$month}");
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Header styling
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 11],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $sheet->getStyle('A3:D3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4CAF50']],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Row styling
                $rowCount = $this->data->count();
                for ($i = 4; $i < 4 + $rowCount; $i++) {
                    $fill = ($i % 2 === 0) ? 'F9F9F9' : 'FFFFFF';
                    $sheet->getStyle("A{$i}:D{$i}")->applyFromArray([
                        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $fill]],
                        'alignment' => ['horizontal' => 'center'],
                        'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                    ]);
                    $sheet->getStyle("B{$i}")->getAlignment()->setHorizontal('left');
                }

                // Footer
                $footerRow = $rowCount + 4;
                $sheet->setCellValue("C{$footerRow}", 'Total Spender');
                $sheet->setCellValue("D{$footerRow}", 'Rp. ' . number_format($this->totalMoney, 0, ',', '.'));
                $sheet->getStyle("C{$footerRow}:D{$footerRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DDDDDD']],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);
            }
        ];
    }
}
