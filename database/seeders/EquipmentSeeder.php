<?php

namespace Database\Seeders;

use App\Models\Inverter;
use App\Models\Panel;
use Illuminate\Database\Seeder;

/**
 * Interni katalog opreme.
 *
 * Napomena: ne postoji jedan pouzdan/besplatan javni API koji vraća ažurne
 * maloprodajne cijene i tehničke specifikacije solarnih panela i invertora
 * za tržište BiH, pa je katalog popunjen reprezentativnim, tržišno
 * realističnim modelima i orijentacionim cijenama (u BAM, bez PDV-a).
 * Administrator ih kasnije može uređivati kroz admin panel
 * (/admin/panels, /admin/inverters) kako bi cijene bile ažurne.
 */
class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $panels = [
            ['manufacturer' => 'JA Solar', 'model' => 'JAM54S31-415/MR', 'technology' => 'monokristalni', 'power_w' => 415, 'efficiency_percent' => 21.3, 'length_mm' => 1722, 'width_mm' => 1134, 'price_bam' => 285.00, 'warranty_years' => 12],
            ['manufacturer' => 'JA Solar', 'model' => 'JAM72S30-540/MR', 'technology' => 'monokristalni', 'power_w' => 540, 'efficiency_percent' => 20.9, 'length_mm' => 2278, 'width_mm' => 1134, 'price_bam' => 365.00, 'warranty_years' => 12],
            ['manufacturer' => 'Longi', 'model' => 'Hi-MO 6 LR5-54HTH-455M', 'technology' => 'monokristalni', 'power_w' => 455, 'efficiency_percent' => 22.3, 'length_mm' => 1762, 'width_mm' => 1134, 'price_bam' => 310.00, 'warranty_years' => 15],
            ['manufacturer' => 'Longi', 'model' => 'Hi-MO 6 LR5-72HTH-580M', 'technology' => 'monokristalni', 'power_w' => 580, 'efficiency_percent' => 22.5, 'length_mm' => 2278, 'width_mm' => 1134, 'price_bam' => 395.00, 'warranty_years' => 15],
            ['manufacturer' => 'Jinko Solar', 'model' => 'Tiger Neo N-type 60HL4-475', 'technology' => 'monokristalni (N-type)', 'power_w' => 475, 'efficiency_percent' => 22.0, 'length_mm' => 1903, 'width_mm' => 1134, 'price_bam' => 335.00, 'warranty_years' => 15],
            ['manufacturer' => 'Canadian Solar', 'model' => 'HiKu6 CS6W-410MS', 'technology' => 'monokristalni', 'power_w' => 410, 'efficiency_percent' => 20.9, 'length_mm' => 1722, 'width_mm' => 1134, 'price_bam' => 270.00, 'warranty_years' => 12],
            ['manufacturer' => 'Trina Solar', 'model' => 'Vertex S+ TSM-440NEG9R.28', 'technology' => 'monokristalni (N-type)', 'power_w' => 440, 'efficiency_percent' => 22.1, 'length_mm' => 1762, 'width_mm' => 1134, 'price_bam' => 320.00, 'warranty_years' => 15],
            ['manufacturer' => 'Astronergy', 'model' => 'ASTRO N5s CHSM54N(H)-420', 'technology' => 'monokristalni (N-type)', 'power_w' => 420, 'efficiency_percent' => 21.6, 'length_mm' => 1762, 'width_mm' => 1134, 'price_bam' => 295.00, 'warranty_years' => 15],
        ];

        foreach ($panels as $panel) {
            Panel::updateOrCreate(
                ['manufacturer' => $panel['manufacturer'], 'model' => $panel['model']],
                $panel + ['is_active' => true]
            );
        }

        $inverters = [
            ['manufacturer' => 'Huawei', 'model' => 'SUN2000-3KTL-L1', 'type' => 'string', 'rated_power_kw' => 3.0, 'max_pv_power_kw' => 4.5, 'phases' => 1, 'price_bam' => 1450.00, 'warranty_years' => 10],
            ['manufacturer' => 'Huawei', 'model' => 'SUN2000-5KTL-L1', 'type' => 'string', 'rated_power_kw' => 5.0, 'max_pv_power_kw' => 7.5, 'phases' => 1, 'price_bam' => 1850.00, 'warranty_years' => 10],
            ['manufacturer' => 'Huawei', 'model' => 'SUN2000-10KTL-M1', 'type' => 'string', 'rated_power_kw' => 10.0, 'max_pv_power_kw' => 15.0, 'phases' => 3, 'price_bam' => 3200.00, 'warranty_years' => 10],
            ['manufacturer' => 'Growatt', 'model' => 'MIN 6000TL-X', 'type' => 'string', 'rated_power_kw' => 6.0, 'max_pv_power_kw' => 9.0, 'phases' => 1, 'price_bam' => 1750.00, 'warranty_years' => 10],
            ['manufacturer' => 'Growatt', 'model' => 'MOD 15KTL3-X', 'type' => 'string', 'rated_power_kw' => 15.0, 'max_pv_power_kw' => 22.5, 'phases' => 3, 'price_bam' => 4400.00, 'warranty_years' => 10],
            ['manufacturer' => 'Fronius', 'model' => 'Symo GEN24 8.0 Plus', 'type' => 'hibridni', 'rated_power_kw' => 8.0, 'max_pv_power_kw' => 12.0, 'phases' => 3, 'price_bam' => 5200.00, 'warranty_years' => 10],
            ['manufacturer' => 'SolarEdge', 'model' => 'SE10K-RWS', 'type' => 'hibridni', 'rated_power_kw' => 10.0, 'max_pv_power_kw' => 13.0, 'phases' => 3, 'price_bam' => 5600.00, 'warranty_years' => 12],
            ['manufacturer' => 'Deye', 'model' => 'SUN-20K-G03', 'type' => 'string', 'rated_power_kw' => 20.0, 'max_pv_power_kw' => 30.0, 'phases' => 3, 'price_bam' => 6100.00, 'warranty_years' => 10],
        ];

        foreach ($inverters as $inverter) {
            Inverter::updateOrCreate(
                ['manufacturer' => $inverter['manufacturer'], 'model' => $inverter['model']],
                $inverter + ['is_active' => true]
            );
        }
    }
}
