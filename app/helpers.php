<?php

if (! function_exists('assetVersion')) {
    /**
     * Retourne l'URL d'un asset local (public/) avec un paramètre de version basé
     * sur la date de modification du fichier — cache-busting automatique : quand le
     * fichier change, l'URL change et le navigateur re-télécharge. Les fichiers
     * inchangés conservent leur URL (donc leur cache navigateur).
     *
     * Exemple : assetVersion('js/mon-compte.js') => "/js/mon-compte.js?v=1730740912"
     */
    function assetVersion(string $path): string
    {
        $path = '/'.ltrim($path, '/');
        $file = public_path(ltrim($path, '/'));
        $version = is_file($file) ? filemtime($file) : false;

        return $version ? $path.'?v='.$version : $path;
    }
}
