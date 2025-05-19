<?php

use App\Models\ActivityLog;

if (!function_exists('activity_log')) {
    /**
     * Enregistrer une activité dans les journaux.
     *
     * @param string $action L'action effectuée (create, update, delete, etc.)
     * @param mixed $model Le modèle concerné par l'action
     * @param string $details Détails supplémentaires sur l'action
     * @return ActivityLog L'entrée de journal créée
     */
    function activity_log($action, $model = null, $details = null)
    {
        $user = auth()->user();
        
        if (!$user) {
            return null;
        }
        
        $data = [
            'user_id' => $user->id,
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
        
        if ($model) {
            $data['model_type'] = get_class($model);
            $data['model_id'] = $model->id;
        }
        
        return ActivityLog::create($data);
    }
}

if (!function_exists('setting')) {
    /**
     * Récupérer la valeur d'un paramètre par sa clé.
     *
     * @param string $key La clé du paramètre
     * @param mixed $default Valeur par défaut si le paramètre n'existe pas
     * @return mixed La valeur du paramètre
     */
    function setting($key, $default = null)
    {
        $setting = \App\Models\Setting::where('key', $key)->first();
        
        return $setting ? $setting->value : $default;
    }
}

if (!function_exists('format_currency')) {
    /**
     * Formater un montant en monnaie.
     *
     * @param float $amount Le montant à formater
     * @param string $currency Le code de la monnaie
     * @return string Le montant formaté
     */
    function format_currency($amount, $currency = 'DH')
    {
        return number_format($amount, 2) . ' ' . $currency;
    }
}

if (!function_exists('format_date')) {
    /**
     * Formater une date.
     *
     * @param string|Carbon\Carbon $date La date à formater
     * @param string $format Le format de date
     * @return string La date formatée
     */
    function format_date($date, $format = 'd/m/Y')
    {
        if (!$date) {
            return '';
        }
        
        if (!$date instanceof \Carbon\Carbon) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        return $date->format($format);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Formater une date et heure.
     *
     * @param string|Carbon\Carbon $date La date à formater
     * @param string $format Le format de date et heure
     * @return string La date et heure formatées
     */
    function format_datetime($date, $format = 'd/m/Y H:i')
    {
        if (!$date) {
            return '';
        }
        
        if (!$date instanceof \Carbon\Carbon) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        return $date->format($format);
    }
}