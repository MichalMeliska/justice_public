<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrinterSeeder extends Seeder
{
    private $printers = [
        [
            'name' => 'HP LaserJet MFP M477fdn',
            'cartridge' => 'CF410',
            'keywords' => ['hp', 477],
            'driverName' => 'HP Color LaserJet Pro MFP M477 PCL 6',
            'driverFile' => 'hpne512a_x64.inf'
        ],
        [
            'name' => 'HP LaserJet 1300',
            'cartridge' => 'Q2613',
            'keywords' => ['hp', 1300],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'HP LaserJet 1320',
            'cartridge' => 'Q5949',
            'keywords' => ['hp', 1320],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'HP LaserJet P2055dn',
            'cartridge' => 'CE505',
            'keywords' => ['hp', 205],
            'driverName' => 'HP Universal Printing PCL 6',
            'driverFile' => 'hpcu255u.inf'
        ],
        [
            'name' => 'HP LaserJet 700 M712',
            'cartridge' => 'CF214',
            'keywords' => ['hp', 700],
            'driverName' => 'HP LaserJet 700 M712 PCL 6',
            'driverFile' => 'hpcm712u.inf'
        ],
        [
            'name' => 'HP LaserJet M506',
            'cartridge' => 'CF287',
            'keywords' => ['hp', 506],
            'driverName' => 'HP LaserJet M506 PCL-6',
            'driverFile' => 'hpye422A4_x64.inf'
        ],
        [
            'name' => 'HP LaserJet P1606dn',
            'cartridge' => 'CE278',
            'keywords' => ['hp', 1606],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'HP LaserJet Pro M404dn',
            'cartridge' => 'CF259',
            'keywords' => ['hp', 404],
            'driverName' => 'HP LaserJet Pro M404-M405 PCL 6 (V3)',
            'driverFile' => 'hpmoC12A_x64.inf'
        ],
        [
            'name' => 'HP LaserJet P3015',
            'cartridge' => 'CE255',
            'keywords' => ['hp', 3015],
            'driverName' => 'HP Universal Printing PCL 6',
            'driverFile' => 'hpcu255u.inf'
        ],
        [
            'name' => 'Konica Minolta C253',
            'cartridge' => null,
            'keywords' => ['konica', 253],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'HP LaserJet Pro 400 M401d',
            'cartridge' => 'CF280',
            'keywords' => ['hp', 401],
            'driverName' => 'HP LaserJet 400 M401 PCL 6',
            'driverFile' => 'hpcm401u.inf'
        ],
        [
            'name' => 'HP LaserJet Pro M201dw',
            'cartridge' => 'CF283',
            'keywords' => ['hp', 201],
            'driverName' => 'HP LaserJet Pro M201-M202 PCL 6',
            'driverFile' => 'hpcm201u.inf'
        ],
        [
            'name' => 'HP LaserJet M402dn',
            'cartridge' => 'CF226',
            'keywords' => ['hp', 402],
            'driverName' => 'HP LaserJet Pro M402-M403 n-dne PCL 6',
            'driverFile' => 'hpdo602a_x64.inf'
        ],
        [
            'name' => 'HP LaserJet Pro MFP M428fdn',
            'cartridge' => 'CF259',
            'keywords' => ['hp', 428],
            'driverName' => 'HP LaserJet Pro M428f-M429f PCL 6 (V3)',
            'driverFile' => 'hpteC22A_x64.inf'
        ],
        [
            'name' => 'Konica Minolta C454e',
            'cartridge' => null,
            'keywords' => ['konica', 454],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'HP LaserJet MFP M227fdw',
            'cartridge' => 'CF230',
            'keywords' => ['hp', 227],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'HP LaserJet 2300',
            'cartridge' => 'Q2610',
            'keywords' => ['hp', 2300],
            'driverName' => 'HP Universal Printing PCL 6',
            'driverFile' => 'hpcu255u.inf'
        ],
        [
            'name' => 'Canon LBP251',
            'cartridge' => 'CRG719',
            'keywords' => ['canon', 251],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'Canon LBP5050',
            'cartridge' => null,
            'keywords' => ['canon', 5050],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'Epson AL-M300',
            'cartridge' => null,
            'keywords' => ['epson', 300],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'HP LaserJet MFP M234sdn',
            'cartridge' => '135X',
            'keywords' => ['hp', 232],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'HP LaserJet P2035',
            'cartridge' => 'CE505',
            'keywords' => ['hp', 2035],
            'driverName' => null,
            'driverFile' => null
        ],
        [
            'name' => 'HP OfficeJet Pro X476dw MFP',
            'cartridge' => null,
            'keywords' => ['hp', 476],
            'driverName' => null,
            'driverFile' => null
        ]
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->printers as &$printer) $printer['keywords'] = json_encode($printer['keywords']);

        DB::table('printers')->insert($this->printers);
    }
}
