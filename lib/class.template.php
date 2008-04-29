<?php
class Template {

    private static $template_dir;
    
    public static function render($template, $vars = array()) {
        Log::debug(__CLASS__.': Attempting to render template "'.$template.'"');
        $template_file = 'tpl.'.$template.'.php';
        if(!self::$template_dir && !(self::$template_dir = Config::get('template_dir'))) {
            $included_dirs = ini_get('include_path');
            foreach(explode(':', $included_dirs) as $dir) {
                if(is_readable($dir.'/templates')) {
                    self::$template_dir = $dir.'/templates';
                    break;
                }
            }
        }
        if(is_readable(self::$template_dir.'/'.$template_file)) {
            ob_start();
            extract($vars);
            include(self::$template_dir.'/'.$template_file);
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        } else {
            Log::warning(__CLASS__.': Could not render template '.self::$template_dir.'/'.$template_file);
        }
        return '';
    }
}
?>
