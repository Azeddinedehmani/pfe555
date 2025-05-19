<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group'
    ];

    /**
     * Récupérer la valeur d'un paramètre par sa clé.
     *
     * @param string $key La clé du paramètre
     * @param mixed $default Valeur par défaut si le paramètre n'existe pas
     * @return mixed La valeur du paramètre
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        return $setting ? $setting->value : $default;
    }

    /**
     * Définir la valeur d'un paramètre.
     *
     * @param string $key La clé du paramètre
     * @param mixed $value La valeur à définir
     * @param string $group Le groupe du paramètre
     * @return Setting Le paramètre mis à jour ou créé
     */
    public static function setValue($key, $value, $group = 'general')
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->update([
                'value' => $value,
                'group' => $group
            ]);
        } else {
            $setting = self::create([
                'key' => $key,
                'value' => $value,
                'group' => $group
            ]);
        }
        
        return $setting;
    }

    /**
     * Récupérer tous les paramètres d'un groupe.
     *
     * @param string $group Le groupe des paramètres
     * @return \Illuminate\Database\Eloquent\Collection Les paramètres du groupe
     */
    public static function getGroup($group)
    {
        return self::where('group', $group)->get();
    }
}