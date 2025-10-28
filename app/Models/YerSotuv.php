<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class YerSotuv extends Model
{
    protected $table = 'yer_sotuvlar';

    protected $fillable = [
        // Asosiy ma'lumotlar
        'lot_raqami',
        'tuman',
        'mfy',
        'manzil',
        'unikal_raqam',
        'zona',
        'bosh_reja_zona',
        'yangi_ozbekiston',
        'maydoni',
        'lokatsiya',

        // Qurilish ma'lumotlari
        'qurilish_turi_1',
        'qurilish_turi_2',
        'qurilish_maydoni',
        'investitsiya',

        // Auksion ma'lumotlari
        'boshlangich_narx',
        'auksion_sana',
        'sotilgan_narx',
        'auksion_golibi',
        'golib_turi',
        'golib_nomi',
        'telefon',
        'tolov_turi',
        'asos',
        'auksion_turi',
        'holat',

        // Shartnoma ma'lumotlari
        'shartnoma_holati',
        'shartnoma_sana',
        'shartnoma_raqam',

        // Moliyaviy ma'lumotlar
        'golib_tolagan',
        'buyurtmachiga_otkazilgan',
        'chegirma',
        'auksion_harajati',
        'tushadigan_mablagh',
        'davaktiv_jamgarmasi',
        'shartnoma_tushgan',
        'davaktivda_turgan',
        'yer_auksion_harajat',

        // Taqsimot - tushadigan
        'mahalliy_byudjet_tushadigan',
        'jamgarma_tushadigan',
        'yangi_oz_direksiya_tushadigan',
        'shayxontohur_tushadigan',

        'yangi_hayot_industrial_park_tushadigan',
        'ksz_direksiyalari_tushadigan',
        'toshkent_city_direksiya_tushadigan',
        'tuman_byudjeti_tushadigan',

        // Taqsimot - taqsimlangan
        'mahalliy_byudjet_taqsimlangan',
        'jamgarma_taqsimlangan',
        'yangi_oz_direksiya_taqsimlangan',
        'shayxontohur_taqsimlangan',

        'yangi_hayot_industrial_park_taqsimlangan',
        'ksz_direksiyalari_taqsimlangan',
        'toshkent_city_direksiya_taqsimlangan',
        'tuman_byudjeti_taqsimlangan',

        // Qoldiq
        'qoldiq_mahalliy_byudjet',
        'qoldiq_jamgarma',
        'qoldiq_yangi_oz_direksiya',
        'qoldiq_shayxontohur',

        'farqi',
        'shartnoma_summasi',
        'yil',
    ];

    protected $casts = [
        'maydoni' => 'decimal:4',
        'boshlangich_narx' => 'decimal:2',
        'sotilgan_narx' => 'decimal:2',
        'auksion_sana' => 'date',
        'shartnoma_sana' => 'date',
        'shartnoma_summasi' => 'decimal:2',
        'qurilish_maydoni' => 'decimal:2',
        'investitsiya' => 'decimal:2',
        'golib_tolagan' => 'decimal:2',
        'buyurtmachiga_otkazilgan' => 'decimal:2',
        'chegirma' => 'decimal:2',
        'auksion_harajati' => 'decimal:2',
        'tushadigan_mablagh' => 'decimal:2',
        'davaktiv_jamgarmasi' => 'decimal:2',
        'shartnoma_tushgan' => 'decimal:2',
        'davaktivda_turgan' => 'decimal:2',
        'yer_auksion_harajat' => 'decimal:2',
        'mahalliy_byudjet_tushadigan' => 'decimal:2',
        'jamgarma_tushadigan' => 'decimal:2',
        'yangi_oz_direksiya_tushadigan' => 'decimal:2',
        'shayxontohur_tushadigan' => 'decimal:2',
        'mahalliy_byudjet_taqsimlangan' => 'decimal:2',
        'jamgarma_taqsimlangan' => 'decimal:2',
        'yangi_oz_direksiya_taqsimlangan' => 'decimal:2',
        'shayxontohur_taqsimlangan' => 'decimal:2',
        'qoldiq_mahalliy_byudjet' => 'decimal:2',
        'qoldiq_jamgarma' => 'decimal:2',
        'qoldiq_yangi_oz_direksiya' => 'decimal:2',
        'qoldiq_shayxontohur' => 'decimal:2',
        'farqi' => 'decimal:2',
    ];

    public function grafikTolovlar(): HasMany
    {
        return $this->hasMany(GrafikTolov::class, 'lot_raqami', 'lot_raqami');
    }

    public function faktTolovlar(): HasMany
    {
        return $this->hasMany(FaktTolov::class, 'lot_raqami', 'lot_raqami');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('lot_raqami', 'like', '%' . $search . '%')
                    ->orWhere('golib_nomi', 'like', '%' . $search . '%')
                    ->orWhere('unikal_raqam', 'like', '%' . $search . '%')
                    ->orWhere('mfy', 'like', '%' . $search . '%');
            });
        });

        $query->when($filters['tuman'] ?? false, fn($query, $tuman) =>
            $query->where('tuman', $tuman)
        );

        $query->when($filters['yil'] ?? false, fn($query, $yil) =>
            $query->where('yil', $yil)
        );

        $query->when($filters['tolov_turi'] ?? false, fn($query, $tolov) =>
            $query->where('tolov_turi', $tolov)
        );

        $query->when($filters['holat'] ?? false, fn($query, $holat) =>
            $query->where('holat', $holat)
        );
    }

    // Helper methods
    public function getJamiGrafikAttribute()
    {
        return $this->grafikTolovlar()->sum('grafik_summa');
    }

    public function getJamiFaktAttribute()
    {
        return $this->faktTolovlar()->sum('tolov_summa');
    }

    public function getQarzdorlikAttribute()
    {
        return $this->jami_grafik - $this->jami_fakt;
    }

    public function getTolovFoiziAttribute()
    {
        if ($this->jami_grafik <= 0) return 0;
        return round(($this->jami_fakt / $this->jami_grafik) * 100, 1);
    }
}
