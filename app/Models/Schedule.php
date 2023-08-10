<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\IOFactory as Excel;

class Schedule extends Model
{
    use HasFactory;

    public static function compare($rozpis, $export)
    {
        $instance = new static;

        $rozpis = $instance->parse($rozpis);
        $export = $instance->parse($export);

        return $instance->diff($rozpis, $export);
    }

    private function parse($file)
    {
        $excel = Excel::load($file);

        $parsed = [];

        for ($i = 0; $i < $excel->getSheetCount(); $i++) {

            $xls = $excel->getSheet($i)->toArray();

            $header = $this->getHeader($xls);

            $head_i = array_column($header, 'i');
            $head_func = array_column($header, 'header');
            $clenovia_senatu = (in_array('clenovia', $head_func) or in_array('Sudca3', $head_func));

            array_walk($xls, function($row) use ($head_i, $head_func, $clenovia_senatu, &$parsed) {

                if (!is_numeric($row[0]) or is_numeric($row[1])) return;

                foreach (array_keys($row) as $i)
                    if (!in_array($i, $head_i))
                        unset($row[$i]);

                $row = array_combine($head_func, $row);

                $arr = [
                    'compare' => [
                        'senat' => $this->trimDot($row['Číslo']) . ucfirst(strtolower($this->trimDot($row['Agenda']))),
                        'sudca' => $this->sanitize($row['Sudca']),
                        'asistent' => $this->sanitize($row['Asistent']),
                        'tajomnik' => $this->sanitize($row['Tajomník']),
                        'pomer' => intval($row['Pomer'])
                    ],
                    'orig' => [
                        'cislo' => intval($this->trimDot($row['Číslo'])),
                        'agenda' => ucfirst(strtolower($this->trimDot($row['Agenda']))),
                        'sudca' => $this->sanitize($row['Sudca'], false),
                        'asistent' => $this->sanitize($row['Asistent'], false),
                        'tajomnik' => $this->sanitize($row['Tajomník'], false),
                        'pomer' => intval($row['Pomer'])
                    ]
                ];

                $merge = [];

                if ($clenovia_senatu) {

                    if (array_key_exists('clenovia', $row)) {

                        list($sudca2, $sudca3) = explode(',', $row['clenovia']);

                        $merge = [
                            'compare' => [
                                'sudca2' => $this->sanitize($sudca2),
                                'sudca3' => $this->sanitize($sudca3)
                            ],
                            'orig' => [
                                'sudca2' => $this->sanitize($sudca2, false),
                                'sudca3' => $this->sanitize($sudca3, false)
                            ]
                        ];

                    } else $merge = [
                                'compare' => [
                                    'sudca2' => $this->sanitize($row['VSU']),
                                    'sudca3' => $this->sanitize($row['Sudca3'])
                                ],
                                'orig' => [
                                    'sudca2' => $this->sanitize($row['VSU'], false),
                                    'sudca3' => $this->sanitize($row['Sudca3'], false)
                                ]
                            ];

                } else $merge = [
                    'compare' => ['vsu' => $this->sanitize($row['VSU'])],
                    'orig' => ['vsu' => $this->sanitize($row['VSU'], false)]
                ];

                $parsed[] = [
                    'compare' => array_merge($arr['compare'], $merge['compare']),
                    'orig' => array_merge($arr['orig'], $merge['orig'])
                ];

            });

        }

        return $parsed;
    }

    private function getHeader($xls)
    {
        $header = [];

        foreach ($xls as $row) {

            if (is_numeric($row[0])) break;

            foreach ($row as $i => $col) {

                $col = trim($col);

                if (str_starts_with($col, 'Číslo')) $header[] = [
                    'header' => 'Číslo',
                    'i' => $i
                ];
                elseif (in_array($col, ['Oddel.', 'Značka', 'Agenda'])) $header[] = [
                    'header' => 'Agenda',
                    'i' => $i
                ];
                elseif (
                    (
                        str_starts_with($col, 'Sudca') and
                        $i !== 0
                    ) or
                    $col === 'Predseda senátu'
                ) $header[] = [
                    'header' => 'Sudca',
                    'i' => $i
                ];
                elseif (str_starts_with($col, 'Členovia senátu')) $header[] = [
                    'header' => 'clenovia',
                    'i' => $i
                ];
                elseif ($col === 's3_meno') $header[] = [
                    'header' => 'Sudca3',
                    'i' => $i
                ];
                elseif ($col === 'Asistent') $header[] = [
                    'header' => 'Asistent',
                    'i' => $i
                ];
                elseif ($col === 'Tajomník') $header[] = [
                    'header' => 'Tajomník',
                    'i' => $i
                ];
                elseif (in_array($col, ['u_meno', 'Úradník', 'Vyšší súdny úradník'])) $header[] = [
                    'header' => 'VSU',
                    'i' => $i
                ];
                elseif ($col === 'Pomer') $header[] = [
                    'header' => 'Pomer',
                    'i' => $i
                ];

            }

        }

        usort($header, fn ($a, $b) => $a['i'] <=> $b['i']);

        return $header;
    }

    private function sanitize($str, $title = true)
    {
        $str = $this->stripFunc($str);

        if ($title) $str = User::stripTitle($str);

        return $this->trimDot($str);
    }

    private function stripFunc($name)
    {
        return preg_replace('/\([PSATU]\)|PaMÚ|-(?: |)U/', '', $name);
    }

    private function trimDot($str)
    {
        if (trim($str) === '') return null;

        return trim($str, ' ,-');
    }

    private function diff($rozpis, $export)
    {
        foreach ($export as $el) $senaty_e[$el['compare']['senat']] = $el;
        foreach ($rozpis as $el) $senaty_r[$el['compare']['senat']] = $el;

        $diff = [];

        foreach ($rozpis as $i => $row_r)
            if (!in_array($row_r['compare']['senat'], array_keys($senaty_e))) $diff[] = array_merge($row_r['orig'], ['attr' => 'missing']);
            else {

                $wrong = [];

                foreach (array_keys($row_r['compare']) as $column) {

                    $arr = explode(' ', $row_r['compare'][$column]);

                    foreach ($arr as $name) {

                        if (
                            (
                                $column === 'vsu' and
                                !array_key_exists($column, $senaty_e[$row_r['compare']['senat']]['compare']) and
                                !str_contains($senaty_e[$row_r['compare']['senat']]['compare']['sudca2'], $name)
                            ) or
                            (
                                array_key_exists($column, $senaty_e[$row_r['compare']['senat']]['compare']) and
                                !str_contains($senaty_e[$row_r['compare']['senat']]['compare'][$column], $name)
                            )

                        ) {

                            $wrong = [
                                'cislo' => $row_r['orig']['cislo'],
                                'agenda' => $row_r['orig']['agenda'],
                                $column => $row_r['orig'][$column]
                            ];

                            break 2;

                        }

                    }

                }

                if ($wrong) {

                    foreach (array_keys($row_r['compare']) as $column)
                        if (!array_key_exists($column, $wrong))
                            $wrong[$column] = null;

                    $diff[] = $wrong;

                }

        }

        foreach ($export as $row_e)
            if (!in_array($row_e['compare']['senat'], array_keys($senaty_r)) and $row_e['compare']['pomer'] !== 0)
                $diff[] = array_merge($row_e['orig'], ['attr' => 'delete']);

        return $diff;
    }
}
